<?php

namespace SPVoipIntegration\mcntelecom\notifications;

use SPVoipIntegration\integration\AbstractNotification;

abstract class MCNAbstractNotification extends AbstractNotification{        
    
    protected $fieldsMapping = array();
    protected abstract function canCreatePBXRecord();

    const SOURCE_ID_PREFIX = "mcn_";
    
    public function __construct($values = array()) {
        $values = json_decode(file_get_contents("php://input"), true);
        parent::__construct($values);
    }
    
    public static function getInstance() {
        $requestObj = json_decode(file_get_contents("php://input"));
        $eventType = $requestObj->event_type;
        
        switch ($eventType) {
            case MCNEventType::ON_IN_CALLING_START      : return new MCNInboundStart();
            case MCNEventType::ON_IN_CALLING_END        : return new MCNInboundEnd();
            case MCNEventType::ON_IN_CALLING_ANSWERED   : return new MCNInboundAnswered();
            case MCNEventType::ON_OUT_CALLING_START     : return new MCNOutboundStart();
            case MCNEventType::ON_OUT_CALLING_END       : return new MCNOutboundEnd();
            case MCNEventType::ON_OUT_CALLING_ANSWERED  : return new MCNOutboundAnswered();
            case MCNEventType::ON_IN_CALLING_MISSED     : return new MCNInboundMissed();
            case MCNEventType::ON_CLOSE_INCOMING_NOTICE : return new MCNCloseIncompletedNotice();
            case MCNEventType::ON_OUT_CALLING_MISSED    : return new MCNOutboundMissed();
            default :
                throw new \Exception('Unknown mcn notification');
        }
    }
    
    public function process() {
        if (!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) {
            return;        
        }
        
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    protected function getSourceUUId() {
        return MCNAbstractNotification::SOURCE_ID_PREFIX . $this->get('call_id') . "_" . $this->get('abon');
    }
    
    public function validateNotification() {
        $crmToken = \Settings_SPVoipIntegration_Record_Model::getMCNCrmToken();
        if ($this->get('secret') != $crmToken) {
            throw new \Exception("Invalid crm token");
        }
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_mcn_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
    
    public static function getActiveCallsByCallid($callId) {
        $db = \PearDatabase::getInstance();
        $callModels = array();
        $query = "SELECT pbxmanagerid FROM vtiger_pbxmanager INNER JOIN vtiger_crmentity "
                . "ON vtiger_pbxmanager.pbxmanagerid=vtiger_crmentity.crmid "
                . "WHERE vtiger_crmentity.deleted=0 AND vtiger_pbxmanager.sourceuuid LIKE '%" . $callId . "%' "
                . "AND vtiger_pbxmanager.callstatus=?";
        
        $res = $db->pquery($query, array('ringing'));
        
        if ($res) {
            while ($resRow = $db->fetchByAssoc($res)) {
                $callModels[] = \Vtiger_Record_Model::getInstanceById($resRow['pbxmanagerid']);
            }
        }
        return $callModels;
    }
    
    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }
    
}
