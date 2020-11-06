<?php
namespace SPVoipIntegration\yandex\notifications;

class IncomingCallStopRinging extends AbstractYandexNotification{    
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'totalduration' => 'totalduration',
        'callstatus' => 'callstatus'
    );

    protected function prepareNotificationModel() {
        $this->doStoppedActions();
    }

    protected function canCreatePBXRecord() {
        return false;
    }

}
