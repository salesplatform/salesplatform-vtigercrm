<?php
namespace SPVoipIntegration\sipuni\notifications;

class SipuniNotifyAnswer extends SipuniNotification {
    
    protected $fieldsMapping = array(
        'callstatus' => 'callstatus'
    );
    
    protected function prepareNotificationModel() {
        
        $this->set('callstatus', 'answered');
        $this->updateRelatedCalls();
    }
    
    protected function canCreatePBXRecord() {
        return false;
    }
    
    protected function getCustomerPhoneNumber() {
    }

}

