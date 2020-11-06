<?php

namespace SPVoipIntegration\mango\notifications;

use SPVoipIntegration\integration\AbstractNotification;
use SPVoipIntegration\ProvidersEnum;
use SPVoipIntegration\ProvidersErrors;

abstract class MangoNotification extends AbstractNotification {

    protected $fieldsMapping = array();

    protected abstract function canCreatePBXRecord();

    protected $sp_user;

    protected function getSourceUUId() {
        $extension = $this->get('extension');
        if (empty($extension)) {
            $to = (array) $this->get('to');
            if (array_key_exists('extension', $to)) {
                $extension = $to['extension'];
            } else {
                $from = (array) $this->get('from');
                $extension = $from['extension'];
            }
        }
        $sourceUUID = $this->get('entry_id') . $extension;
        return $sourceUUID;
    }

    public function process() {
        if ((!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) || $this->get('location') == 'ivr' || $this->get('location') == 'queue') {
            return;
        }

        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }

    public function validateNotification() {
        $mangoSecret = \Settings_SPVoipIntegration_Record_Model::getMangoSecret();
        if (empty($mangoSecret)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
        $mangoKey = $this->get('vpbx_api_key');
        $signature = $this->get('sign');
        $json = $this->get('json');
        $validateString = $mangoKey . $json . $mangoSecret;
        $signatureTest = hash('sha256', $validateString);

        if ($signature != $signatureTest) {
            throw new \Exception("Wrong signature", ProvidersErrors::WRONG_SIGNATURE);
        }
        return true;
    }

    public static function getInstance($requestData) {
        $uri = array();
        preg_match('@/events/[a-z]+$@', $_SERVER['REQUEST_URI'], $uri);
        switch ($uri[0]) {
            case MangoEventType::CALL:
                return new MangoNotifyCall($requestData);
            case MangoEventType::RECORD:
                return new MangoNotifyRecord($requestData);
            default:
                throw new \Exception("Unknown notification type");
        }
    }

    protected function getUserPhoneNumber() {
        return $this->sp_user;
    }

    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('user', '');
        $this->set('sp_voip_provider', ProvidersEnum::MANGO);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('sp_user', $userModel->getId());
        }
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_mango_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }

}
