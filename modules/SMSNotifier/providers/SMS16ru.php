<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once 'vtlib/Vtiger/Net/Client.php';

class SMSNotifier_SMS16ru_Provider implements SMSNotifier_ISMSProvider_Model {

    private $_username;
    private $_password;
    private $_parameters = array();

    const SENDER_PARAM = 'LBL_SMS_SENDER_SMS16ru';

    private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM);

    function __construct() {
    }

    public function getName() {
        return 'SMS16ru';
    }

    public function setAuthParameters($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
    }

    public function setParameter($key, $value) {
        $this->_parameters[$key] = $value;
    }

    public function getParameter($key, $defvalue = false)  {
        if(isset($this->_parameters[$key])) {
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

    public function send($message, $tonumbers) {
        if (!is_array($tonumbers)) {
            $tonumbers = array($tonumbers);
        }
        $sender = $this->getParameter(self::SENDER_PARAM);
        $message = htmlspecialchars($message);
        $results = array();
        $src = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>".
            "<request><message type=\"sms\"><sender>$sender</sender><text>$message</text>";
        $ix = 1;
        foreach ($tonumbers as $to) {
            $src .= "<abonent phone=\"$to\" number_sms=\"$ix\"/>";
            $ix++;
        }
        $src .= "</message><security><login value=\"$this->_username\" /><password value=\"$this->_password\" /></security></request>";
        $response_xml = $this->curl_send($src, 'xml.sms16.ru/xml/');
        $response = simplexml_load_string($response_xml);
        $error = (string)$response->error;
        $ix = 0;
        foreach ($tonumbers as $to) {
            $result['id'] = time(); // generate id
            $result['to'] = $to;
            if (!empty($error)) {
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_ERROR;
                $result['statusmessage'] = $error;
            } else {
                if (is_object($response->information[$ix])) {
                    $response_attrs = $response->information[$ix]->attributes();
                    $result['id'] = (string)$response_attrs["id_sms"];
                    if (!empty($result['id'])) {
                        $result['error'] = false;
                        $result['status'] = self::MSG_STATUS_PROCESSING;
                    } else {
                        $result['error'] = true;
                        $result['status'] = self::MSG_STATUS_ERROR;
                        $result['statusmessage'] = (string)$response->information;
                    }
                } else {
                    $result['error'] = true;
                    $result['status'] = self::MSG_STATUS_ERROR;
                    $result['statusmessage'] = (string)$response->information;
                }
            }
            $ix++;
            $results[] = $result;
        }
        return $results;
    }

    public function query($messageid) {
        if (empty($messageid)){
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['statusmessage'] = 'Пустой идентификатор сообщения';
            $result['status'] = self::MSG_STATUS_ERROR;
            return ($result);
        }
        $src = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>".
            "<request><security><login value=\"$this->_username\" /><password value=\"$this->_password\" /></security>".
            "<get_state><id_sms>$messageid</id_sms></get_state></request>";
        $response_xml = $this->curl_send($src, 'xml.sms16.ru/xml/state.php');
        @$response = simplexml_load_string($response_xml);
        $result['id'] = $messageid;
        if (!empty($response->error)) {
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['status'] = self::MSG_STATUS_ERROR;
            $result['statusmessage'] = $response->error;
        } else {
            $result['error'] = false;
            $msg = "";
            switch($response->state[0]) {
                case 'send':
                    // статус сообщения не получен. В этом случае передается пустой time
                    $msg = "Статус сообщения не получен";
                    $result['status'] = self::MSG_STATUS_PROCESSING;
                    $result['needlookup'] = 1;
                    break;
                case 'partly_deliver':
                    // сообщение было отправлено, но статус так и не был получен.
                    // Конечный статус (не меняется со временем).
                    // В этом случае для разъяснения причин отсутствия статуса необходимо связаться со службой тех. поддержки
                    $msg = "Сообщение было отправлено, но статус так и не был получен";
                    $result['status'] = self::MSG_STATUS_FAILED;
                    $result['needlookup'] = 0;
                    $result['error'] = true;
                    break;
                case 'not_deliver':
                    // сообщение не было доставлено. Конечный статус (не меняется со временем)
                    $msg = "Сообщение не было доставлено";
                    $result['status'] = self::MSG_STATUS_FAILED;
                    $result['needlookup'] = 0;
                    break;
                case 'expired':
                    // абонент находился не в сети в те моменты, когда делалась попытка доставки. Конечный статус (не меняется со временем)
                    $msg = "Абонент находился вне сети";
                    $result['status'] = self::MSG_STATUS_FAILED;
                    $result['needlookup'] = 0;
                    break;
                case 'deliver':
                    // сообщение доставлено. Конечный статус (не меняется со временем)
                    $msg = "Сообщение доставлено";
                    $result['status'] = self::MSG_STATUS_DELIVERED;
                    $result['needlookup'] = 0;
                    break;
                default:
                    $msg = "Неизвестный статус";
                    $result['status'] = self::MSG_STATUS_ERROR;
                    $result['needlookup'] = 0;
                    $result['error'] = true;
                    break;
            }
            $result['statusmessage'] = $msg;
        }
        return $result;
    }

    private function curl_send($xml, $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response_xml = curl_exec($ch);
        curl_close($ch);
        return $response_xml;
    }
}
?>
