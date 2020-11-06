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
include_once dirname(__FILE__) . '/streamsms/StreamClass.php';

class SMSNotifier_StreamTelecomRu_Provider implements SMSNotifier_ISMSProvider_Model {

    private $_username;
    private $_password;
    private $server = 'http://gateway.api.sc/rest/';
    private $_parameters = array();

    private $_streamsms;

    const SENDER_PARAM = 'LBL_SMS_SENDER_StreamTelecomRu';
    const TIME_PARAM = 'LBL_SMS_TIME_StreamTelecomRu';

    private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM, self::TIME_PARAM);

    function __construct() {
        $this->_streamsms = new STREAM();
    }

    public function getName() {
        return 'StreamTelecomRu';
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

    public function send($message, $toNumbers) {
        if(!is_array($toNumbers)) {
            $toNumbers = array($toNumbers);
        }

        $time = $this->getParameter(self::TIME_PARAM);
        if ($time <= 0) {
            $time = 10;
        }
        $sender = $this->getParameter(self::SENDER_PARAM);
        $message = htmlspecialchars($message);

        $session = $this->_streamsms->GetSessionId(
            $this->server,
            $this->_username,
            $this->_password
        );

        $results = array();
        foreach($toNumbers as $to) {
            $result['to'] = $to;
            $messageId = $this->_streamsms->SendSms(
                $this->server,
                $session,
                $sender,
                $to,
                $message,
                $time
            );

            if(!isset($messageId[0])) {
                return array(
                    'id' => $messageId,
                    'error' => true,
                    'needlookup' => 0,
                    'statusmessage' => 'Ошибка при отправке сообщения',
                    'status' => self::MSG_STATUS_ERROR
                );
            }

            $messageStatus = $this->_streamsms->GetState(
                $this->server,
                $session,
                $messageId[0]
            );

            if(!isset($messageStatus['State'])) {
                return array(
                    'id' => $messageId,
                    'error' => true,
                    'needlookup' => 0,
                    'statusmessage' => 'Ошибка при получении статуса сообщения',
                    'status' => self::MSG_STATUS_ERROR
                );
            }

            $result['id'] = $messageId;
            if($messageStatus['State'] == -1) {
                $result['statusmessage'] = 'Сообщение передано в мобильную сеть';
                $result['error'] = false;
                $result['status'] = self::MSG_STATUS_PROCESSING;
            } else {
                $result['statusmessage'] = 'Не доставлено или просрочено';
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_ERROR;
            }

            $results[] = $result;
        }

        return $results;
    }

    public function query($messageId) {
        if(empty($messageId)){
            $result['error'] = true;
            $result['needlookup'] = 0;
            $result['statusmessage'] = 'Пустой идентификатор сообщения';
            $result['status'] = self::MSG_STATUS_ERROR;
            return($result);
        }

        $session = $this->_streamsms->GetSessionId(
            $this->server,
            $this->_username,
            $this->_password
        );

        $messageStatus = $this->_streamsms->GetState(
            $this->server,
            $session,
            $messageId
        );

        if(!isset($messageStatus['State'])) {
            return array(
                'id' => $messageId,
                'error' => true,
                'needlookup' => 0,
                'statusmessage' => 'Ошибка при получении статуса сообщения',
                'status' => self::MSG_STATUS_ERROR
            );
        }

        $result['id'] = $messageId;
        switch($messageStatus['State']) {
            case -1:
                $result['error'] = false;
                $result['status'] = self::MSG_STATUS_PROCESSING;
                $result['needlookup'] = 1;
                $result['statusmessage'] = 'Сообщение передано в мобильную сеть';
                break;
            case 0:
                $result['error'] = false;
                $result['status'] = self::MSG_STATUS_DELIVERED;
                $result['needlookup'] = 0;
                $result['statusmessage'] = 'Сообщение доставлено получателю';
                break;
            case 42:
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_FAILED;
                $result['needlookup'] = 0;
                $result['statusmessage'] = 'Сообщение не доставлено';
                break;
            case 46:
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_FAILED;
                $result['needlookup'] = 0;
                $result['statusmessage'] = 'Просрочено (истек срок жизни сообщения)';
                break;

            default:
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_FAILED;
                $result['needlookup'] = 0;
                $result['statusmessage'] = self::MSG_STATUS_FAILED;
                break;
        }

        return $result;
    }
}
?>
