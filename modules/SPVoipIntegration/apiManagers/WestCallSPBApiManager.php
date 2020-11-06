<?php

namespace SPVoipIntegration\apiManagers;

class WestCallSPBApiManager extends GravitelApiManager{
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $westcallSpbId = $currentUser->get('sp_westcall_spb_id');
        if(empty($westcallSpbId)) {
            throw new \Exception('No WestCallSPB id in profile');
        }
        
        $response = $this->sendRequest(
            \Settings_SPVoipIntegration_Record_Model::getWestCallSPBAPIUrl(), 
            $number, 
            $westcallSpbId, 
            \Settings_SPVoipIntegration_Record_Model::getWestCallSPBToken()
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