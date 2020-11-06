<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
if (!defined('VTIGER_UPGRADE'))
    die('Invalid entry point');
chdir(dirname(__FILE__) . '/../../../');
include_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
include_once 'include/utils/utils.php';

vimport('~~modules/com_vtiger_workflow/include.inc');
vimport('~~modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
vimport('~~modules/com_vtiger_workflow/VTEntityMethodManager.inc');
vimport('~~include/Webservices/Utils.php');
vimport('~~modules/Users/Users.php');

//Start add new currency - 'CFP Franc or Pacific Franc' 
global $adb;

$query = 'UPDATE vtiger_currencies_seq SET id = (SELECT currencyid FROM vtiger_currencies ORDER BY currencyid DESC LIMIT 1)';
$adb->pquery($query, array());

$uniqId = $adb->getUniqueID('vtiger_currencies'); 
$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?',array('CFP Franc')); 
 
if($adb->num_rows($result) <= 0){ 
    Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies VALUES (?,?,?,?)', array($uniqId, 'CFP Franc', 'XPF', 'F')); 
} 

//Adding new timezone (GMT+11:00) New Caledonia 
$sortOrderResult = $adb->pquery("SELECT sortorderid FROM vtiger_time_zone WHERE time_zone = ?", array('Asia/Yakutsk'));
if ($adb->num_rows($sortOrderResult)) {
    $sortOrderId = $adb->query_result($sortOrderResult, 0, 'sortorderid');
    $adb->pquery("UPDATE vtiger_time_zone SET sortorderid = (sortorderid + 1) WHERE sortorderid > ?", array($sortOrderId));
    Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_time_zone (time_zone, sortorderid, presence) VALUES (?, ?, ?)', array('Etc/GMT-11', ($sortOrderId + 1), 1));
    echo "New timezone (GMT+11:00) New Caledonia added.<br>";
}


//SalesPlatform.ru begin

// Begin Update summary fields for PBXManager
$moduleName = 'PBXManager';
$updateQuery = 'UPDATE vtiger_field SET summaryfield = 1 WHERE fieldname = ? AND tabid = '. getTabid($moduleName);
Migration_Index_View::ExecuteQuery($updateQuery, array('customernumber'));
$updateQuery = 'UPDATE vtiger_field SET summaryfield = 0 WHERE fieldname = ? AND tabid = '. getTabid($moduleName);
Migration_Index_View::ExecuteQuery($updateQuery, array('customer'));
$updateQuery = 'UPDATE vtiger_field SET summaryfield = 1 WHERE fieldname = ? AND tabid = '. getTabid($moduleName);
Migration_Index_View::ExecuteQuery($updateQuery, array('totalduration'));
$updateQuery = 'UPDATE vtiger_field SET summaryfield = 0 WHERE fieldname = ? AND tabid = '. getTabid($moduleName);
Migration_Index_View::ExecuteQuery($updateQuery, array('incominglinename'));
// End

// Begin Multiple organizations for new modules

/* Default field values */
$columnname = "spcompany";
$uitype = 16;
$fieldname = "spcompany";
$fieldlabel = "Self Company";

/* --------------------------- Act module --------------------------- */
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_sp_act ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
/* Get module tab id */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'Act'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');
/* Get block id */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_ACT_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');
/* Update field seq */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');
/* Count new block id */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;
/* Create field */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_sp_act", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_sp_act", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

/* --------------------------- Consignment module --------------------------- */
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_sp_consignment ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
/* Get module tab id */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'Consignment'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');
/* Get block id */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_CONSIGNMENT_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');
/* Update field seq */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');
/* Count new block id */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;
/* Create field */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_sp_consignment", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_sp_consignment", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

/* --------------------------- Potentials module --------------------------- */
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_potential ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
/* Get module tab id */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'Potentials'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');
/* Get block id */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_OPPORTUNITY_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');
/* Update field seq */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');
/* Count new block id */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;
/* Create field */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_potential", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_potential", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

/* --------------------------- PurchaseOrder module --------------------------- */
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_purchaseorder ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
/* Get module tab id */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'PurchaseOrder'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');
/* Get block id */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_PO_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');
/* Update field seq */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');
/* Count new block id */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;
/* Create field */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_purchaseorder", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_purchaseorder", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

/* --------------------------- SPPayments module --------------------------- */
Migration_Index_View::ExecuteQuery("ALTER TABLE sp_payments ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
/* Get module tab id */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'SPPayments'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');
/* Get block id */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_PAYMENT_DETAILS' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');
/* Update field seq */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');
/* Count new block id */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;
/* Create field */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "sp_payments", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "sp_payments", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());
// End

