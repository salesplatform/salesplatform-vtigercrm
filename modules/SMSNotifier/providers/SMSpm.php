<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Net/Client.php';


function check_gsm($str) 
{ 
    $arr = array( 
"0x00", "0x01", "0x02", "0x03", "0x04", "0x05","0x06","0x07","0x08","0x09", 
"0x0A","0x0B","0x0C","0x0D","0x0E","0x0F","0x10","0x11","0x12","0x13", 
"0x14","0x15","0x16","0x17","0x18","0x19","0x1A","0x1B","0x1B0A", 
"0x1B14","0x1B28","0x1B29","0x1B2F","0x1B3C","0x1B3D","0x1B3E", 
"0x1B40","0x1B65","0x1C","0x1D","0x1E","0x1F","0x20","0x21","0x22", 
"0x23","0x24","0x25","0x26","0x27","0x28","0x29","0x2A","0x2B","0x2C", 
"0x2D","0x2E","0x2F","0x30","0x31","0x32","0x33","0x34","0x35","0x36", 
"0x37","0x38","0x39","0x3A","0x3B","0x3C","0x3D","0x3E","0x3F","0x40", 
"0x41","0x42","0x43","0x44","0x45","0x46","0x47","0x48","0x49","0x4A", 
"0x4B","0x4C","0x4D","0x4E","0x4F","0x50","0x51","0x52","0x53","0x54", 
"0x55","0x56","0x57","0x58","0x59","0x5A","0x5B","0x5C","0x5D","0x5E", 
"0x5F","0x60","0x61","0x62","0x63","0x64","0x65","0x66","0x67","0x68", 
"0x69","0x6A","0x6B","0x6C","0x6D","0x6E","0x6F","0x70","0x71","0x72", 
"0x73","0x74","0x75","0x76","0x77","0x78","0x79","0x7A","0x7B","0x7C", 
"0x7D","0x7E","0x7F", "0xE2"); 
    $j=0;
    $strl = strlen($str); 
    for ($i = 0;$i < $strl; $i++) 
    { 
        $char = '0x' . bin2hex(substr($str,$i,1)); 
        $pos = in_array($char,$arr); 
        if ($pos == 1) 
        { 
            $j++; 
        } 
    } 
  
    if ($j < $strl) 
    { 
        return false; 
    } 
    else 
    { 
        return true; 
    } 
}

class SMSNotifier_SMSpm_Provider implements SMSNotifier_ISMSProvider_Model {
	
	private $_parameters = array();
	
	const SERVICE_URI = 'http://panel.smspm.com/gateway/';
        const AUTH_HASH = 'LBL_SMSPM_AUTH_HASH';
	const SENDER = 'LBL_SMS_SENDER_SMSpm';

	private static $REQUIRED_PARAMETERS = array(self::AUTH_HASH, self::SENDER);
	
	function __construct() {		
	}
        
        public function getName() {
		return 'SMSpm';
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
		if($type) {
			switch(strtoupper($type)) {				
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND: return  self::SERVICE_URI . $this->getParameter(self::AUTH_HASH).'/api.v1/send';
				case self::SERVICE_QUERY: return self::SERVICE_URI . $this->getParameter(self::AUTH_HASH).'/api.v1/query/';
			}
		}
		return false;
	}	
	
	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}
		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$results = array();
		foreach($tonumbers as $number) {
			$param = array(
			'sender'  => $this->getParameter(self::SENDER),
			'message' => $message,
			'phone'   => $number,
			'output'  => 'json'
			);
			if(!check_gsm($message)){
				$param['unicode'] = 'on';
			}
			$response = $httpClient->doGet($param);
			$responseLine = trim($response);
			$result = array( 'error' => false, 'statusmessage' => '' );
			$reply = json_decode($responseLine, true);
			if(key($reply) == 'error') {
				$result['id'] = 'error';
				$result['error'] = true; 
				$result['to'] = $number;
				$result['statusmessage'] = $reply['error']['message']; // Complete error message
			} if(key($reply) == 'submitted') {
				$result['id'] = $reply['id'];
				$result['to'] = $number;
				$result['status'] = self::MSG_STATUS_PROCESSING;
			}
			$results[] = $result;
		}
		return $results;
	}
	
	public function query($messageid) {
		if(empty($messageid)){
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = 'Error';
			return($result);
		}
		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$httpClient = new Vtiger_Net_Client($serviceURL.$messageid);
		$response = $httpClient->doGet(array(
			'output'    => 'json'
			));
		$response = trim($response);
		$result = array( 'error' => false, 'needlookup' => 1, 'statusmessage' => '' );
		$reply = array();
		$reply = json_decode($response, true);
		if(key($reply) == 'error') {
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = 'Error';
		} else {
			$result['id'] = $messageid;
			// Capture the status code as message by default.
			$result['statusmessage'] = "unknown";
			switch(key($reply)) {
			case 'queued': $statusMessage = 'Message is waiting in queue';
				$result['status'] = self::MSG_STATUS_PROCESSING;
				$needlookup = 1;
				break;
			case 'submitting': $statusMessage = 'Message is about to be submitted';
				$needlookup = 1;
				break;
			case 'submitted': $statusMessage = 'Message has been sent';
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$needlookup = 1;
				break;
			case 'delivered': $result['status'] = 'Delivered';$statusMessage = 'Message delivered to phone';    $needlookup = 0; break;
			case 'submitFailed': $statusMessage = 'Message rejected (invalid recipient, invalid senderID)';     $needlookup = 0; break;
			case 'deliveryFailed': $statusMessage = 'Message undelivered (non-existent number, roaming error)'; $needlookup = 0; break;
			}
			if(!empty($statusMessage)) {
				$result['needlookup'] = $needlookup;
				$result['statusmessage'] = $statusMessage;
			}
		}

		return $result;
	}
}
?>
