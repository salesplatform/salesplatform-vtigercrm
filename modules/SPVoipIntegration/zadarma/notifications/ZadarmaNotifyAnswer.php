<?php
namespace SPVoipIntegration\zadarma\notifications;

class ZadarmaNotifyAnswer extends ZadarmaNotification {
    
    protected $fieldsMapping = array(
        'callstatus' => 'disposition',
        'totalduration' => 'duration'
    );

    public function getValidationString() {
        return $this->get('caller_id') . $this->get('destination') . $this->get('call_start');
    }
    
    protected function prepareNotificationModel() {   
        $this->set('disposition', 'in-progress');
        
        $time = time();
        $createdtime = strtotime($this->pbxManagerModel->get('createdtime'));
        $timeDiff = $time - $createdtime;
        $this->set('duration', $timeDiff > 0 ? $timeDiff : 0);       
    }

    protected function getCustomerPhoneNumber() {
        return '';
    }

    protected function getUserPhoneNumber() {
        return '';
    }

    protected function canCreatePBXRecord() {
        return false;
    }
    
    public function process() {
        $this->closeOtherCalls();
        parent::process();
    }        

}