<?php


class Settings_SPVoipIntegration_SaveAjax_Action extends Vtiger_SaveAjax_Action {
    
    function checkPermission(\Vtiger_Request $request) {
        
    }

    public function process(Vtiger_Request $request) {
        $recordModel = Settings_SPVoipIntegration_Record_Model::getInstance();
        $recordModel->saveSettings($request);
        $response = new Vtiger_Response();
        $response->setResult(true);
        
        $response->emit();
    }
}