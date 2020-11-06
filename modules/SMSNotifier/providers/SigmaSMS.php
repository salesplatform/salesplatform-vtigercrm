<?php
/*+**********************************************************************************
 * PINstudio #Binizik
 ************************************************************************************/
include_once 'vtlib/Vtiger/Net/Client.php';

class SMSNotifier_SigmaSMS_Provider implements SMSNotifier_ISMSProvider_Model {

    private $_username;
    private $_password;
    private $_parameters = array();
    private static $MSGS = [
        'PROCESSING' => 'Передано на отправку'
    ];
    
    private $_errorsMsg = array(
            1 => 'ошибка авторизации',
            2 => 'недостаточно денежных средств',
            3 => 'запрос отвергнут провайдером',
            4 => 'неверный запрос',
            5 => 'неверный тип запроса',
            6 => 'неверные параметры сообщения',
            7 => 'запрос с неизвестного IP адреса',
            8 => 'сообщение не найдено в БД',
            9 => 'неверный адрес отправителя',
            10 => 'неверный текст сообщения',
            11 => 'неверный параметр validity_period',
            13 => 'превышено максимальное количество номеров за один запрос',
            14 => 'неверный тип группы',
            15 => 'ошибка сохранения в БД',
            16 => 'неверный формат даты',
            17 => 'неверный формат даты и времени',
            99 => 'внутренняя ошибка системы'
        );

    const SENDER_PARAM = 'Отправитель';
    const HOST = 'https://online.sigmasms.ru/';

    private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM);

    function __construct() {
    }

    public function getName() {
        return 'SigmaSMS';
    }

    public function setAuthParameters($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
    }

    public function setParameter($key, $value) {
        $this->_parameters[$key] = $value;
    }

    public function getParameter($key, $defvalue = false)  {
        if (isset($this->_parameters[$key])) {
            return $this->_parameters[$key];
        }
        return $defvalue;
    }

    public function getRequiredParams() {
        return self::$REQUIRED_PARAMETERS;
    }

    public function getServiceURL($type = false) {
        return false;
    }

    public function query($messageId)
    {
        $result = [
            'needlookup' => 0
        ];
        if (empty($messageId)){
            $result['error'] = true;
            $result['statusmessage'] = 'Пустой идентификатор сообщения';
            $result['status'] = self::MSG_STATUS_ERROR;

            return $result;
        }

        $response = $this->queryNG($messageId);

        if (!empty($response['error'])) {
            $result['error'] = true;
            $result['status'] = self::MSG_STATUS_ERROR;
            $result['statusmessage'] = $response['error'];
            return $result;
        }

        switch ($response['status']) {
            case 'succeed':
            case 'delivered':
            case 'seen':
                $result['statusmessage'] = "Сообщение было доставлено";
                $result['status'] = self::MSG_STATUS_DELIVERED;
                $result['error'] = false;

                break;
            case 'failed':
                $result['statusmessage'] = "Сообщение не было доставлено";
                $result['status'] = self::MSG_STATUS_FAILED;
                $result['error'] = true;

                break;
            case 'processing':
            case 'sent':
            case 'pending':
                // Не конечный статус (меняется со временем).
                $result['needlookup'] = 1;
                $result['statusmessage'] = "Передано в MSGC на отправку - в очереди отправки MSGC";
                $result['status'] = self::MSG_STATUS_PROCESSING;
                $result['error'] = false;
                break;
            default:
                $result['statusmessage'] = "Неизвестный статус: {$response['status']}/{$response['msg']}";
                $result['status'] = self::MSG_STATUS_ERROR;
                $result['error'] = true;

                break;
        }

        return $result;
    }

    //GET https://set.sigmasms.ru/api/sendings/[ID СООБЩЕНИЯ]
    public function queryNG($msgid)
    {
        $response = $this->sigmaRequest('sendings/'.$msgid);

        if (!in_array('id', array_keys($response))) return false;

        return [
            'id'     => $response['id'],
            'status' => $response['state']['status'],
            'msg'    => '',
        ];
    }

    public function send($msg, $to)
    {
        $tgt = is_array($to)?implode(',', $to):$to;
        $body = [
            "type"      => "sms",
            "recipient" => $tgt,
            "payload"   => [
                "sender" => $this->getParameter(self::SENDER_PARAM),
                "text"   => $msg
            ]
        ];

        $response = $this->sigmaRequest('sendings', $body);

        if (is_bool($response)) return false;

        /*
        //Sample error message
        "data":[{
            "type":"string",
            "field":"payload.sender",
            "message":"The \'payload.sender\' field must be a string!"
        }]
         */
        if (empty($response['id'])) {
            $msg = [];
            if (array_key_exists('error', $response)) {
                $msg[] = 'Error: ' . $response['error'];
            }
            if (array_key_exists('message', $response)) {
                $msg[] = $response['message'];
            }

            return implode('; ', $msg);
        }

        $result = [
            'id' => $response['id'],
            'to' => $tgt,
            'status' => $response['status'],
            'statusmessage' => self::$MSGS['PROCESSING'],
            'raw' => $response
        ];

        return [$result];
    }

    /**
     * Wrapper to lowlevel curl request
     *
     * @param str   $op   operation
     * @param array $data post data
     *
     * @return curl request results
     */
    public function sigmaRequest($op, $data = [])
    {
        $auth = $this->_getToken();
        $gotToken = is_array($auth) && array_key_exists('token', $auth);
        if (!$gotToken) {
            return 'No token';
        }

        $token = $auth['token'];
        $query = '';

        $gotData = !empty($data);
        if ($gotData) {
            $query = json_encode($data);
        }

        $headers = [
            'Authorization: ' . $token,
        ];

        return $this->_curl($op, $query, $headers);
    }
    
    /**
     * GET request
     * TODO token caching
     *
     * @return array
     */
    private function _getToken()
    {
        $args = [
            'username' => $this->_username,
            'password' => $this->_password
        ];
        return $this->_curl(
            'login',
            json_encode($args)
        );
    }

    /**
     * Request
     *
     * @param str   $op      sigmasms operation
     * @param str   $data    post data as a string
     * @param array $head    extra headers
     * @param int   $timeout defaults to 10 seconds
     *
     * @return mixed decoded json response
     */
    private function _curl($op, $data = '', $head = [], $timeout = 10)
    {
        $svcUrl = self::HOST . 'api/' . $op;

        $common = [
            'Content-type: application/json',
            'Referer: ' . self::HOST,
        ];

        $headers = array_merge($common, $head);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $svcUrl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            $headers[] = 'Content-length: ' . strlen($data);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $response = curl_exec($curl);

        $decoded = json_decode($response, 1);
        if (json_last_error() != JSON_ERROR_NONE) return false;

        $stats = [
            'code'  => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            'error' => curl_error($curl),
            'data'  => $decoded,
        ];

        curl_close($curl);

        return $decoded;    
    }
}
?>