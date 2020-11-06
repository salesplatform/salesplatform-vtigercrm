<?php
namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\UIScomClient;

class UIScomApiManager extends AbstractCallApiManager {
    
    private $callMethod = 'start.employee_call';
    private $jsonrpc = '2.0';
    private $firstCall = 'employee'; //employee/contact
    const REQUEST_ID = 123;//only for statistic
    
    /**
     * 
     * @param type $number 
     * @throws Exception
     */     
    public function doOutgoingCall($number) {
        $accessToken = \Settings_SPVoipIntegration_Record_Model::getUIScomSecret();
        $currentUser = \Users_Record_Model::getCurrentUserModel();  
        $employeeId = (int) $currentUser->get('sp_uiscom_id');
        $uiscomExtension = (String) $currentUser->get('sp_uiscom_extension');
        $employee = array(
            'id' => $employeeId
        );
        $params = array(
            'access_token' => $accessToken,
            'virtual_phone_number' => $uiscomExtension,
            'contact' => (String) $number,
            'first_call' => $this->firstCall, 
            'employee' => $employee
        );
        $UIScomRequest = array(
            jsonrpc => $this->jsonrpc,
            method => $this->callMethod,
            id => self::REQUEST_ID,
            params => $params
        );

        $UISClient = new UIScomClient($UIScomRequest);
        $answer = $UISClient->call($UIScomRequest);
        $answerObject = json_decode($answer);
        if ($answerObject->error != NULL) {
            throw new Exception("Error code: ". $answerObject->error->code);
        }
    }
}