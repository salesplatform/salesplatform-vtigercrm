<?php

namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\TelphinClient;
class TelphinApiManager extends AbstractCallApiManager{
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $phoneNumber = $currentUser->get('sp_telphin_extension');
        $client = new TelphinClient();
        $client->makeCall($phoneNumber, $number);       
    }
    
}