// Begin Update summary fields for PBXManager
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_systems ADD COLUMN use_mail_account varchar(5) DEFAULT NULL", array());
// End

// Begin Set custom view for standard All filter
$cvInstance = CustomView_Record_Model::getAllFilterByModule('Act');
$res = $adb->pquery('select * from vtiger_cvcolumnlist where cvid = ? and columnindex = ?', array($cvInstance->getId(), 0));
if($adb->num_rows($res) == 0) {
    $sql = "insert into vtiger_cvcolumnlist (cvid, columnindex, columnname) values (?,?,?)";
    $params = array($cvInstance->getId(), 0, 'vtiger_sp_act:act_no:act_no:Act_Act_No:V');
    $adb->pquery($sql, $params);
}

$cvInstance = CustomView_Record_Model::getAllFilterByModule('Consignment');
$res = $adb->pquery('select * from vtiger_cvcolumnlist where cvid = ? and columnindex = ?', array($cvInstance->getId(), 0));
if($adb->num_rows($res) == 0) {
    $sql = "insert into vtiger_cvcolumnlist (cvid, columnindex, columnname) values (?,?,?)";
    $params = array($cvInstance->getId(), 0, 'vtiger_sp_consignment:consignment_no:consignment_no:Consignment_Consignment_No:V');
    $adb->pquery($sql, $params);
}
// End

// Begin Update Invoice links
if(defined('VTIGER_UPGRADE')) {
    $updateQuery = 'UPDATE vtiger_links SET linkurl = ? WHERE linklabel = ? AND tabid = ' . getTabid('Invoice');
    Migration_Index_View::ExecuteQuery($updateQuery, array('index.php?module=Act&view=Edit&sourceModule=$MODULE$&sourceRecord=$RECORD$&invoice_id=$RECORD$&relationOperation=true', 'LBL_INVOICE_ADD_ACT'));
}
// End

// Begin Folder for Reports Templates
$sql = "INSERT INTO vtiger_reportfolder (FOLDERNAME,DESCRIPTION,STATE) VALUES(?,?,?)";
$params = array('Templates', 'Report templates for modules', 'SAVED');
Migration_Index_View::ExecuteQuery($sql, $params);
// End

// Begin Add created by fields
$entityModulesModels = Vtiger_Module_Model::getEntityModules();
$modules = array();
if($entityModulesModels){
    foreach($entityModulesModels as $model){
        $modules[] =  $model->getName();
    }
}
foreach($modules as $module){
    $moduleInstance = Vtiger_Module::getInstance($module);
    if($moduleInstance){
        $result = Migration_Index_View::ExecuteQuery("select blocklabel from vtiger_blocks where tabid=? and sequence = ?", array($moduleInstance->id, 1));
        $block = $adb->query_result($result,0,'blocklabel');
        if($block){
            $blockInstance = Vtiger_Block::getInstance($block, $moduleInstance);
            $field = new Vtiger_Field();
            $field->name = 'created_user_id';
            $field->label = 'Created By';
            $field->table = 'vtiger_crmentity';
            $field->column = 'smcreatorid';
            $field->uitype = 53;
            $field->typeofdata = 'V~O';
            $field->displaytype= 2;
            $field->quickcreate = 3;
            $field->masseditable = 0;
            $blockInstance->addField($field);
            echo "Creator field added for $module";
            echo '<br>';
        }
    }else{
        echo "Unable to find $module instance";
        echo '<br>';
    }
}
// End

//Begin fix SPCMLConnector service field for mobile apps
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_account CHANGE COLUMN `1c_id` `one_s_id` VARCHAR(255) NULL DEFAULT NULL", array());
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_pricebook CHANGE COLUMN `1c_id` `one_s_id` VARCHAR(255) NULL DEFAULT NULL", array());
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_products CHANGE COLUMN `1c_id` `one_s_id` VARCHAR(255) NULL DEFAULT NULL", array());
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_salesorder CHANGE COLUMN `1c_id` `one_s_id` VARCHAR(255) NULL DEFAULT NULL", array());
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_service CHANGE COLUMN `1c_id` `one_s_id` VARCHAR(255) NULL DEFAULT NULL", array());

Migration_Index_View::ExecuteQuery(
    "UPDATE vtiger_field SET columnname='one_s_id', fieldname='one_s_id', fieldlabel='1C ID' WHERE " .
    "tablename IN('vtiger_account','vtiger_pricebook','vtiger_products','vtiger_salesorder','vtiger_service') AND fieldname='1c_id'", array());
//End


/* Duplicates workflows for Calendar and Events */

