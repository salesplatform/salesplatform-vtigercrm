<?php

namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\MCNClient;
class MCNTelecomApiManager extends AbstractCallApiManager {
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();        
        $extension = $currentUser->get('sp_mcn_extension');
        $client = new MCNClient();
        $number = trim($number, " \t\n\r\0\x0B+");
        $client->makeCall($extension, $number);
    }
    
    public function getRecord($callId) {
        $client = new MCNClient();
        return $client->getRecord($callId);
    }

}
