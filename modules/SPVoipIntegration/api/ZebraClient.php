<?php
namespace SPVoipIntegration\api;

class ZebraClient {
    
    private $url;
    private $authToken;
    private $accountId;
    
    public function __construct() {
        $this->url = \Settings_SPVoipIntegration_Record_Model::getZebraApiUrl();
        $this->authenticate();
    }
    
    public function registerWebhook($params){
        $method = 'accounts/' . $this->accountId . '/webhooks';
        return $this->call($method, $params, 'PUT');        
    }
    
    public function getWebhooks() {
        $method = 'accounts/' . $this->accountId . '/webhooks';
        return $this->call($method)['data'];
    }
    
    public function deleteWebhook($webhookId) {
        $method = 'accounts/' . $this->accountId . '/webhooks/' . $webhookId;
        return $this->call($method, array(), 'DELETE');
    }
    
    public function makeCall($from, $to) {
        $method = 'accounts/' . $this->accountId . '/devices/' . $from . '/quickcall/' . $to;
        return $this->call($method);
    }
    
    private function call($method, $params = array(), $requestType = 'GET') {
        if (empty($this->authToken)) {
            $this->authenticate();
        }
        $ch = curl_init();
        $fullURL =  $this->url . $method;

        curl_setopt($ch, CURLOPT_URL, $fullURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        
        if ($requestType != 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Auth-Token: {$this->authToken}";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        $responseArr = json_decode($response, true);
        $errorCode = $responseArr['error_code'];
        if (!empty($errorCode)) {
            throw new \Exception($responseArr['error_message']);
        }
        
        return $responseArr;
    }
    
    private function authenticate() {
        $ch = curl_init();
        $params = array( 'data' => array(
            'login' => \Settings_SPVoipIntegration_Record_Model::getZebraLogin(),
            'password' => \Settings_SPVoipIntegration_Record_Model::getZebraPassword(),
            'realm' => \Settings_SPVoipIntegration_Record_Model::getZebraRealm()
        ));
        curl_setopt($ch, CURLOPT_URL, $this->url . "user_auth");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        $responseArr = json_decode($response, true);
        $errorCode = $responseArr['error_code'];
        if (!empty($errorCode)) {
            throw new \Exception($responseArr['error_message']);
        }
        $this->authToken = $responseArr['data']['auth_token'];
        $this->accountId = $responseArr['data']['account_id'];
        return $response;
    }     
}
