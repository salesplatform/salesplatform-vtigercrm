<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_SaveStatus_Action extends Settings_Vtiger_Index_Action {
    public function process (Vtiger_Request $request) {
        $recordId = $request->get('record');
        
        if($recordId == NULL) {
            $statusRecord = new Settings_SPCMLConnector_Record_Model();
        } else {
            $statusRecord = Settings_SPCMLConnector_Record_Model::getInstance($recordId);
        }
        
        /* Set options and save it */
        $statusRecord->set('key', $request->get('crmStatus'));
        $statusRecord->set('value', $request->get('siteStatus'));
        $statusRecord->save();
        
        /* Send response */
        $response = new Vtiger_Response();
        $response->emit();
    }
}