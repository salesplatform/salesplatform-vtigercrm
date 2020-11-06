<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPSocialConnector_SaveAjax_Action extends Vtiger_SaveAjax_Action {

    // To save Mapping of user from mapping popup
    public function process(Vtiger_Request $request) {
        
        $recordModel = Settings_SPSocialConnector_Record_Model::getCleanInstance();
        
        $model = new Settings_SPSocialConnector_Module_Model;
        foreach ($model->getSettingsParameters() as $field => $type) {
            $recordModel->set($field, $request->get($field));
        }
     
        $response = new Vtiger_Response();
        try {
            $recordModel->save();
            $response->setResult(true);
        } catch (Exception $e) {
            $response->setError($e->getMessage());
        }
        $response->emit();
    }
}
