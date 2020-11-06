<?php
namespace SPVoipIntegration\apiManagers;

class MegafonApiManager extends GravitelApiManager{
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $megafonUserId = $currentUser->get('sp_megafon_id');
        if(empty($megafonUserId)) {
            throw new \Exception('No Megafon id in profile');
        }
        
        $response = $this->sendRequest(
            \Settings_SPVoipIntegration_Record_Model::getMegafonAPIUrl(), 
            $number, 
            $megafonUserId, 
            \Settings_SPVoipIntegration_Record_Model::getMegafonToken()
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
