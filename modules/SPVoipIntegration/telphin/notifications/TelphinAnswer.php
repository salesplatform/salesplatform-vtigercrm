<?php
namespace SPVoipIntegration\telphin\notifications;


class TelphinAnswer extends TelphinAbstractNotification{
    
    protected $fieldsMapping = array(
        'callstatus' => 'callstatus',
        'totalduration' => 'duration'
    );

    protected function prepareNotificationModel() {
        $this->set('callstatus', 'in-progress');
        $notificationTimestamp = $this->get('EventTime');
        $notificationTimestamp = $notificationTimestamp / 1000000;
        
        $startDatetime = $this->pbxManagerModel->get('starttime');
        $diff = $notificationTimestamp - strtotime($startDatetime);
        $this->set('duration', $diff > 0 ? $diff : 0);
    }

    protected function canCreatePBXRecord() {
        return false;
    }

}
