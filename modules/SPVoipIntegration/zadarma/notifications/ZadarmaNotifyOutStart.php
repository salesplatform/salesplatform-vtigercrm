<?php
namespace SPVoipIntegration\zadarma\notifications;

class ZadarmaNotifyOutStart extends ZadarmaNotification {    
    
    protected $fieldsMapping = array(
        'starttime' => 'call_start',
        'direction' => 'direction',
        'sp_called_from_number' => 'internal',
        'sp_called_to_number' => 'destination',
        'user' => 'sp_user',
        'sourceuuid' => 'sourceuuid',
        'sp_voip_provider' => 'sp_voip_provider',
        'callstatus' => 'disposition',
        'totalduration' => 'duration',
        'sp_is_recorder' => 'is_recorded',
        'sp_recorded_call_id' => 'call_id_with_rec',
        'sp_call_status_code' => 'status_code',
        'sp_billduration' => 'duration',
        'customernumber' => 'destination',
    );
    
    public function getValidationString() {
        return $this->get('internal') . $this->get('destination') . $this->get('call_start');
    }
    
    public function process() {
        $userNumbers = \PBXManager_Record_Model::getUserNumbers();       
        if (in_array($this->get('destination'), $userNumbers)) {
            return;
        }
        parent::process();
    }

    protected function prepareNotificationModel() {
        parent::prepareNotificationModel();
        
        $this->set('sourceuuid', $this->getSourceUUId());
        $this->set('direction', 'outbound');
        if ($this->get('disposition') == '') {
            $this->set('disposition', 'ringing');
        }
    }
    
    public function validateNotification() {
        parent::validateNotification();
        $userModel = $this->getUserByNumber($this->get('internal'));
        if ($userModel == null) {
            throw new \Exception('No need handle notification');
        }
    }
    
    protected function getCustomerPhoneNumber() {
        return $this->get('destination');
    }

    protected function canCreatePBXRecord() {
        return true;
    }

}