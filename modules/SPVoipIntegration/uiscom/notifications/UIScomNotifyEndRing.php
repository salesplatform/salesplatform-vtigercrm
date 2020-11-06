<?php
namespace SPVoipIntegration\uiscom\notifications;

class UIScomNotifyEndRing extends UIScomNotification {                
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'billduration' => 'talk_time_duration',
        'totalduration' => 'duration',
        'callstatus' => 'disposition',
        'sp_call_status_code' => 'status_code',
        'sp_is_recorded' => 'is_recorded',
        'sp_recorded_call_id' => 'call_id_with_rec'
    ); 
    
    protected function canCreatePBXRecord() {
        return false;
    }
    
    protected function getCustomerPhoneNumber() {
        return $this->get('calling_phone_number');
}
}