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
include_once dirname(__FILE__) . '/devinotele/smsClient.php';


class SMSNotifier_DevinoTelecom_Provider implements SMSNotifier_ISMSProvider_Model {
	
	private $_username;
	private $_password;
	private $_parameters = array();
        
        private $_devino;
        private $_msg_status = array(-1 => 'Отправлено (передано в мобильную сеть)', 
                                        -2 => 'В очереди',
                                        47 => 'Удалено', 
                                        -98 => 'Остановлено', 
                                        0 => 'Доставлено абоненту',
                                        10 => 'Неверно введен адрес отправителя', 
                                        11 => 'Неверно введен адрес получателя', 
                                        41 => 'Недопустимый адрес получателя',
                                        42 => 'Отклонено смс центром', 
                                        46 => 'Просрочено (истек срок жизни сообщения)',
                                        48 => 'Отклонено Платформой', 
                                        69 => 'Отклонено', 
                                        99 => 'Неизвестный', 
                                        255 => 'Не успело попасть в БД либо сообщение старше 48 часов');
	const SENDER_PARAM = 'LBL_SMS_SENDER_DevinoTelecom';
        
	private static $REQUIRED_PARAMETERS = array(self::SENDER_PARAM);
	
	function __construct() {
	}
	
        public function getName() {
		return 'DevinoTelecom';
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
                $this->_devino = new SMSClient($this->_username, $this->_password);
                foreach($tonumbers as $to) {
                    $result['id'] = time(); // generate id
                    if (!empty($to)) {
                        $result_devino = array();
                        $result['to'] = $to;
                        try {
                            $session_id = $this->_devino->getSessionID();
                            $result_devino = $this->_devino->send($sender, $to, $message);
                        } catch (Exception $e) {
                            $result['error'] = true;
                            $result['status'] = self::MSG_STATUS_ERROR;
                            $result['statusmessage'] = 'Сообщение не доставлено';
                        }
                        if (!empty($result_devino)) {
                            $result['id'] = $result_devino[0];
                            $status = $this->_devino->getSMSState($result['id']);
                            $result['status'] = self::MSG_STATUS_PROCESSING;
                            $result['statusmessage'] = $status['StateDescription'];
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
                $this->_devino = new SMSClient($this->_username, $this->_password);
                $result['id'] = $messageid;
                try {
                    $this->_devino->getSessionID();
                    $status = $this->_devino->getSMSState($messageid);
                    switch($status['State']) {
                            case '-1':
                                    $result['status'] = self::MSG_STATUS_DELIVERED;
                                    $result['needlookup'] = 0;
                                    break;
                            case '-2':
                                    $result['status'] = self::MSG_STATUS_PROCESSING;
                                    $result['needlookup'] = 1;
                                    break;
                            case '47':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;
                            case '-98':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;   
                            case '0':
                                    $result['status'] = self::MSG_STATUS_DELIVERED;
                                    $result['needlookup'] = 0;
                                    break;
                            case '10':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;    
                            case '11':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;    
                            case '41':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;     
                            case '42':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;    
                            case '46':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;    
                            case '48':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;    
                            case '69':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;     
                            case '99':
                                    $result['status'] = self::MSG_STATUS_FAILED;
                                    $result['needlookup'] = 0;
                                    break;
                            case '255':
                                    $result['status'] = self::MSG_STATUS_PROCESSING;
                                    $result['needlookup'] = 1;
                                    break;
                            default:
                                    $result['status'] = self::MSG_STATUS_ERROR;
                                    $result['needlookup'] = 0;
                                    break;
                    }
                    $result['statusmessage'] = $this->_msg_status[$status['State']];
                } catch (Exception $e) {
                    $result['status'] = self::MSG_STATUS_FAILED;
                    $result['needlookup'] = 0;
                    $result['statusmessage'] = 'Ошибка запроса статуса';
                }
		return $result;
	}
}

?>
