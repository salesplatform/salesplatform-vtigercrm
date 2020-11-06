<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SelectProvider_Action extends Settings_Vtiger_Index_Action {
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $record = $request->get('record');
        if ($record) {
            $providerModel = Settings_SPTips_Provider_Model::getInstanceById($record);
            if($providerModel !== null) {
                $providerModel->set('is_default', 1);
                $providerModel->save();
                $response->setResult(['success' => true]);
            } else {
                $response->setError('JS_UNSUCCESSFULL'); 
            }
        } else {
            $response->setError('JS_NO_RECORD_IN_REQUEST');
        }
        
        $response->emit();
    }
}