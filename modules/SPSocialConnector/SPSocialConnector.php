<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once 'include/Zend/Json.php';
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
require_once 'modules/Vtiger/CRMEntity.php'; 

class SPSocialConnector extends Vtiger_CRMEntity {

    var $dependent_modules = array('Contacts', 'Leads','Accounts');
    
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_sp_socialconnector';
    var $table_index= 'socialconnectorid';

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_sp_socialconnectorcf', 'socialconnectorid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_sp_socialconnector', 'vtiger_sp_socialconnectorcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
            'vtiger_crmentity' => 'crmid',
            'vtiger_sp_socialconnector' => 'socialconnectorid',
            'vtiger_sp_socialconnectorcf'=>'socialconnectorid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'message' => Array('sp_socialconnector', 'message'),
        'type' => Array('sp_socialconnector', 'type'),
        'message_datetime' => Array('sp_socialconnector', 'message_datetime'),
        'Assigned To' => Array('crmentity','smownerid')
    );
    var $list_fields_name = Array (
        /* Format: Field Label => fieldname */
        'message' => 'message',
        'type' => 'type',
        'message_datetime' => 'message_datetime',
        'Assigned To' => 'assigned_user_id'
    );

    // Make the field link to detail view 
    var $list_link_field = 'message';

    // For Popup listview and UI type support
    var $search_fields = Array(
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'message' => Array('sp_socialconnector', 'message')
    );
    var $search_fields_name = Array (
            /* Format: Field Label => fieldname */
            'message' => 'message'
    );

    // For Popup window record selection
    var $popup_fields = Array ('message');

    // Allow sorting on the following (field column names)
    var $sortby_fields = Array ('message');

    // Should contain field labels
    //var $detailview_links = Array ('Message');

    // For Alphabetical search
    var $def_basicsearch_col = 'message';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'message';

    // Required Information for enabling Import feature
    var $required_fields = Array ('assigned_user_id'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'message_datetime';
    var $default_sort_order='DESC';
    
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('createdtime', 'modifiedtime', 'message');
	
    function __construct() {
        $this->column_fields = getColumnFields('SPSocialConnector');
        $this->db = new PearDatabase();
    }
    
    function save_module($module) {
    }
    

    function getListQuery($module, $where='') {
        global $current_user;
        $query = "SELECT vtiger_crmentity.*, "
                . "vtiger_sp_socialconnector.* "
                . "FROM vtiger_sp_socialconnector "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_sp_socialconnector.socialconnectorid ";
            $query .= getNonAdminAccessControlQuery($module, $current_user);
            $query .= "WHERE vtiger_crmentity.deleted = 0 ". $where;
            
        return $query;
    }

    
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {

        if($event_type == 'module.postinstall') {
            global $adb;
            // Mark the module as Additional module
            $adb->pquery('UPDATE vtiger_tab SET customized=1 WHERE name=?', array($modulename));

            // Insert into vtiger_sp_socialconnector_providers social nets
            $this->addSocialNet(array("1","Vkontakte","vk.com"));
            $this->addSocialNet(array("2","Twitter","twitter.com"));

            // Update summary field for related view
            $this->setSummaryField();

            // Adding new fields to Contacts
            $this->addNewField('Contacts', array(
                'block' => 'LBL_CONTACT_INFORMATION',
                'label' => 'Vkontakte URL',
                'name' => 'vk_url',
                'table' => 'vtiger_contactscf',
                'column' => 'vk_url'
                ));
            
            $this->addNewField('Contacts', array(
                'block' => 'LBL_CONTACT_INFORMATION',
                'label' => 'Twitter URL',
                'name' => 'tw_url',
                'table' => 'vtiger_contactscf',
                'column' => 'tw_url'
                ));

            $adb->pquery("ALTER TABLE `vtiger_contactscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;", array());
            $adb->pquery("ALTER TABLE `vtiger_contactscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;", array()); 

            // Adding new fields to Leads
            $this->addNewField('Leads', array(
                'block' => 'LBL_LEAD_INFORMATION',
                'label' => 'Vkontakte URL',
                'name' => 'vk_url',
                'table' => 'vtiger_leadscf',
                'column' => 'vk_url'
                ));
            
            $this->addNewField('Leads', array(
                'block' => 'LBL_LEAD_INFORMATION',
                'label' => 'Twitter URL',
                'name' => 'tw_url',
                'table' => 'vtiger_leadscf',
                'column' => 'tw_url'
                ));
         
            $adb->pquery("ALTER TABLE `vtiger_leadscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;", array());
            $adb->pquery("ALTER TABLE `vtiger_leadscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;", array()); 

            // Adding new fields to Accounts            
            $this->addNewField('Accounts', array(
                'block' => 'LBL_ACCOUNT_INFORMATION',
                'label' => 'Vkontakte URL',
                'name' => 'vk_url',
                'table' => 'vtiger_accountscf',
                'column' => 'vk_url'
                ));
            
            $this->addNewField('Accounts', array(
                'block' => 'LBL_ACCOUNT_INFORMATION',
                'label' => 'Twitter URL',
                'name' => 'tw_url',
                'table' => 'vtiger_accountscf',
                'column' => 'tw_url'
                ));

            $adb->pquery("ALTER TABLE `vtiger_accountscf` ADD COLUMN `vk_url` VARCHAR(155) NULL ;", array());
            $adb->pquery("ALTER TABLE `vtiger_accountscf` ADD COLUMN `tw_url` VARCHAR(155) NULL ;", array());         

            /* Initilizate settings */
            $sql = "INSERT INTO `vtiger_sp_socialconnector_settings` VALUES 
                ('vk_app_id',''),
                ('vk_app_secret',''),
                ('vk_access_token',''),
                ('tw_app_key',''),
                ('tw_app_secret','');";
            $adb->pquery($sql,array());
            
            /* Create field in module settings */
            $sql = "set @lastfieldid = (select `id` from `vtiger_settings_field_seq`);";
            $adb->pquery($sql,array());
            $sql = "set @blockid = (select `blockid` from `vtiger_settings_blocks` where `label` = 'LBL_INTEGRATION');";
            $adb->pquery($sql,array());
            $sql = "set @maxseq = (select max(`sequence`) from `vtiger_settings_field` where `blockid` = @blockid);";
            $adb->pquery($sql,array());
            $sql = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`) "
                    . " VALUES (@lastfieldid+1, @blockid, 'LBL_SOCIALCONNECTOR_SETTINGS', '', 'LBL_SOCIALCONNECTOR_SETTINGS', 'index.php?module=SPSocialConnector&view=Index&parent=Settings', @maxseq+1, 0);";
            $adb->pquery($sql,array());
            $sql = "UPDATE `vtiger_settings_field_seq` SET `id` = @lastfieldid+1;";
            $adb->pquery($sql,array());      
            
            $this->addRelation();
            
        } else if($event_type == 'module.disabled') {

            // Disabled additional fields for Contacts
            $this->setPresence('Contacts', 'vk_url', 1);
            $this->setPresence('Contacts', 'tw_url', 1);

            // Disabled additional fields for Leads
            $this->setPresence('Leads', 'vk_url', 1);
            $this->setPresence('Leads', 'tw_url', 1);

            // Disabled additional fields for Accounts
            $this->setPresence('Accounts', 'vk_url', 1);
            $this->setPresence('Accounts', 'tw_url', 1);
            
            $this->deleteRelation();
            
        } else if($event_type == 'module.enabled') {
            
            // Enabled additional fields for Contacts
            $this->setPresence('Contacts', 'vk_url', 2);
            $this->setPresence('Contacts', 'tw_url', 2);

            // Enabled additional fields for Leads
            $this->setPresence('Leads', 'vk_url', 2);
            $this->setPresence('Leads', 'tw_url', 2);

            // Enabled additional fields for Accounts
            $this->setPresence('Accounts', 'vk_url', 2);
            $this->setPresence('Accounts', 'tw_url', 2);

            $this->addRelation();
            
        } else if($event_type == 'module.preuninstall') {

        } else if($event_type == 'module.preupdate') {

        } else if($event_type == 'module.postupdate') {

        }

    }

    /**
     * Insert into vtiger_sp_socialconnector_providers social nets
     * @param $params
     */
    function addSocialNet($params) {
        global $adb;
        $sql = "insert into vtiger_sp_socialconnector_providers "
                . "(id,provider_name,provider_domen) values(?, ?, ?)";
        $adb->pquery($sql, $params);
    }
    
    /**
     * Add new field to $module
     * @param type $module
     * @param type $params
     */
    function addNewField($module, $params) {
        $moduleInstance = Vtiger_Module::getInstance($module);
        $blockInstance = Vtiger_Block::getInstance($params['block'], $moduleInstance);
        $fieldInstance = new Vtiger_Field();
        $fieldInstance->label = $params['label'];
        $fieldInstance->name = $params['name'];
        $fieldInstance->table = $params['table'];
        $fieldInstance->column = $params['column'];
        $fieldInstance->columntype = 'varchar';
        $fieldInstance->uitype = 17;
        $fieldInstance->typeofdata = 'V~O';
        $blockInstance->addField($fieldInstance);
        $fieldInstance->setRelatedModules(Array('SPSocialConnector')); 
    }
    
    /**
     * Set presence to choosen field
     * @param type $module
     * @param type $field
     * @param type $presence
     */
    function setPresence($module, $field, $presence) {
        $moduleInstance = Vtiger_Module::getInstance($module);
        $fieldInstance = Vtiger_Field::getInstance($field, $moduleInstance);
        $fieldInstance->setPresence($presence);
        $fieldInstance->save();
    }

    /**
     * Set presence to choosen field
     */
    function setSummaryField() {
        $fields = array('message_datetime', 'type');
        foreach($fields as $field) {
            $moduleInstance = Vtiger_Module::getInstance('SPSocialConnector');
            $fieldInstance = Vtiger_Field::getInstance($field, $moduleInstance);
            $fieldInstance->setSummaryField(1);
            $fieldInstance->save();
        }
    }
    
    /**
     * Delete relations with $this->dependent_modules
     */
    function deleteRelation() {
        foreach ($this->dependent_modules as $module) {
            $socialconnectorModuleInstance = Vtiger_Module::getInstance('SPSocialConnector');
            $moduleInstance = Vtiger_Module::getInstance($module);
            $moduleInstance->deleteLink('DETAILVIEWBASIC', 'Send to social nets');
            $moduleInstance->deleteLink('DETAILVIEWBASIC', 'Get from social nets');
            $moduleInstance->unsetRelatedlist($socialconnectorModuleInstance,'SPSocialConnector','get_related_list');
        }

    }

    /**
     * Add relations with $this->dependent_modules
     */
    function addRelation() {
        foreach ($this->dependent_modules as $module) {
            $socialconnectorModuleInstance = Vtiger_Module::getInstance('SPSocialConnector');
            $moduleInstance = Vtiger_Module::getInstance($module);
            $moduleInstance->addLink("DETAILVIEWBASIC", "Send to social nets", "javascript:SPSocialConnector_Detail_Js.triggerSendMessage('index.php?module=\$MODULE\$&view=MassActionAjax&mode=showSendMessageForm');");
            $moduleInstance->addLink("DETAILVIEWBASIC", "Get from social nets", "javascript:SPSocialConnector_Detail_Js.triggerGetMessage('source_module=\$MODULE\$&source_record=\$RECORD\$');");
            $moduleInstance->setRelatedlist($socialconnectorModuleInstance,'SPSocialConnector',array(),'get_related_list');       
        }        
    }

    /**
     * Save message info in database and linking with other modules (Leads, Contacts, Accounts)
     * @param type $message
     * @param type $urlfieldList
     * @param type $provider
     * @param type $status
     * @param bool|type $ownerid
     * @param bool|type $linktoids
     * @param bool|type $linktoModule
     * @global type $current_user
     */
    static function saveOutboundMsg($message, $urlfieldList, $provider, $status, $ownerid = false, $linktoids = false, $linktoModule = false) {
        global $adb, $current_user;

        if($ownerid === false) {
            if(isset($current_user) && !empty($current_user)) {
                    $ownerid = $current_user->id;
            } else {
                    $ownerid = 1;
            }
        }

        $moduleName = 'SPSocialConnector';
        $focus = CRMEntity::getInstance($moduleName);

        $focus->column_fields['message'] = $message;
        $focus->column_fields['assigned_user_id'] = $ownerid;
        $focus->column_fields['type'] = 'Outbound';

        $date_var = date("Y-m-d H:i:s");
        $focus->column_fields['message_datetime'] = $adb->formatDate($date_var, true);

        for($i = 0; $i < count($urlfieldList); $i++){
            if($status[$i] !== -1) {
                switch ($provider[$i]->domen) {
                    case 'twitter.com':
                        $focus->column_fields['tw_status'] = 'Sent';
                        $focus->column_fields['tw_message_id'] = $status[$i];
                        break;
                    case 'vk.com':
                        $focus->column_fields['vk_status'] = 'Sent';
                        $focus->column_fields['vk_message_id'] = $status[$i];
                        break;
                    default:
                        break;
                }
            } else {
                switch ($provider[$i]->domen) {
                    case 'twitter.com':
                        $focus->column_fields['tw_status'] = 'Not sent';
                        break;
                    case 'vk.com':
                        $focus->column_fields['vk_status'] = 'Not sent';
                        $focus->column_fields['vk_message_id'] = $status[$i];
                        break;
                    default:
                        break;
                }             
            }
        }
        $focus->save($moduleName);

        relateEntities($focus, $moduleName, $focus->id, $linktoModule, $linktoids);

    }

    /**
     * Save messages from Vkontakte
     * @param $source_record
     * @param $source_module
     * @param $messages
     */
    static function saveVkMsg($source_record, $source_module, $messages) {
        global $current_user;

        $moduleName = 'SPSocialConnector';
        $focus = CRMEntity::getInstance($moduleName);
        foreach($messages as $message) {
            $focus->column_fields['message'] = $message->body;
            $focus->column_fields['assigned_user_id'] = $current_user->id;
            $focus->column_fields['vk_message_id'] = $message->id;

            $date = new DateTime(date('c', $message->date));
            $focus->column_fields['message_datetime'] = $date->format('Y-m-d H:i:s');

            if($message->out == 0) {
                $focus->column_fields['vk_status'] = 'Received';
                $focus->column_fields['type'] = 'Incoming';
            } else {
                $focus->column_fields['vk_status'] = 'Sent';
                $focus->column_fields['type'] = 'Outbound';
            }

            $focus->save($moduleName);
            relateEntities($focus, $moduleName, $focus->id, $source_module, $source_record);
        }

    }
}
    
?>
