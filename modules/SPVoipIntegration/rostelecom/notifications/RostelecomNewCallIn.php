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

use SPVoipIntegration\ProvidersEnum;

class RostelecomNewCallIn extends RostelecomAbstractNotification {
    
    protected $fieldsMapping = array(
        'starttime' => 'starttime',
        'callstatus' => 'callstatus',
        'direction' => 'direction',
        'user' => 'user',
        'sourceuuid' => 'sourceuuid',
        'customernumber' => 'from_number',
        'sp_voip_provider' => 'sp_voip_provider',
    );
    
    protected $direction = 'inbound';   
    
    protected function getCustomerPhoneNumber() { 
        $internalRtNumber = $this->get('from_pin');
        if ($internalRtNumber) {
            return $internalRtNumber;
        }
        else {
            $externalRtNumber = $this->get('from_number');
            $cleanNumber = $this->getPhoneNumberFromRequestString($externalRtNumber);
            return $cleanNumber;
        }
    }
    
    protected function getUserPhoneNumber() {
        $number = $this->get('request_number');
        $cleanNumber = $this->getPhoneNumberFromRequestString($number);
        return $cleanNumber;
        }
    
    protected function prepareNotificationModel() {
        $startTime = $this->get('timestamp');
        $this->set('starttime', $startTime);
        $this->set('sp_voip_provider', ProvidersEnum::ROSTELECOM);
        $this->set('direction', $this->direction);
        $this->set('callstatus', 'ringing');
        $this->set('sourceuuid', $this->getSourceUUId());
        
        $this->set('from_number', $this->getCustomerPhoneNumber());
        $this->set('request_number', $this->getUserPhoneNumber());
        
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }
    
    protected function canCreatePBXRecord() {
        return true;
    }
    
}

