<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPKladr {
     
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) { 
            if($eventType == 'module.postinstall') {
                $this->addResources();
            } else if($eventType == 'module.disabled') {
                $this->removeResources();
            } else if($eventType == 'module.enabled') {
                $this->addResources();
            } else if($eventType == 'module.preuninstall') {
                $this->removeResources();
            } else if($eventType == 'module.preupdate') {
           
            } else if($eventType == 'module.postupdate') {
            
            }
 	}
    
    private function addResources() {
        Vtiger_Link::addLink(0, 'HEADERSCRIPT', 'Kladr', 'modules/SPKladr/resources/SPKladr.js');
    }
    
    private function removeResources() {
        Vtiger_Link::deleteLink(0, 'HEADERSCRIPT', 'Kladr', 'modules/SPKladr/resources/SPKladr.js');
    }
}
?>
