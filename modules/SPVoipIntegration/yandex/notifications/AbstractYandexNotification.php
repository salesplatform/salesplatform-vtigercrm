<?php

namespace SPVoipIntegration\yandex\notifications;

use SPVoipIntegration\integration\AbstractNotification;
use SPVoipIntegration\ProvidersEnum;

abstract class AbstractYandexNotification extends AbstractNotification{
    
    const SOURCE_ID_PREFIX = "yandex_";
    protected $fieldsMapping = array();
        
    protected abstract function canCreatePBXRecord();
    
    public function __construct($values = array()) {
        $values = json_decode(file_get_contents("php://input"), true);
        parent::__construct($values);
    }
    
    
    public static function getInstance($requestData) {
        $requestObj = json_decode(file_get_contents("php://input"));
        $eventType = $requestObj->EventType;

        switch ($eventType) {
            case YandexEventType::CALLBACK_CALL_COMPLETED    :  return new CallbackCallCompleted($requestData);
            case YandexEventType::CALLBACK_CALL_CONNECTED    :  return new CallbackCallConnected($requestData);
            case YandexEventType::CALLBACK_CALL_RINGING      :  return new CallbackCallRinging($requestData);
            case YandexEventType::CALLBACK_CALL_STOP_RINGING :  return new CallbackCallStopRinging($requestData);
            case YandexEventType::INCOMING_CALL_COMPLETED    :  return new IncomingCallCompleted($requestData);
            case YandexEventType::INCOMING_CALL_CONNECTED    :  return new IncomingCallConnected($requestData);
            case YandexEventType::INCOMING_CALL_RINGING      :  return new IncomingCallRinging($requestData);
            case YandexEventType::INCOMING_CALL_STOP_RINGING :  return new IncomingCallStopRinging($requestData);
            case YandexEventType::OUTGOING_CALL              :  return new OutgoingCall($requestData);
            case YandexEventType::OUTGOING_CALL_COMPLETED    :  return new OutgoingCallCompleted($requestData);
            case YandexEventType::OUTGOING_CALL_CONNECTED    :  return new OutgoingCallConnected($requestData);
            default :
                throw new \Exception('Unused yandex notification');
        }        
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
    
    protected function doConnectedActions() {
        $eventTime = $this->get('Timestamp');
        $currentStatus = $this->pbxManagerModel->get('callstatus');
        if ($currentStatus == 'ringing') {
            $startDatetime = $this->pbxManagerModel->get('starttime');
            $diff = strtotime($eventTime) - strtotime($startDatetime);
            if ($diff > 0) {
                $this->set('totalduration', $diff);
            }
            $this->set('callstatus', 'in-progress');
        }
    }

    protected function doStoppedActions() {
        $eventTime = $this->get('Timestamp');
        $currentStatus = $this->pbxManagerModel->get('callstatus');
        if ($currentStatus == 'ringing') {
            $pbxStartDateTime = $this->pbxManagerModel->get('starttime');
            $diff = strtotime($eventTime) - strtotime($pbxStartDateTime);
            if ($diff > 0) {
                $this->set('totalduration', $diff);
            }
            $this->set('endtime', date('Y-m-d H:i:s', strtotime($eventTime)));
            $this->set('callstatus', 'no-answer');
        }
    }

    protected function doCompletedActions($pbxModel) {
        if (empty($pbxModel)) {
            return;
        }
        $eventTime = $this->get('Timestamp');
        $eventTimestamp = strtotime($eventTime);
        $startDateTime = $pbxModel->get('starttime');
        $totalDuration = $pbxModel->get('totalduration');
        $currentStatus = $pbxModel->get('callstatus');
        $pbxModel->set('mode', 'edit');
        if ($currentStatus == 'in-progress') {
            $diff = $eventTimestamp - strtotime($startDateTime);
            $billDuration = 0;
            if ($diff > $totalDuration) {
                if ($totalDuration > 0) {
                    $billDuration = $diff - $totalDuration;
                }
                $totalDuration = $diff;
            }
            $pbxModel->set('totalduration', $totalDuration);
            $pbxModel->set('billduration', $billDuration);
            $pbxModel->set('endtime', date('Y-m-d H:i:s', $eventTimestamp));
            $pbxModel->set('callstatus', 'completed');
        } else if ($currentStatus != 'no-answer') {
            $pbxModel->set('callstatus', 'no-answer');
            $pbxModel->set('totalduration', 0);
            $pbxModel->set('endtime', date('Y-m-d H:i:s', $eventTimestamp));
        }
        return $pbxModel;
    }

    protected function doRingingActions() {
        $eventTime = $this->get('Timestamp');
        $this->set('starttime', date('Y-m-d H:i:s', strtotime($eventTime)));
        $this->set('callstatus', 'ringing');
        $this->set('sourceuuid', $this->getSourceUUId());
        $this->set('sp_voip_provider', ProvidersEnum::YANDEX);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }
    
    protected function getSourceUUId() {
        return AbstractYandexNotification::SOURCE_ID_PREFIX . $this->get('Body')['Id'] . '_' . $this->get('Body')['Extension'];
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_yandex_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
    
    public function validateNotification() {
        
    }
    
    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }

}
