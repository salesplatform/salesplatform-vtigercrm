<?php

namespace SPVoipIntegration\telphin\notifications;

use SPVoipIntegration\ProvidersEnum;

class TelphinDialIn extends TelphinAbstractNotification{
    
    protected $fieldsMapping = array(
        'starttime' => 'starttime',
        'direction' => 'direction',
        'sp_called_from_number' => 'CallerIDNum',
        'sp_called_to_number' => 'CalledNumber',
        'user' => 'user',
        'sourceuuid' => 'sourceuuid',
        'sp_voip_provider' => 'sp_voip_provider',
        'callstatus' => 'callstatus',        
        'customernumber' => 'CallerIDNum',
        'sourceuuid' => 'sourceuuid',
    );
    
    protected $direction = 'inbound';

    protected function getCustomerPhoneNumber() {        
        return $this->get('CallerIDNum');
    }

    protected function getUserPhoneNumber() {
        return $this->get('CalledNumber');
    }

    protected function prepareNotificationModel() {        
        $this->set('starttime', $this->getEventDatetime());
        $this->set('direction', $this->direction);
        $this->set('sourceuuid', $this->getSourceUUId());  
        $this->set('sp_voip_provider', ProvidersEnum::TELPHIN);
        $this->set('callstatus', 'ringing');
        
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }

    protected function canCreatePBXRecord() {
        return true;
    }

}
