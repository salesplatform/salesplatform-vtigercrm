<?php
namespace SPVoipIntegration\api;


class YandexClient {
    
    private $apiUrl = 'api/v2/';
    private $authToken = null;

    public function makeCall($fromNumber, $toNumber) {        
        $params = array(
            'to' => $toNumber,
            'from' => $fromNumber
        );
        $this->call('calls/makecall', $params, 'POST');
    }

    public function getRecord($callId) {        
        $res = $this->call('calls/' . $callId, array(), 'GET');
        return $res['data']['callRecord']['uri'];
    }
    
    private function call($method, $params = array(), $requestType = 'GET') {
        if (empty($this->authToken)) {
            $this->authenticate();
        }
        $ch = curl_init();
        $fullURL =  \Settings_SPVoipIntegration_Record_Model::getYandexApiURL() . $this->apiUrl . $method;
        if (!empty($params) && $requestType != 'POST') {
            $fullURL .= "?" . $this->httpBuildQuery($params);
        }
        curl_setopt($ch, CURLOPT_URL, $fullURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        
        if ($requestType == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer {$this->authToken}";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        $responseArr = json_decode($response, true);
        if (!isset($responseArr['isSuccess']) || !$responseArr['isSuccess']) {
            throw new \Exception('Call failed');
        }
        
        return $responseArr;
    }
    
    private function authenticate() {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $ch = curl_init();
        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => \Settings_SPVoipIntegration_Record_Model::getYandexApiKey(),
            'client_secret' => $currentUser->get('sp_yandex_extension'),
        );
        
        curl_setopt($ch, CURLOPT_URL, \Settings_SPVoipIntegration_Record_Model::getYandexApiURL() . $this->apiUrl . 'auth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->httpBuildQuery($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $responseArr = json_decode($response, true);
        $error = curl_error($ch);
        curl_close($ch);
        $this->authToken = $responseArr['access_token'];
        if (empty($this->authToken)) {
            throw new \Exception('Login error');
        }
        
        if ($error) {
            throw new Exception($error);                
        }
        return $response;
    }

    private function httpBuildQuery($params = array()) {
        return http_build_query($params, null, '&', PHP_QUERY_RFC1738);
    }
}
