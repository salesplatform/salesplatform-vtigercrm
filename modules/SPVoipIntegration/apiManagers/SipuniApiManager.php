<?php

namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\SipuniClient;

class SipuniApiManager extends AbstractCallApiManager {

    const REVERSE = '0';
    const ANTIAON = '0';

    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $params = array(
            'antiaon' => self::ANTIAON,
            'phone' => (String) $number,
            'reverse' => self::REVERSE,
            'sipnumber' => $currentUser->get('sp_sipuni_extension'),
            'user' => \Settings_SPVoipIntegration_Record_Model::getSipuniId()              
        );
        $answer = (new SipuniClient(
                \Settings_SPVoipIntegration_Record_Model::getSipuniKey(), \Settings_SPVoipIntegration_Record_Model::getSipuniAPIUrl()
                ))->makeCall($params);
        $answerObject = json_decode($answer);
        if ($answerObject->success !== true) {
            throw new Exception("Not success");
        }
    }
}
