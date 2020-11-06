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

class SPTips {
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
        global $adb;
        
        //adding new fields into linked tables
        if($event_type == 'module.postinstall') {
            
            /* Create field in module settings */
            $sql = "set @lastfieldid = (select `id` from `vtiger_settings_field_seq`);";
            $adb->query($sql);
            $sql = "set @blockid = (select `blockid` from `vtiger_settings_blocks` where `label` = 'LBL_INTEGRATION');";
            $adb->query($sql);
            $sql = "set @maxseq = (select max(`sequence`) from `vtiger_settings_field` where `blockid` = @blockid);";
            $adb->query($sql);
            $sql = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`) "
                    . " VALUES (@lastfieldid+1, @blockid, 'LBL_TIPS', NULL, NULL, 'index.php?module=SPTips&view=Index&parent=Settings', @maxseq+1, 0);";
            $adb->query($sql);
            $sql = "UPDATE `vtiger_settings_field_seq` SET `id` = @lastfieldid+1;";
            $adb->query($sql);
            
            $sql = "INSERT INTO sp_tips_providers (provider_name, settings) VALUES ('DaData', '{\"api_key\":\"\"}')";
            $adb->query($sql);
            $sql = "INSERT INTO sp_tips_providers (provider_name, settings) VALUES ('Google', '{\"api_key\":\"\"}')";
            $adb->query($sql);
            
            
            $this->addResources();
            
        } else if($event_type == 'module.disabled') {
             $this->removeResources();
        } else if($event_type == 'module.enabled') {
            $this->addResources();
        } else if($event_type == 'module.preuninstall') {
            $this->removeResources();
        } else if($event_type == 'module.preupdate') {
        } else if($event_type == 'module.postupdate') {
        }
        
    }

    private function addResources() {
        Vtiger_Link::addLink(0, 'HEADERSCRIPT', 'SPTips', 'modules/SPTips/resources/SPTips.js');
    }
    
    private function removeResources() {
        Vtiger_Link::deleteLink(0, 'HEADERSCRIPT', 'SPTips', 'modules/SPTips/resources/SPTips.js');
    }
    
}

?>
