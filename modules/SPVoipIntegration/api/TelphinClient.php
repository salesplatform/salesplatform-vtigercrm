<?php

namespace SPVoipIntegration\api;

class TelphinClient {

    private $apiUrl = 'api/ver1.0/';
    private $authUrl = 'oauth/token';
    private $authToken = null;

    public function makeCall($fromNumber, $toNumber) {
        $extensionId = $this->getExtensionId($fromNumber);
        if (!empty($extensionId)) {
            $params = array(
                'dst_num' => $toNumber,
                'src_num' => array($fromNumber)
            );
            $this->call('extension/' . $extensionId . '/callback/', $params, 'POST');
        } else {
            throw new \Exception('Empty extension id');
        }
    }
    
    public function getRecord($extensionName, $recordUUID) {        
        $extensionId = $this->getExtensionId($extensionName);
        $method = 'extension/' . $extensionId . '/record/' . $recordUUID;
        $res = $this->call($method);
        return $res;
    }
    
    private function call($method, $params = array(), $requestType = 'GET') {
        if (empty($this->authToken)) {
            $this->authenticate();
        }
        $ch = curl_init();
        $fullURL =  \Settings_SPVoipIntegration_Record_Model::getTelphinAPIUrl() . $this->apiUrl . $method;
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
        return $response;
    }
    
    private function getExtensionId($number) {
        $params = array(
            'name' => $number,
            'page' => 1
        );
        $res = $this->call('client/@me/extension/',$params, 'GET');
        $result = json_decode($res, true);
        return $result[0]['id'];
    }

    private function authenticate() {
        $ch = curl_init();
        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => \Settings_SPVoipIntegration_Record_Model::getTelphinAppId(),
            'client_secret' => \Settings_SPVoipIntegration_Record_Model::getTelphinAppSecret()
        );
        curl_setopt($ch, CURLOPT_URL, \Settings_SPVoipIntegration_Record_Model::getTelphinAPIUrl() . $this->authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->httpBuildQuery($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        $responseArr = json_decode($response, true);
        $this->authToken = $responseArr['access_token'];

        return $response;
    }

    private function httpBuildQuery($params = array()) {
        return http_build_query($params, null, '&', PHP_QUERY_RFC1738);
    }

}
