<?php
namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\ZadarmaClient;

class ZadarmaApiManager extends AbstractCallApiManager {        
    
    private $callUrl = '/v1/request/callback/';
    private $recordUrl = '/v1/pbx/record/request/';
    public function doOutgoingCall($number) {
        
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $params = array(
            'from' => $currentUser->get('sp_zadarma_extension'),
            'to' => $number,
            'sip' => $currentUser->get('sp_zadarma_extension')
        );
        $zd = new ZadarmaClient(
                \Settings_SPVoipIntegration_Record_Model::getZadarmaKey(), 
                \Settings_SPVoipIntegration_Record_Model::getZadarmaSecret()
                );
        $answer = $zd->call($this->callUrl, $params);
        $answerObject = json_decode($answer);
        if ($answerObject->status != 'success') {            
            throw new \Exception($answerObject->message);
        }
    }
    
    public function getRecordLink($callId) {
        $params = array(
            'call_id' => $callId
        );
        $zd = new ZadarmaClient(
                \Settings_SPVoipIntegration_Record_Model::getZadarmaKey(), 
                \Settings_SPVoipIntegration_Record_Model::getZadarmaSecret()
                );
        $answer = $zd->call($this->recordUrl, $params);
        
        $answerObject = json_decode($answer);
        if ($answerObject->status != 'success') {
            throw new \Exception($answerObject->message);
        }
        return $answerObject->link;
    }

}