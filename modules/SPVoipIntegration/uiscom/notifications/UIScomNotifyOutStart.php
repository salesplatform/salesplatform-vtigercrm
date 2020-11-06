<?php
namespace SPVoipIntegration\uiscom\notifications;

class UIScomNotifyOutStart extends UIScomNotification {    
       
    private $canCreate = true;
    
    protected $fieldsMapping = array(
        'starttime' => 'start_time',
        'direction' => 'direction',
        'sp_called_from_number' => 'calling_phone_number',
        'sp_called_to_number' => 'called_phone_number',
        'user' => 'sp_user',
        'sourceuuid' => 'call_session_id',
        'sp_voip_provider' => 'sp_voip_provider',
        'callstatus' => 'disposition',
        'totalduration' => 'duration',
        'sp_is_recorder' => 'is_recorded',
        'sp_recorded_call_id' => 'call_id_with_rec',
        'sp_call_status_code' => 'status_code',
        'sp_billduration' => 'talk_time_duration',
        'customernumber' => 'contact_phone_number',
    );
    
    public function process() {
        $userNumbers = \PBXManager_Record_Model::getUserNumbers();       
        if (in_array($this->get('contact_phone_number'), $userNumbers)) {
            return;
        }
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        
        $internalDuplicateFilter = $this->isCRMUIScomExtension($this->get('calling_phone_number'));
        $isMyInternal = $this->checkIsMyInternal();
        $isNotificationCorrect = $this->incorrectOutStartNotificationFilter();
        $internalFilter = $this->internalFilter();
        
        
        
        $isDuplicateInternal = $this->checkMyInternal($this->get('call_session_id'));
        if ($internalDuplicateFilter){
            $this->set('calling_phone_number', $this->get('called_phone_number'));
            $this->set('called_phone_number', $currentUser->get('phone_crm_extension'));
        } elseif ($isDuplicateInternal){
            $callId = $this->getSourceUUId();
            $AlreadyExistPBXManagerModel = $this->getInstanceBySourceUUID($callId, $this->getUserPhoneNumber());
            if ($AlreadyExistPBXManagerModel->getData()){
                 $this->pbxManagerModel = $AlreadyExistPBXManagerModel;
            } else {
                $this->pbxManagerModel = $this->getInstanceBySourceUUID($callId);
            } 
            $this->updateActiveCalls($callId);
            $pbxManagerId = $this->pbxManagerModel->get('pbxmanagerid');
            $this->pbxManagerModel = \Vtiger_Record_Model::getInstanceById($pbxManagerId);
            $this->pbxManagerModel->set('id', NULL);
        }
        
        if ($this->get('call_source') == 'callapi'){
            $this->set('direction', 'outbound');
            $this->set('called_phone_number', $this->get('calling_phone_number'));
            $this->set('calling_phone_number', $currentUser->get('phone_crm_extension'));   
        }
        
        if ($isMyInternal && $internalFilter  && $isNotificationCorrect){
            parent::process();
        }
    }
    
    private function checkMyInternal($sourceuuid){
        $pBXManagerModel = $this->getInstanceBySourceUUID($sourceuuid);
        if (!$pBXManagerModel){
            return false;
        }
        $curentCalledNumber = $pBXManagerModel->get('sp_called_from_number');
        
        if (($this->get('direction') == 'in') && ($curentCalledNumber == $this->get('calling_phone_number'))){
            return true;
        }
        return false;
    }
    

    
    public static function updateActiveCalls($callId) {
        $db = \PearDatabase::getInstance();        
        $query = "UPDATE vtiger_pbxmanager SET callstatus='no-answer'"
                . "WHERE sourceuuid =? and callstatus != 'no-answer'";
        $db->pquery($query, array($callId));
    }
    
    private function internalFilter(){
        return ($this->get('call_source') != 'sip');
    }
    
    private function incorrectOutStartNotificationFilter(){
        return ($this->get('called_phone_number') != '{{extension_phone_number}}');
    }

    private function checkIsMyInternal(){
        $internalDuplicateFilter = $this->isCRMUIScomExtension($this->get('calling_phone_number'));
        $currentUser = \Users_Record_Model::getCurrentUserModel(); 
        return !(($internalDuplicateFilter) && ($this->get('called_phone_number') == $currentUser->get('phone_crm_extension')));
    }

    private function isCRMUIScomExtension($spExtensionNumber){
        $db = \PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_uiscom_extension=?", array($spExtensionNumber));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            return true;
        }
        return false;
    }
    
    protected function prepareNotificationModel() {
        parent::prepareNotificationModel();  
        $direction = $this->getDirection();
        
        $this->set('direction', $direction);
        if ($this->get('disposition') == '') {
            $this->set('disposition', 'ringing');
        }
    }
    
    private  function getDirection(){
        $db = \PearDatabase::getInstance(); 
        $query = "SELECT * FROM vtiger_users WHERE phone_crm_extension=?"; 
        $result = $db->pquery($query, array($this->get('called_phone_number')));
        $rowCount =  $db->num_rows($result);
        if($rowCount){
            $this->fieldsMapping['customernumber'] = 'calling_phone_number';
            return "inbound";
        }
        $result = $db->pquery($query, array($this->get('calling_phone_number')));
        $rowCount =  $db->num_rows($result);
        if ($rowCount){
            $this->fieldsMapping['customernumber'] = 'called_phone_number';
            return "outbound";
        }
        $this->canCreate = false;
        return false;
    }


    protected function getCustomerPhoneNumber() {
        if ($this->get('direction') == 'outbound'){
            return $this->get('called_phone_number');
        } else {
            return $this->get('calling_phone_number');
        }
    } 


    protected function canCreatePBXRecord() {
        $this->getDirection();
        return $this->canCreate;
    }

}