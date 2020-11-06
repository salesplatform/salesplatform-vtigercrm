<?php
namespace SPVoipIntegration\telphin\notifications;

use SPVoipIntegration\integration\AbstractNotification;

abstract class TelphinAbstractNotification extends AbstractNotification{
    
    const SOURCE_ID_PREFIX = "telphin_";
    const MICRO_DELIMETER = 1000000;
    
    
    protected $fieldsMapping = array();
    
    protected abstract function canCreatePBXRecord();
    
    public static function getInstance($requestData) {
        $eventType = $requestData['EventType'];

        switch ($eventType) {
            case TelphinEventType::DIAL_IN :
                return new TelphinDialIn($requestData);
            case TelphinEventType::DIAL_OUT :
                return new TelphinDialOut($requestData);
            case TelphinEventType::ANSWER :
                return new TelphinAnswer($requestData);
            case TelphinEventType::HANGUP :
                return new TelphinHangup($requestData);
            default :
                throw new \Exception('Unknown telphin notification');
        }        
    }
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    public function validateNotification() {
        
    }
    
    public function process() {
        if (!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) {
            return;        
        }
        
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }
    
    protected function getSourceUUId() {
        return TelphinAbstractNotification::SOURCE_ID_PREFIX . $this->get('SubCallID');
    }
    
    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }
    
    protected function getEventDatetime() {
        $microTimestamp = $this->get('EventTime');
        return date('Y-m-d H:i:s', $microTimestamp/self::MICRO_DELIMETER);
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_telphin_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
}
