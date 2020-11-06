<?php

namespace SPVoipIntegration\apiManagers;

class DomruApiManager extends GravitelApiManager{
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $domruUserId = $currentUser->get('sp_domru_id');
        if(empty($domruUserId)) {
            throw new \Exception('No Domru id in profile');
        }
        
        $response = $this->sendRequest(
            \Settings_SPVoipIntegration_Record_Model::getDomruAPIUrl(), 
            $number, 
            $domruUserId, 
            \Settings_SPVoipIntegration_Record_Model::getDomruToken()
        );
        
        if(empty($response)) {
            throw new \Exception('No communication with provider');
        }
        
        $decodedResponse = json_decode($response);
        if($decodedResponse != null && !empty($decodedResponse->error)) {
            throw new \Exception('Invalid provider parameters');
        }
    }
}