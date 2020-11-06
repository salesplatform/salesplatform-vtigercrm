<?php
namespace SPVoipIntegration\zadarma\notifications;

use SPVoipIntegration\integration\AbstractNotification;
use SPVoipIntegration\ProvidersEnum;
use SPVoipIntegration\ProvidersErrors;

abstract class ZadarmaNotification extends AbstractNotification {
       
    protected $fieldsMapping = array();
    
    public abstract function getValidationString();           
    protected abstract function canCreatePBXRecord();
    
    protected function getSourceUUId() {
        return $this->get('pbx_call_id') . "_" .$this->get('internal');
    }
    
    public function process() {
        if (!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) {
            return;        
        }             
        
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();        
    }

    public function validateNotification() {
        $zadarmaSecret = \Settings_SPVoipIntegration_Record_Model::getZadarmaSecret();
        if (empty($zadarmaSecret)) {
            return true;
        }

        $validationString = $this->getValidationString();
        $signature = $this->getHeader('Signature');
        $signatureTest = base64_encode(hash_hmac('sha1', $validationString, $zadarmaSecret));
        
        if ($signature != $signatureTest) {
            throw new \Exception("Wrong signature", ProvidersErrors::WRONG_SIGNATURE);
        }
        return true;
    }        
    
    public static function getInstance($requestData) {
        $type = $requestData['event'];
        switch ($type) {
            case ZadarmaEventType::OUT_START:
                return new ZadarmaNotifyOutStart($requestData);
            case ZadarmaEventType::START:
                return new ZadarmaNotifyStart($requestData);
            case ZadarmaEventType::INTERNAL:
                return new ZadarmaNotifyInternal($requestData);
            case ZadarmaEventType::ANSWER:
                return new ZadarmaNotifyAnswer($requestData);
            case ZadarmaEventType::END:
                return new ZadarmaNotifyEnd($requestData);
            case ZadarmaEventType::OUT_END:
                return new ZadarmaNotifyOutEnd($requestData);
            case ZadarmaEventType::RECORD:
                return new ZadarmaNotifyRecord($requestData);
            default:
                throw new \Exception("Unknown notification type");
        }
    }
       
    protected function getUserPhoneNumber() {
        return $this->get('internal');
    }       
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    protected function prepareNotificationModel() {
        $this->set('user', '');
        $this->set('sp_voip_provider', ProvidersEnum::ZADARMA);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('sp_user', $userModel->getId());
        }
    }
    
    protected function getHeader($name) {
        $headers = getAllHeaders();
        foreach ($headers as $key => $val) {
            if ($key == $name)
                return $val;
        }
        return null;
    }

    protected function getAllHeaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_zadarma_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
    
    protected function closeOtherCalls() {
        $db = \PearDatabase::getInstance();
        $callId = $this->get('pbx_call_id');
        $res = $db->pquery("UPDATE vtiger_pbxmanager SET callstatus='no-answer' WHERE sourceuuid LIKE '%$callId%' "
                . "AND sourceuuid!=?", array($this->getSourceUUId()));        
    }
}