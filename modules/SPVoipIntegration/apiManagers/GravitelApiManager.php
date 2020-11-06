<?php
namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;

class GravitelApiManager extends AbstractCallApiManager {
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $gravitelUserId = $currentUser->get('sp_gravitel_id');
        if(empty($gravitelUserId)) {
            throw new \Exception('No Gravitel id in profile');
        }
        
        $response = $this->sendRequest(
            \Settings_SPVoipIntegration_Record_Model::getGravitelAPIUrl(), 
            $number, 
            $gravitelUserId, 
            \Settings_SPVoipIntegration_Record_Model::getGravitelToken()
        );
        
        if(empty($response)) {
            throw new \Exception('No communication with provider');
        }
        
        $decodedResponse = json_decode($response);
        if($decodedResponse != null && !empty($decodedResponse->error)) {
            throw new \Exception('Invalid provider parameters');
        }
    }
    
    protected function sendRequest($apiUrl, $number, $user, $token) {
        $options = array(
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                'cmd' => 'makeCall',
                'phone' => $number,
                'user' => $user,
                'token' => $token
            )
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $this->_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        return $response;
    }
    
}
