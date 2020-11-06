<?php

namespace SPVoipIntegration\yandex\notifications;

class OutgoingCallConnected extends AbstractYandexNotification{
    
    protected $fieldsMapping = array(
        'callstatus' => 'callstatus',
        'totalduration' => 'totalduration'
    );
    
    protected function prepareNotificationModel() {
        $this->doConnectedActions();
    }

    protected function canCreatePBXRecord() {
        return false;
    }

}
