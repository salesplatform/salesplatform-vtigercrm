<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\api;

class RostelecomClient {
    
    public function __construct() {
        $this->apiURL = \Settings_SPVoipIntegration_Record_Model::getRostelecomApiURL();
        $this->sipURI = \Settings_SPVoipIntegration_Record_Model::getRostelecomSipURI();
    }

    public function makeCall($fromNumber, $toNumber) {
        $methodName = '/call_back';
        $fullURL = $this->apiURL . $methodName;
        
        $rawBody = array(
            "request_number" => $toNumber,
            "from_sipuri" => $this->sipURI
        );
        $json = json_encode($rawBody);
        $rostelecomAnswer = $this->sendRequest($fullURL, $json, true);
        if ($rostelecomAnswer['resultMessage'] != 'ok') {
            throw new \Exception('Unsuccessful operation. Error: ' . $rostelecomAnswer['resultMessage']);
        }
    }
    
    public function getRecord($sessionId) {
        $audioContent = null;
        
        $methodName = '/get_record';
        $fullURL = $this->apiURL . $methodName;
        
        $rawBody = array("session_id" => $sessionId);
        $json = json_encode($rawBody);
        $rostelecomAnswer = $this->sendRequest($fullURL, $json, true);
        $audioRecordURL = $rostelecomAnswer['url'];
        if (!empty($audioRecordURL)) {
            $audioContent = $this->sendRequest($audioRecordURL, $json);
        }
        
        return $audioContent;
    }
    
        private function sendRequest($url, $json=false, $decodeJson=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($json) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders($json));
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        if ($decodeJson) {
            $response = json_decode($response, true);
        }
        return $response;
    }
    
    private function getHeaders($json) {
        //need sign request before send
        $signedRequest = \Settings_SPVoipIntegration_Record_Model::signRostelecomData($json, true);
        
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Client-ID: " . \Settings_SPVoipIntegration_Record_Model::getRostelecomIdentificationKey();
        $headers[] = "X-Client-Sign: " . $signedRequest;
        
        return $headers;
    }
}