/* Delete broken worflows and tasks */
$brokenWorkflowsResult = $adb->pquery(
    "SELECT workflow_id FROM com_vtiger_workflows WHERE (module_name=? AND summary IN(?,?)) OR (module_name=? AND summary IN(?,?))",
    array(
        'Calendar', 
        'Автоматические обработчики для задач Календаря при выбранной опции Отправить уведомление', 
        'Workflow for Calendar Todos when Send Notification is True',
        'Events', 
        'Автоматические обработчики для событий при выбранной опции Отправить уведомление',
        'Workflow for Events when Send Notification is True'
    )
);

while($brokenWorkflowRow = $adb->fetchByAssoc($brokenWorkflowsResult)) {
    $adb->pquery('DELETE FROM com_vtiger_workflows WHERE workflow_id=?', array($brokenWorkflowRow['workflow_id']));
    $adb->pquery('DELETE FROM com_vtiger_workflowtasks WHERE workflow_id=?', array($brokenWorkflowRow['workflow_id']));
}

/* Recreate normal */
$workflowManager = new VTWorkflowManager($adb);
$taskManager = new VTTaskManager($adb);

$eventsWorkflow = $workflowManager->newWorkFlow("Events");
$eventsWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
$eventsWorkflow->description = vtranslate('LBL_WORKFLOW_FOR_ACTIVITY', 'Install');
$eventsWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$eventsWorkflow->defaultworkflow = 1;
$workflowManager->save($eventsWorkflow);

$task = $taskManager->createTask('VTEmailTask', $eventsWorkflow->id);
$task->active = true;
$task->summary = vtranslate('LBL_SEND_NOTIFICATION_TO_INVITED_USERS', 'Install');
$task->recepient = "\$(assigned_user_id : (Users) email1)";
$task->subject = vtranslate('LBL_EVENT', 'Install')." :  \$subject";
$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
                .'<b>'.vtranslate("LBL_EVENT_DETAILS", 'Install').':</b><br/>'
                .vtranslate("LBL_EVENT_NAME", 'Install').'         : $subject<br/>'
                .vtranslate("LBL_START_DATETIME", 'Install').'  : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
                .vtranslate("LBL_END_DATETIME", 'Install').'    : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
                .vtranslate("LBL_STATUS", 'Install').'             : $eventstatus <br/>'
                .vtranslate("LBL_PRIORITY", 'Install').'           : $taskpriority <br/>'
                .vtranslate("LBL_RELATED_WITH", 'Install').'       : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
                .'$(parent_id            : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
                .vtranslate("LBL_CONTACTS", 'Install').'           : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
                .vtranslate("LBL_LOCATION", 'Install').'           : $location <br/>'
                .vtranslate("LBL_DESCRIPTION", 'Install').'        : $description';
$taskManager->saveTask($task);

/* Calendar workflow when Send Notification is checked */
$calendarWorkflow = $workflowManager->newWorkFlow("Calendar");
$calendarWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
$calendarWorkflow->description = vtranslate('LBL_WORKFLOW_FOR_TASK', 'Install');
$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$calendarWorkflow->defaultworkflow = 1;
$workflowManager->save($calendarWorkflow);

$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
$task->active = true;
$task->summary = vtranslate('LBL_SEND_EMAIL_TO_ASSIGNED_USER', 'Install');
$task->recepient = "\$(assigned_user_id : (Users) email1)";
$task->subject = vtranslate('LBL_TASK', 'Install')." :  \$subject";
$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
            .'<b>'.vtranslate("LBL_TASK_DETAILS", 'Install').':</b><br/>'
            .vtranslate("LBL_TASK_NAME", 'Install').'         : $subject<br/>'
            .vtranslate("LBL_START_DATETIME", 'Install').' : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
            .vtranslate("LBL_END_DATETIME", 'Install').'   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
            .vtranslate("LBL_STATUS", 'Install').'            : $taskstatus <br/>'
            .vtranslate("LBL_PRIORITY", 'Install').'          : $taskpriority <br/>'
            .vtranslate("LBL_RELATED_WITH", 'Install').'      : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
            .'$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
            .vtranslate("LBL_CONTACTS", 'Install').'          : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
            .vtranslate("LBL_LOCATION", 'Install').'          : $location <br/>'
            .vtranslate("LBL_DESCRIPTION", 'Install').'       : $description';
$taskManager->saveTask($task);

// Begin Fix Webforms tables
if(defined('VTIGER_UPGRADE')) {
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms COLLATE utf8_unicode_ci', array());
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms_field COLLATE utf8_unicode_ci', array());
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms_field DROP FOREIGN KEY fk_3_vtiger_webforms_field', array());
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms_field ADD CONSTRAINT fk_2_vtiger_webforms_field FOREIGN KEY (fieldname) REFERENCES vtiger_field (fieldname) ON DELETE CASCADE', array());
}
// End

//SalesPlatform.ru end