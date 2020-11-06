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

use SPVoipIntegration\integration\AbstractNotification;

abstract class RostelecomAbstractNotification extends AbstractNotification {
    
    protected $fieldsMapping = array();
    
    public static function getInstance($requestData) {
        $stateType = $requestData['state'];
        $eventType = $requestData['type'];
        
        switch ($stateType) {
            case RostelecomEventAndStateType::NEW_STATE:
                if ($eventType == RostelecomEventAndStateType::INCOMING) {
                    return new RostelecomNewCallIn($requestData);
                }
                else if ($eventType == RostelecomEventAndStateType::OUTBOUND) {
                    return new RostelecomNewCallOut($requestData);
                }
            case RostelecomEventAndStateType::CONNECTED:
                return new RostelecomConnectedCall($requestData);
            case RostelecomEventAndStateType::DISCONNECTED:
                return new RostelecomEndCall($requestData);
            default:
                throw new \Exception('Unknown rostelecom notification');
        }
    }
    
    public function validateNotification() {
        
    }
    
    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }
    
    /*
     * Rostelecom may sends phonenumber like:
     * sip:+71234567890@188.254.33.73 or sip:username@188.254.33.73
     */
    public static function getPhoneNumberFromRequestString($phoneString) {
        // if phone number clean, without sip and domain
        if (!substr_count($phoneString, 'sip:')) {
            return $phoneString;
        }
        
        $phone = str_replace('sip:', '', $phoneString);
        $stringBeforeAtSign = strstr($phone, '@', true);
        return $stringBeforeAtSign;
    }
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    public function process() {
        if (!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) {
            return;        
        }
        
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }
    
    protected function getSourceUUId() {
        return $this->get('session_id');
    }
    
    protected function getEventDatetime($timestamp) {
        return date('Y-m-d H:i:s', $timestamp);
    }
    
    /**
     * Returns call duration in seconds
     * @param type $startTime
     * @param type $endTime
     * @return type
     */
    public function getTotalDurationTime($startTime, $endTime) {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $totalDutation = intval($end) - intval($start);
        if ($totalDutation > 0) {
            return $totalDutation;
        }
        return 0;
    }
    
    /**
     * Find user in CRM by internal or external numbers
     * @param type $number
     * @return type
     */
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_rostelecom_extension=? or sp_rostelecom_extension_internal=?", array($number, $number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
    
}

