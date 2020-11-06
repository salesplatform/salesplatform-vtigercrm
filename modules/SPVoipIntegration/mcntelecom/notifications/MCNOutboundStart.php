<?php
namespace SPVoipIntegration\mcntelecom\notifications;

use SPVoipIntegration\ProvidersEnum;

class MCNOutboundStart extends MCNInboundStart{
    
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
    
    protected function prepareNotificationModel() {
        $this->set('direction', 'outbound');
        $this->set('from_number', $this->get('did_mcn'));
        $this->set('to_number', $this->get('did'));
        $this->set('starttime', date('Y-m-d H:i:s'));
        $this->set('callstatus', 'ringing');
        $this->set('sourceuuid', $this->getSourceUUId());
        $this->set('sp_voip_provider', ProvidersEnum::MCN);
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
    }
    
}
