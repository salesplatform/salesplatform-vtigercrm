<?php
namespace SPVoipIntegration\api;

class UIScomClient {
    const EMPLOYEE = 'employee/';
    
    private $_url;
    private $_key;
    private $_secret;
    private $_httpCode;
    private $_limits = array();
    /**
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret) {
        $this->_url = \Settings_SPVoipIntegration_Record_Model::getUIScomApiUrl();
        $this->_key = $key;
        $this->_secret = $secret;
    }
    
    /**
     * 
     * @param type $UIScomRequest
     * @return type
     * @throws Exception
     */
    public function call($UIScomRequest) {
        if (!is_array($UIScomRequest)) {
            throw new Exception('Null request.');
        }
        $requestType = 'POST';
        $data = json_encode($UIScomRequest);
        $options = array(
            CURLOPT_URL            => $this->_url,
            CURLOPT_CUSTOMREQUEST  => $requestType,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
           // CURLOPT_HEADERFUNCTION => array($this, '_parseHeaders')
        );
        $ch = curl_init();
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $this->_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($error) {
            throw new Exception($error);
        }
        return $response; // return call_session_id
    } 
    /**
     * @return int
     */
    public function getHttpCode() {
        return $this->_httpCode;
    }
    
    /**
     * @return array
     */
    public function getLimits() {
        return $this->_limits;
    }
}