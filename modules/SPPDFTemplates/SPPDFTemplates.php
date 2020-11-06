<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates {
    
     function vtlib_handler($modulename, $event_type) {
        
        if($event_type == 'module.postinstall') {
            
        } else if($event_type == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
        } else if($event_type == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
	} else if($event_type == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
	} else if($event_type == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
	} else if($event_type == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
	}
     }
     
     /**
      * For widget display of last records without fails.
      * @param type $module
      * @param type $user
      * @param type $scope
      * @return null
      */
     function getNonAdminAccessControlQuery($module, $user, $scope='') {
         return null;
     }
}