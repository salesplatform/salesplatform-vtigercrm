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
include_once dirname(__FILE__) . '/vesms/VESMS.class.php';
include_once dirname(__FILE__) . '/vesms/smsc_api.php';

class SMSNotifier_VESMSru_Provider implements SMSNotifier_ISMSProvider_Model {
	
	private $_username;
	private $_password;
	private $_parameters = array();
	private $_msg_error = array(1 => 'Ошибка в параметрах.', 2 => 'Неверный логин или пароль.',
                3 => 'Недостаточно средств на счете Клиента.',
                4 => 'IP-адрес временно заблокирован из-за частых ошибок в запросах.',
                5 => 'Неверный формат даты.',
                6 => 'Сообщение запрещено (по тексту или по имени отправителя).',
                7 => 'Неверный формат номера телефона.',
                8 => 'Сообщение на указанный номер не может быть доставлено.',
                9 => 'Отправка более одного одинакового запроса на передачу 
                    SMS-сообщения либо более пяти 
                    одинаковых запросов на получение
                    стоимости сообщения в течение минуты.',
            );
        private $_msg_status = array(-1 => 'Ожидает отправки.', 0 => 'Передано оператору.',
                1 => 'Доставлено.', 3 => 'Просрочено.', 20 => 'Невозможно доставить.',
                22 => 'Неверный номер.', 23 => 'Запрещено.', 24 => 'Недостаточно средств.',
                25 => 'Недоступный номер.',
            );
        private $_vesmsru;
	const SENDER_PARAM = 'LBL_SMS_SENDER_VESMSru';
        
	private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM);
	
	function __construct() {
	}
	
        public function getName() {
		return 'VESMSru';
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
                $message = iconv('UTF-8', 'CP1251', htmlspecialchars($message));
		$results = array();
                foreach($tonumbers as $to) {
                    $result = array();
                    if (!empty($to)) {
                        $result = send_sms($to, $message, 0, 0, 0, 0, $sender,"fmt=1", $this->_username, $this->_password);
                        $sms_id = $result[0];
                        $result['to'] = $to;
                        if (!empty($sms_id)) {
                            $result['id'] = $sms_id .":::".$to;
                            $status = get_status($sms_id, $to, 0, $this->_username, $this->_password);
                            $result['status'] = self::MSG_STATUS_PROCESSING;
                            if (count($status) > 2) {
                                $result['statusmessage'] = $this->_msg_status[$status[0]];
                            } else {
                                $result['statusmessage'] = $this->_msg_status[substr($status[1], 1)];
                            }
                        } else {
                            $result['error'] = true;
                            $result['status'] = self::MSG_STATUS_ERROR;
                            $result['statusmessage'] = $this->_msg_error[substr($result[1], 1)];
                        }
                    }
                    else {
                        $result['to'] = 'Incorrect phone';
                        $result['error'] = true;
                        $result['status'] = self::MSG_STATUS_ERROR;
                        $result['statusmessage'] = 'Ошибочный номер телефона';
                    }
                    
                    $results[] = $result;
                }
                
		return $results;
	}

	public function query($messageid) {
		$result = array();
		if (empty($messageid)) {
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = 'Пустой идентификатор сообщения';
			$result['status'] = self::MSG_STATUS_ERROR;
			return $result;
		}
                $data = explode(':::', $messageid);
                $messageid = $data[0];
                $phone = $data[1];
                $status = get_status($messageid, $phone, 0, $this->_username, $this->_password);
                if (count($status) < 3) {
                    $result['statusmessage'] = $this->_msg_status[substr($status[1], 1)];
                    $result['error'] = true;
                    $result['needlookup'] = 0;
                    $result['status'] = self::MSG_STATUS_ERROR;
                } else {
                    $result['statusmessage'] = $this->_msg_status[$status[0]];
                    switch($result['statusmessage']) {
                                case '-1':
                                        $result['status'] = self::MSG_STATUS_PROCESSING;
                                        $result['needlookup'] = 1;
                                        break;
                                case '3':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '0':
                                        $result['status'] = self::MSG_STATUS_PROCESSING;
                                        $result['needlookup'] = 1;
                                        break;
                                case '1':
                                        $result['status'] = self::MSG_STATUS_DELIVERED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '20':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '22':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '23':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '24':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break;
                                case '25':
                                        $result['status'] = self::MSG_STATUS_FAILED;
                                        $result['needlookup'] = 0;
                                        break; 
                                default:
                                        $result['status'] = self::MSG_STATUS_ERROR;
                                        $result['needlookup'] = 0;
                                        break;
                    }
                    
                }
                $result = array('id' => $messageid, 'error' => false, 'statusmessage' => $result['statusmessage']);
		return $result;
	}
}
?>
