<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\rostelecom\notifications;

class RostelecomNewCallOut extends RostelecomNewCallIn {
    
    protected $direction = 'outbound';
    
    public function __construct($values = array()) {
        parent::__construct($values);
        $this->fieldsMapping['customernumber'] = 'request_number';
    }
    
    protected function getCustomerPhoneNumber() {
        $number = $this->get('request_number');
        $cleanNumber = $this->getPhoneNumberFromRequestString($number);
        return $cleanNumber;
    }
    
    protected function getUserPhoneNumber() {   
        $externalRtNumber = $this->get('from_number');
        if ($externalRtNumber) {
            $cleanNumber = $this->getPhoneNumberFromRequestString($externalRtNumber);
            if (is_numeric($cleanNumber)) {
                return $cleanNumber;
            }
        }
        else {
            return $this->get('from_pin');
        }
    }
    
    protected function prepareNotificationModel() {
        $toNumber = $this->getCustomerPhoneNumber();
        
        parent::prepareNotificationModel();
        $this->set('from_number', '');
        $this->set('request_number', $toNumber);
        
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }
    
}

