<?php
namespace SPVoipIntegration\uiscom\notifications;

class UIScomNotifyAnswer extends UIScomNotification {
    
    protected $fieldsMapping = array(
        'callstatus' => 'disposition',
        'totalduration' => 'duration'
    );
    
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

}