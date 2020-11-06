<?php
namespace SPVoipIntegration\api;
class MangoClient {

    private $_key;
    private $_secret;
    private $_api;

    /**
     * @param $key
     * @param $secret
     * @param $api
     */
    public function __construct($key, $secret, $api) {
        $this->_key = $key;
        $this->_secret = $secret;
        $this->_api = $api;
    }

    /**
     * @param array $params - Query params
     * @param $methodPath
     * @return mixed
     *
     */
    public function makeRequest($params, $methodPath) {
        $json = json_encode($params);
        $sign = hash('sha256', $this->_key . $json . $this->_secret);
        $postdata = array(
            'vpbx_api_key' => $this->_key,
            'sign' => $sign,
            'json' => $json
        );
        $api = $this->_api . $methodPath;
        $post = http_build_query($postdata);
        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }  
}
