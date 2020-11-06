<?php

namespace SPVoipIntegration\api;

class MCNClient {
    
    private $authToken;
    private $apiUrl;
    private $accountId;    
    private $pbxId;
    
    public function __construct() {
        $this->authToken = \Settings_SPVoipIntegration_Record_Model::getMCNApiToken();
        $this->apiUrl = \Settings_SPVoipIntegration_Record_Model::getMCNApiUrl();
        $this->accountId = \Settings_SPVoipIntegration_Record_Model::getMCNAccountId();
        $this->pbxId = \Settings_SPVoipIntegration_Record_Model::getMCNPBXId();
        
        if (empty($this->authToken)) {
            throw new \Exception('Empty mcn auth token');
        }
        
        if (empty($this->authToken)) {
            throw new \Exception('Empty mcn api url');
        }
        
        if (empty($this->accountId)) {
            throw new \Exeception('Empty mcn account id');
        }
        
        if (empty($this->pbxId)) {
            throw new \Exception('Empty pbx id');
        }
    }
    
    public function makeCall($from, $to) {
        $method = 'account/' . $this->accountId . '/vpbx/' . $this->pbxId . '/outbound_call';
        $params = array(
            'from'  => $from,
            'to'    => $to
        );
        $res = $this->call($method, $params, 'POST');
        $responseObj = json_decode($res, true);
        
        if (!isset($responseObj['status']) || $responseObj['status'] != 'success') {
            $errorMsg = $responseObj['errors']['description'];
            throw new \Exception($errorMsg);
        }
    }
    
    public function getRecord($callId) {
        $method = 'account/' . $this->accountId . '/vpbx/' . $this->pbxId . '/call_log/record';
        $params = array(
            'call_id' => $callId
        );

        return $this->call($method, $params);
    }        
    
    private function call($method, $params = array(), $requestType = 'GET') {
        $ch = curl_init();
        $fullURL =  $this->apiUrl . $method;
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
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = 'Accept: application/json';
        $headers[] = "Authorization: Bearer {$this->authToken}";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
                
        return $response;
    }
    
    private function httpBuildQuery($params = array()) {
        return http_build_query($params, null, '&', PHP_QUERY_RFC1738);
    }
    
}
