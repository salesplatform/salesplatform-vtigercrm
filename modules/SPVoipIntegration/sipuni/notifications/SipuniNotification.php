<?php

namespace SPVoipIntegration\sipuni\notifications;

use SPVoipIntegration\integration\AbstractNotification;
use SPVoipIntegration\ProvidersEnum;

abstract class SipuniNotification extends AbstractNotification {

    protected $fieldsMapping = array();

    protected abstract function canCreatePBXRecord();

    protected $user_number;

    protected function getSourceUUId() {
        return $this->get('call_id');
    }

    public function __construct($values = array()) {
        parent::__construct($values);
        $sourceUUID = $this->getSourceUUId();
        $this->pbxManagerModel = $this->getInstanceBySourceUUID($sourceUUID);
        $pbxManagerId = $this->pbxManagerModel->get('pbxmanagerid');
        if (!empty($pbxManagerId)) {
            $this->pbxManagerModel = \Vtiger_Record_Model::getInstanceById($pbxManagerId);
        }
    }

    public function getInstanceBySourceUUID($sourceuuid) {
        $db = \PearDatabase::getInstance();
        $record = new \PBXManager_Record_Model();
        if ($this->get('src_type') == '2') {
            $query = "SELECT * FROM vtiger_pbxmanager WHERE sourceuuid=? and sp_called_from_number=?";
            $result = $db->pquery($query, array($sourceuuid, $this->get('short_src_num')));
        } else {
            $query = "SELECT * FROM vtiger_pbxmanager WHERE sourceuuid=? and sp_called_to_number=?";
            $result = $db->pquery($query, array($sourceuuid, $this->get('short_dst_num')));
        }
        $rowCount = $db->num_rows($result);
        if ($rowCount) {
            $rowData = $db->query_result_rowdata($result, 0);
            $record->setData($rowData);
        }
        return $record;
    }

    public function process() {
        if ($this->get('dst_type') === "1" && $this->get('src_type') === "1") {
            $this->updateRelatedCalls();
            $this->sendResponse();
            return;
        }
        if ((!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord())) {
            return;
        }

        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
        $this->sendResponse();
    }

    public function sendResponse() {
        header('Content-type: application/json');
        $text = array();
        $text['success'] = true;
        $response = json_encode($text);
        echo $response;
    }

    public function validateNotification() {
        return true;
    }

    public static function getInstance($requestData) {
        $event = $requestData['event'];
        switch ($event) {
            case SipuniEventType::CALL:
                return new SipuniNotifyCall($requestData);
            case SipuniEventType::HANG_UP:
                return new SipuniNotifyHangUp($requestData);
            case SipuniEventType::ANSWER:
                return new SipuniNotifyAnswer($requestData);
            case SipuniEventType::SEC_HANG_UP:
                return new SipuniNotifySecHangUp($requestData);
            default:
                throw new \Exception("Unknown notification type");
        }
    }

    protected function getUserPhoneNumber() {
        return $this->user_number;
    }

    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('user', '');
        $this->set('sp_voip_provider', ProvidersEnum::SIPUNI);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }

    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_sipuni_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }

    protected function updateRelatedCalls() {
        $db = \PearDatabase::getInstance();
        $time = date('Y-m-d H:i:s');
        $query = "UPDATE vtiger_pbxmanager SET callstatus='no-answer', endtime =?"
                . "WHERE sourceuuid =? and callstatus = 'ringing'";
        $db->pquery($query, array($time, $this->get("call_id")));
    }

}
