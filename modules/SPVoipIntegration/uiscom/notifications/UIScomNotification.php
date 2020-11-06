<?php
namespace SPVoipIntegration\uiscom\notifications;

use SPVoipIntegration\integration\AbstractNotification;

abstract class UIScomNotification extends AbstractNotification {
       
    const UISCOM_NOTIFICATION_OUT_START = "\"Исходящее плечо\"";
    const UISCOM_NOTIFICATION_ANSWER = "\"Начало разговора\"";
    const UISCOM_NOTIFICATION_END = "\"Завершение звонка\"";
    const UISCOM_NOTIFICATION_LOST = "\"Потерянный звонок\"";
    const UISCOM_NOTIFICATION_RECORD = "\"Записанный разговор\"";
    
    protected $fieldsMapping = array();
        
    protected abstract function canCreatePBXRecord();
    
    protected function getSourceUUId() {
        return $this->get('call_session_id');
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
    
    public function process() {
        if ((!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) ) {
            return;        
        }
        
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();        
    }
    
    public function validateNotification() {
        return true;
    }        
    
    public static function getInstance($requestData) {
        $type = $requestData['notification_name'];
        switch ($type) {
            case self::UISCOM_NOTIFICATION_OUT_START:
                return new UIScomNotifyOutStart($requestData);
            case self::UISCOM_NOTIFICATION_ANSWER:
                return new UIScomNotifyAnswer($requestData);
            case self::UISCOM_NOTIFICATION_END:
                return new UIScomNotifyEnd($requestData);
            case self::UISCOM_NOTIFICATION_LOST:
                return new UIScomNotifyLost($requestData);
            case self::UISCOM_NOTIFICATION_RECORD:
                return new UIScomNotifyRecord($requestData);
            default:
                throw new \Exception("Unknown notification type");
        }
    }
       
    protected function getUserPhoneNumber() {
        if ($this->get('direction') == "in"){
            return $this->get('called_phone_number');
        }
        else{
            return $this->get('calling_phone_number');
        }   
    }       
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    protected function prepareNotificationModel() {
        $this->set('user', '');
        $this->set('sp_voip_provider', 'uiscom');
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('sp_user', $userModel->getId());
        }
        $notificationFieldName = str_replace(['"', "\\"], "", $this->get('start_time'));
        $this->set('start_time', $notificationFieldName);
    }
    
    public function getInstanceBySourceUUID($sourceuuid, $number = NULL){
        $db = \PearDatabase::getInstance(); 
        $record = new \PBXManager_Record_Model();
        if (!empty($number)){
            $query = "SELECT * FROM vtiger_pbxmanager WHERE sourceuuid=? and sp_called_to_number=?"; 
            $result = $db->pquery($query, array($sourceuuid, $number));
        } else {
            $query = "SELECT * FROM vtiger_pbxmanager WHERE sourceuuid=? and callstatus != 'no-answer'";
            $result = $db->pquery($query, array($sourceuuid));
        }
        $rowCount =  $db->num_rows($result);
        if($rowCount){
            $rowData = $db->query_result_rowdata($result, 0);
            $record->setData($rowData);
        }
        return $record;
    }
}    