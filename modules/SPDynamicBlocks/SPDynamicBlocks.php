<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPDynamicBlocks {

	function vtlib_handler($modulename, $event_type) {
		require_once('include/utils/utils.php');			
		if($event_type == 'module.postinstall') {
			$this->addResources();
            $this->updateSeqTable();     
            $registerLink = true;
		} 
		else if($event_type == 'module.disabled') {
			$this->removeResources();
		} 
		else if($event_type == 'module.enabled') {
			$this->addResources();
		} 
		else if($event_type == 'module.preuninstall') {
			$this->removeResources();
		} 
		else if($event_type == 'module.preupdate') {
		
		} 
		else if($event_type == 'module.postupdate') {
				
		}
        $db = PearDatabase::getInstance();
        $displayLabel = 'DynamicBlocks';
        if ($registerLink) {            
            $fieldid = $db->query_result( 
                    $db->pquery("SELECT fieldid FROM vtiger_settings_field WHERE name=?",array($displayLabel)), 0, 'fieldid');
            if (!$fieldid) {
                $blockid = $db->query_result( 
                        $db->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()), 0, 'blockid');
                $sequence = (int)$db->query_result(
                        $db->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)), 0, 'sequence') + 1;
                $fieldid = $db->getUniqueId('vtiger_settings_field');
                $db->pquery("INSERT INTO vtiger_settings_field (fieldid, blockid, sequence, name, iconpath, linkto)
                        VALUES (?,?,?,?,?,?)", array($fieldid, $blockid, $sequence, $displayLabel, '', 
                            'index.php?module=SPDynamicBlocks&parent=Settings&view=List'));
            }
        }
        
	}
    
    private function updateSeqTable() {
        $db = PearDatabase::getInstance();
        $result = $db->query("SELECT 1 FROM vtiger_sp_blocks_configuration_seq");
        if ($db->num_rows($result) > 0) {
            $db->query("UPDATE vtiger_sp_blocks_configuration_seq SET id=0");
        } else {
            $db->query("INSERT INTO vtiger_sp_blocks_configuration_seq (id) VALUES (0)");
        }
        
    }
    
    private function addResources() {
        Vtiger_Link::addLink(0, 'HEADERSCRIPT', 'SPDynamicBlocks', 'modules/SPDynamicBlocks/resources/SPDynamicBlocks.js');
    }
    
    private function removeResources() {
        Vtiger_Link::deleteLink(0, 'HEADERSCRIPT', 'SPDynamicBlocks', 'modules/SPDynamicBlocks/resources/SPDynamicBlocks.js');
    }
}