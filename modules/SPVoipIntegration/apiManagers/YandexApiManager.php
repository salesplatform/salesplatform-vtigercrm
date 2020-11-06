<?php
namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\YandexClient;
class YandexApiManager extends AbstractCallApiManager{
    
    public function doOutgoingCall($number) {
        $client = new YandexClient();
        $fromNumber = \Settings_SPVoipIntegration_Record_Model::getYandexUserOutgoing();
        $client->makeCall($fromNumber, $number);
    }
    
    public function getRecordLink($callId) {
        $client = new YandexClient();
        return $client->getRecord($callId);
    }

}
