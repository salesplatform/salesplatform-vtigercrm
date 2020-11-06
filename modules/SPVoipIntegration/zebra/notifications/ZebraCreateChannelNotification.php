<?php
namespace SPVoipIntegration\zebra\notifications;

use SPVoipIntegration\integration\AbstractNotification;

class ZebraCreateChannelNotification extends AbstractZebraNotification {
    
    protected $fieldsMapping = array(
        'starttime' => 'starttime',
        'direction' => 'direction',
        'sp_called_from_number' => 'caller_number',
        'sp_called_to_number' => 'callee_number',
        'user' => 'user',
        'sourceuuid' => 'call_id',
        'sp_voip_provider' => 'sp_voip_provider',
        'callstatus' => 'answer_state',                                
        'customernumber' => 'customernumber',
    );
    
    protected function canCreatePBXRecord() {
        return true;
    }

    protected function getUserPhoneNumber() {
        return $this->get('user_number');
    }
    
    protected function prepareNotificationModel() {
        $callTimeStamp = $this->args['Timestamp'];
        $this->set('starttime', date('Y-m-d H:i:s', $callTimeStamp));        
        $this->set('caller_number', $this->args['Caller-ID-Number']);
        $this->set('callee_number', $this->args['Callee-ID-Number']);
        
        $direction = $this->args['Call-Direction'];
        
        //TODO find correct sip number
        $userModel = AbstractNotification::getUserByNumber($this->args['Caller-ID-Number']);
        if ($userModel) {            
            $this->set('user_number', $this->args['Caller-ID-Number']);
            $this->set('customernumber', $this->args['Callee-ID-Number']);
        } else {            
            $this->set('user_number', $this->args['Callee-ID-Number']);
            $this->set('customernumber', $this->args['Caller-ID-Number']);
        }
        $this->set('direction', $direction);
        $this->set('call_id', $this->args['Call-ID']);
        $this->set('answer_state', 'ringing');
        parent::prepareNotificationModel();
    }

    protected function getCustomerPhoneNumber() {
        return $this->get('customernumber');
    }

}
