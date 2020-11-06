<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

chdir(dirname(__FILE__) . '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vimport('includes.http.Request');


class SPAgiCallback {
    
    function process($request){
        $pbxmanagerController = new PBXManager_PBXManager_Controller();
        $connector = $pbxmanagerController->getConnector();

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        if($this->validateRequest($connector->getVtigerSecretKey(), $request)) {
            $crmUserExtension = $this->getCrmUserExtension($request->get('callerNumber'));
            $response->setResult(array('crmUserExtension' => $crmUserExtension));
        } else {
            $response->setError(400, 'Invalid request params');
        }
        
        $response->emit();
    }
    
    /**
     * Validates callback request params
     * 
     * @param string $vtigersecretkey
     * @param Vtiger_Request $request
     * @return boolean
     */
    private function validateRequest($vtigersecretkey, $request) {
        return ($vtigersecretkey == $request->get('vtigersignature') && $request->get('callerNumber') != null);
    }
    
    /**
     * Returns crm user extension assigned to caller number or null if not assigned user for caller number
     * 
     * @param string $callerNumber
     * @return string
     */
    private function getCrmUserExtension($callerNumber) {
        $crmUserExtension = null;
        $callerUserInfo = PBXManager_Record_Model::lookUpRelatedWithNumber($callerNumber);
        if($callerUserInfo) {
            $callerRecordModel = Vtiger_Record_Model::getInstanceById($callerUserInfo['id']);
            $assignedUser = Users_Record_Model::getInstanceById($callerRecordModel->get('assigned_user_id'), "Users");
            $crmUserExtension = $assignedUser->get('phone_crm_extension');
        }
        
        return $crmUserExtension;
    }
}


$agiCallback = new SPAgiCallback();
$agiCallback->process(new Vtiger_Request($_REQUEST));