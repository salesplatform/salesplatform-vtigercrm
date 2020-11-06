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
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class SPCMLConnector {
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
        global $adb;
        
        //adding new fields into linked tables
        if($event_type == 'module.postinstall') {
            $module  = Vtiger_Module_Model::getInstance('Accounts'); 
            if(!$this->isFieldExists('one_s_id', $module)) {
                $blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $module);

                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = '1C ID';
                $fieldInstance->name = 'one_s_id';
                $fieldInstance->table = 'vtiger_account';
                $fieldInstance->column = 'one_s_id';
                $fieldInstance->columntype = 'varchar';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            

            $sql = "ALTER TABLE `vtiger_account` ADD COLUMN `one_s_id` VARCHAR(255) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);


            /*********************************************************************************/

            $module  = Vtiger_Module_Model::getInstance('PriceBooks'); 
            if(!$this->isFieldExists('one_s_id', $module)) {
                $blockInstance = Vtiger_Block::getInstance('LBL_PRICEBOOK_INFORMATION', $module);

                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = '1C ID';
                $fieldInstance->name = 'one_s_id';
                $fieldInstance->table = 'vtiger_pricebook';
                $fieldInstance->column = 'one_s_id';
                $fieldInstance->columntype = 'varchar';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            

            $sql = "ALTER TABLE `vtiger_pricebook` ADD COLUMN `one_s_id` VARCHAR(255) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);

            /*********************************************************************************/

            $module  = Vtiger_Module_Model::getInstance('Products'); 
            if(!$this->isFieldExists('one_s_id', $module)) {
                $blockInstance = Vtiger_Block::getInstance('LBL_PRODUCT_INFORMATION', $module);

                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = '1C ID';
                $fieldInstance->name = 'one_s_id';
                $fieldInstance->table = 'vtiger_products';
                $fieldInstance->column = 'one_s_id';
                $fieldInstance->columntype = 'varchar';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            

            $sql = "ALTER TABLE `vtiger_products` ADD COLUMN `one_s_id` VARCHAR(255) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);

            /*********************************************************************************/
            
            $module  = Vtiger_Module_Model::getInstance('SalesOrder'); 
            if(!$this->isFieldExists('one_s_id', $module)) {
                $blockInstance = Vtiger_Block::getInstance('LBL_SO_INFORMATION', $module);

                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = '1C ID';
                $fieldInstance->name = 'one_s_id';
                $fieldInstance->table = 'vtiger_salesorder';
                $fieldInstance->column = 'one_s_id';
                $fieldInstance->columntype = 'varchar';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            
            $sql = "ALTER TABLE `vtiger_salesorder` ADD COLUMN `one_s_id` VARCHAR(255) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);
            
            
            /*********************************************************************************/
            if(!$this->isFieldExists('fromsite', $module)) {
                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = 'fromsite';
                $fieldInstance->name = 'fromsite';
                $fieldInstance->table = 'vtiger_salesorder';
                $fieldInstance->column = 'fromsite';
                $fieldInstance->columntype = 'int';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            
            $sql = "ALTER TABLE `vtiger_salesorder` ADD COLUMN `fromsite` int(20) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);

            /*********************************************************************************/

            $module  = Vtiger_Module_Model::getInstance('Services'); 
            if(!$this->isFieldExists('one_s_id', $module)) {
                $blockInstance = Vtiger_Block::getInstance('LBL_SERVICE_INFORMATION', $module);

                $fieldInstance = new Vtiger_Field();
                $fieldInstance->label = '1C ID';
                $fieldInstance->name = 'one_s_id';
                $fieldInstance->table = 'vtiger_service';
                $fieldInstance->column = 'one_s_id';
                $fieldInstance->columntype = 'varchar';
                $fieldInstance->uitype = 1;
                $fieldInstance->displaytype = 3;
                $fieldInstance->typeofdata = 'V~O';
                $blockInstance->addField($fieldInstance);
            }
            
            $sql = "ALTER TABLE `vtiger_service` ADD COLUMN `one_s_id` VARCHAR(255) NULL ;";
            $params = array();
            $adb->pquery($sql, $params);
            
            /* Initilizate cml_settings */
            $sql = "INSERT INTO `sp_cml_site_settings` VALUES 
                    (1,'siteParam','adminLogin','admin'),
                    (2,'siteParam','adminPassword','admin'),
                    (3,'siteParam','siteUrl','http://localhost/1c_exchange.php'),
                    (4,'siteParam','assignedUser','admin'),
                    (5,'statusParam','Created','Принят');";
            $adb->pquery($sql,array());
            
            /* Create field in module settings */
            $sql = "set @lastfieldid = (select `id` from `vtiger_settings_field_seq`);";
            $adb->pquery($sql,array());
            $sql = "set @blockid = (select `blockid` from `vtiger_settings_blocks` where `label` = 'LBL_INTEGRATION');";
            $adb->pquery($sql,array());
            $sql = "set @maxseq = (select max(`sequence`) from `vtiger_settings_field` where `blockid` = @blockid);";
            $adb->pquery($sql,array());
            $sql = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`) "
                    . " VALUES (@lastfieldid+1, @blockid, 'LBL_CML_SETTINGS', 'cml_settings.png', 'LBL_CML_SETTINGS_DESCRIPTION', 'index.php?module=SPCMLConnector&view=Index&parent=Settings', @maxseq+1, 0);";
            $adb->pquery($sql,array());
            $sql = "UPDATE `vtiger_settings_field_seq` SET `id` = @lastfieldid+1;";
            $adb->pquery($sql,array());
            
        } else if($event_type == 'module.disabled') {
            $this->disableModule();
        } else if($event_type == 'module.enabled') {
            $this->enableModule();
        } else if($event_type == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
        
    }
    
    /**
     * Disable one_s_id field on module disable
     */
    private function disableModule() {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE vtiger_field SET presence=1 WHERE fieldname='one_s_id' AND "
                 . "tablename IN('vtiger_account','vtiger_pricebook','vtiger_products','vtiger_salesorder','vtiger_service')");
        
        $db->query("UPDATE vtiger_cron_task SET status=0 WHERE module='SPCMLConnector'");
    }
    
    /**
     * Enable one_s_id field on module enable
     */
    private function enableModule() {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE vtiger_field SET presence=2 WHERE fieldname='one_s_id' AND "
                 . "tablename IN('vtiger_account','vtiger_pricebook','vtiger_products','vtiger_salesorder','vtiger_service')");
    }
    
    /**
     * 
     * @param string $fieldName
     * @param Vtiger_Module_Model $moduleModel
     */
    private function isFieldExists($fieldName, $moduleModel) {
        $fieldModel = $moduleModel->getField($fieldName);
        
        return $fieldModel !== false;
    }
}

?>
