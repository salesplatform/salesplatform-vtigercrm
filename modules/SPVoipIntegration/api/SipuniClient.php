<?php

namespace SPVoipIntegration\api;

class SipuniClient {

    private $_key;
    private $_api;
    const CALL_METHOD = 'callback/call_number';

    /**
     * @param $key
     * @param $api
     */
    public function __construct($key, $api) {
        $this->_key = $key;
        $this->_api = $api;
    }

    /**
     * @param array $params - Query params
     * @return mixed
     *
     */
    public function makeCall($params) {
        
        $params['secret'] = $this->_key;
        $hashString = join('+', $params);
        unset($params['secret']);
        $params['hash'] = md5($hashString);
        
        $url = $this->_api . self::CALL_METHOD;
        $query = http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
