<?php

namespace SPVoipIntegration\yandex\notifications;

class OutgoingCall extends AbstractYandexNotification{
    
    protected $fieldsMapping = array(
        'starttime' => 'starttime',
        'direction' => 'direction',
        'sp_called_from_number' => 'from_number',
        'sp_called_to_number' => 'to_number',
        'user' => 'user',
        'sourceuuid' => 'sourceuuid',
        'sp_voip_provider' => 'sp_voip_provider',
        'callstatus' => 'callstatus',        
        'customernumber' => 'to_number',        
    );
    
    protected function getCustomerPhoneNumber() {
        $body = $this->get('Body');
        return $body['To'];
    }    

    protected function getUserPhoneNumber() {
        $body = $this->get('Body');
        return $body['Extension'];
    }

    protected function prepareNotificationModel() {
        $this->set('direction', 'outbound');
        $this->set('from_number', $this->get('Body')['From']);
        $this->set('to_number', $this->get('Body')['To']);
        $this->doRingingActions();
    }

    protected function canCreatePBXRecord() {
        return true;
    }

}
