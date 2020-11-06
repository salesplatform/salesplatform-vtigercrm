<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_DeleteStatus_Action extends Settings_Vtiger_Index_Action {
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $recordId = $request->get('record');
        if(!empty($recordId)) {
            $record  =  Settings_SPCMLConnector_Record_Model::getInstance($recordId);
            $record->delete();
        } else {
            $response->setError(NULL, 'not record id');
        }
        $response->emit();
    }
}