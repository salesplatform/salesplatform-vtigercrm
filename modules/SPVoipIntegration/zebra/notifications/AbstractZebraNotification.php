<?php
namespace SPVoipIntegration\zebra\notifications;

use SPVoipIntegration\integration\AbstractNotification;
use SPVoipIntegration\ProvidersEnum;

abstract class AbstractZebraNotification extends AbstractNotification{
    
    protected abstract function canCreatePBXRecord();
    protected $fieldsMapping = array();
    protected $args = array();
    const SOURCE_ID_PREFIX = "zebra_";
    
    public function __construct($values = array()) {
        $values = json_decode(file_get_contents("php://input"), true);
        parent::__construct($values);
        $this->args = $this->get('args');
    }
    
    
    protected function getNotificationDataMapping() {
        return $this->fieldsMapping;
    }
    
    protected function prepareNotificationModel() {
        $this->set('user', '');
        $this->set('sp_voip_provider', ProvidersEnum::ZEBRA);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }

    public function process() {        
        if (!$this->pbxManagerModel->getId() && !$this->canCreatePBXRecord()) {
            return;        
        }             

        $voipModel = $this->getVoipRecordModelFromNotificationModel();    
        $voipModel->save();        
    }

    public function validateNotification() {
        $handleNotification = true;
        $direction = $this->args['Call-Direction'];
        if ($direction == 'inbound') {
            $handleNotification = false;
        }
        if ($handleNotification) {
            $userModel = $this->getUserFromRequest();
            if ($userModel == null) {
                $handleNotification = false;
            }
        }
        
        if (!$handleNotification) {
            throw new \Exception("Unused notification");
        }        
        
    }
    
    public static function getInstance($requestData) {
        $eventName = json_decode(file_get_contents("php://input"))->name;

        switch ($eventName) {
            case ZebraEventType::CREATE :
                return new ZebraCreateChannelNotification($requestData);
            case ZebraEventType::ANSWER :
                return new ZebraChannelAnswerNotification($requestData);
            case ZebraEventType::DESTROY :
                return new ZebraChannelDestroyNotification($requestData);
            default :
                throw new \Exception("Unknown zebra notification");
        }
    }
    
    protected function getUserFromRequest() {
        $request = $this->args['Request'];
        $requestParts = explode('@', $request);
        $sipLogin = $requestParts[0];
        $userModel = $this->getUserBySipLogin($sipLogin);
        return $userModel;
    }
    
    protected function getUserBySipLogin($sipLogin) {
        $db = \PearDatabase::getInstance();
        $userModel = null;
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_zebra_login=?", array($sipLogin));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
        
    }
    
    protected function getSourceUUId() {
        return AbstractZebraNotification::SOURCE_ID_PREFIX . $this->get('args')['Call-ID'];
    }
    
    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }
    
}
