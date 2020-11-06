<?php
namespace SPVoipIntegration\integration;

abstract class AbstractNotification extends \Vtiger_Base_Model {
    
    public abstract function process();
    public abstract function validateNotification();
    protected abstract function getNotificationDataMapping();
    protected abstract function prepareNotificationModel();
    protected abstract function getCustomerPhoneNumber();
    protected abstract function getUserPhoneNumber();
    protected abstract function getSourceUUId();
    
    protected $pbxManagerModel = null;
    
    public function __construct($values = array()) {
        parent::__construct($values);
        $sourceUUID = $this->getSourceUUId();
        $this->pbxManagerModel = \PBXManager_Record_Model::getInstanceBySourceUUID($sourceUUID);
        $pbxManagerId = $this->pbxManagerModel->get('pbxmanagerid');
        if (!empty($pbxManagerId)) {
            $this->pbxManagerModel = \Vtiger_Record_Model::getInstanceById($pbxManagerId);
        }
    }
    
    public static function getUserByNumber($number) {
        $db = \PearDatabase::getInstance();

        $result = $db->pquery("SELECT id FROM vtiger_users WHERE phone_crm_extension=?", array($number));
        $userModel = null;
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $userModel = \Users_Record_Model::getInstanceById($resRow['id'], 'Users');
        }
        return $userModel;
    }
    
    /**
     * Some providers may send phone number like "+7911...", "7911..."
     * We try to find customer with + in number or without
     * in case, when number starts with "8..." we do not try to find equals "+7" or other country codes, only direct "8..." 
     */
    public function getCustomerByPhone() {
        $customerModel = null;
        $customerPhone = $this->getCustomerPhoneNumber();
        if (!empty($customerPhone)) {
            
            $customerModel = $this->getAccountByPhone($customerPhone);
            if ($customerModel == null) {
                $customerModel = $this->getContactByPhone($customerPhone);
            }
            if ($customerModel == null) {
                $customerModel = $this->getLeadByPhone($customerPhone);
            }
        }
        return $customerModel;
    }
    
    protected function getVoipRecordModelFromNotificationModel() {
        $this->prepareNotificationModel();
        $dataMapping = $this->getNotificationDataMapping();
 
        if ($this->pbxManagerModel != null && $this->pbxManagerModel->getId()) {
            $recordModel = $this->pbxManagerModel;
            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = \Vtiger_Record_Model::getCleanInstance("PBXManager");
        }

        foreach ($dataMapping as $voipFieldName => $notificationFieldName) {
            $recordModel->set($voipFieldName, $this->get($notificationFieldName));
        }

        $customerModel = $this->getCustomerByPhone();
        if ($customerModel != null) {
            $recordModel->set('customer', $customerModel->getId());
            $recordModel->set('customertype', $customerModel->getModuleName());
        }

        return $recordModel;
    }

    private function getAccountByPhone($customerPhone) {
        $db = \PearDatabase::getInstance();
        $accountModel = null;
        $plusPhone = '+' . $customerPhone;
        $sql = "SELECT accountid FROM vtiger_account INNER JOIN vtiger_crmentity "
                . "ON vtiger_crmentity.crmid=vtiger_account.accountid "
                . "WHERE vtiger_crmentity.deleted=0 AND (vtiger_account.phone IN (?,?) OR vtiger_account.otherphone IN (?, ?))";

        $result = $db->pquery($sql, array($customerPhone, $plusPhone, $customerPhone, $plusPhone));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $accountModel = \Vtiger_Record_Model::getInstanceById($resRow['accountid']);
        }

        return $accountModel;
    }

    private function getContactByPhone($customerPhone) {
        $db = \PearDatabase::getInstance();
        $contactModel = null;
        $plusPhone = '+' . $customerPhone;
        $sql = "SELECT contactid FROM vtiger_contactdetails "
                . "INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid=vtiger_contactsubdetails.contactsubscriptionid "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid "
                . "WHERE vtiger_crmentity.deleted=0 AND (vtiger_contactdetails.phone IN (?,?) "
                . "OR vtiger_contactsubdetails.homephone IN (?,?) OR vtiger_contactdetails.mobile IN (?,?) OR vtiger_contactsubdetails.otherphone IN (?,?))";

        $result = $db->pquery($sql, array($customerPhone, $plusPhone, 
                                            $customerPhone, $plusPhone, 
                                            $customerPhone, $plusPhone, 
                                            $customerPhone, $plusPhone
                ));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $contactModel = \Vtiger_Record_Model::getInstanceById($resRow['contactid']);
        }

        return $contactModel;
    }

    private function getLeadByPhone($customerPhone) {
        $db = \PearDatabase::getInstance();
        $leadModel = null;
        $plusPhone = '+' . $customerPhone;
        $sql = "SELECT leadaddressid FROM vtiger_leadaddress INNER JOIN vtiger_crmentity "
                . "ON vtiger_crmentity.crmid=vtiger_leadaddress.leadaddressid "
                . "WHERE vtiger_crmentity.deleted=0 AND (vtiger_leadaddress.phone IN (?,?) OR vtiger_leadaddress.mobile IN (?,?))";

        $result = $db->pquery($sql, array($customerPhone, $plusPhone, $customerPhone, $plusPhone));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $leadModel = \Vtiger_Record_Model::getInstanceById($resRow['leadaddressid']);
        }

        return $leadModel;
    }

}
