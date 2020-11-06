<?php

namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\MangoClient;

class MangoApiManager extends AbstractCallApiManager {

    const SUCCESS_CODE = 1000;
    const CALL = '/commands/callback';
    const RECORD = '/queries/recording/post/';

    public function doOutgoingCall($number) {

        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $commandId = hash('sha256', $number . date('Y-m-d H:i:s'));
        $params = array(
            'command_id' => $commandId,
            'from' => array('extension' => $currentUser->get('sp_mango_extension'), 'number' => $currentUser->get('sp_mango_extension')),
            'to_number' => $number
        );
        $answer = (new MangoClient(
                \Settings_SPVoipIntegration_Record_Model::getMangoKey(), \Settings_SPVoipIntegration_Record_Model::getMangoSecret(), \Settings_SPVoipIntegration_Record_Model::getMangoAPIUrl()
                ))->makeRequest($params, self::CALL);
        $answerObject = json_decode($answer);
        if ($answerObject->result != self::SUCCESS_CODE) {
            throw new Exception("Not success");
        }
    }

    public function recieveRecord($recordId) {
        $params = array(
            'recording_id' => $recordId,
            'action' => 'download'
        );
        $answer = (new MangoClient(
                \Settings_SPVoipIntegration_Record_Model::getMangoKey(), \Settings_SPVoipIntegration_Record_Model::getMangoSecret(), \Settings_SPVoipIntegration_Record_Model::getMangoAPIUrl()
                ))->makeRequest($params, self::RECORD);
        $answerObject = json_decode($answer);
        if ($answerObject->result != self::SUCCESS_CODE) {
            throw new Exception("Not success");
        }
        return $answerObject->location;
    }
}
