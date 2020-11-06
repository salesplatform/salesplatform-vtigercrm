<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_OutgoingServerSaveAjax_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        // SalesPlatform.ru begin
        require_once 'includes/SalesPlatform/NetIDNA/idna_convert.class.php';
        // SalesPlatform.ru end
        $outgoingServerSettingsModel = Settings_Vtiger_Systems_Model::getInstanceFromServerType('email', 'OutgoingServer');
        $loadDefaultSettings = $request->get('default');
        if($loadDefaultSettings == "true") {
            $outgoingServerSettingsModel->loadDefaultValues();
        }else{
            $outgoingServerSettingsModel->setData($request->getAll());
        }
        $response = new Vtiger_Response();
        
        // SalesPlatform.ru begin
        $idn = new idna_convert();
        $server_username = $idn->encode(vtlib_purify($request->get('server')));
        $from_email_field = $idn->encode(vtlib_purify($request->get('from_email_field')));
        $request->set('server_username', $server_username);
        $request->set('from_email_field', $from_email_field);
        // SalesPlatform.ru end
        try{
            if ($loadDefaultSettings == "true") {
                $response->setResult('OK');
            } else {
                $id = $outgoingServerSettingsModel->save($request);
                $data = $outgoingServerSettingsModel->getData();
                $response->setResult($data);
            }
        }catch(Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}
