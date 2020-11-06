<?php
namespace SPVoipIntegration\zadarma\notifications;

class ZadarmaNotifyEnd extends ZadarmaNotifyStart {                
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'billduration' => 'billduration',
        'totalduration' => 'duration',
        'callstatus' => 'disposition',
        'sp_call_status_code' => 'status_code',
        'sp_is_recorded' => 'is_recorded',
        'sp_recorded_call_id' => 'call_id_with_rec'
    );    

    public function prepareNotificationModel() {
        $this->set('endtime', date('Y-m-d H:i:s'));        
        $totalDuration = $this->pbxManagerModel->get('totalduration');        
        $billDuration = $this->get('duration');        
        $callStatus = $this->get('disposition');
        switch ($callStatus) {
            case 'answered' :
                $callStatus = 'completed';
                break;
            case 'no answer' :
                $callStatus = 'no-answer';
                break;
            case 'busy' :
                $callStatus = 'busy';
                break;
            case 'cancel' :
                $callStatus = 'no-answer';
                break;
        }
        
        $this->set('disposition', $callStatus);

        if ($billDuration > 0) {
            $totalDuration += $billDuration;
        }
                
        $this->set('billduration', $billDuration);
        $this->set('duration', $totalDuration);
    }
    
    public function process() {        
        $this->closeOtherCalls();
        parent::process();
    }

    protected function canCreatePBXRecord() {
        return false;
    }
    
    public function validateNotification() {
        return ZadarmaNotification::validateNotification();        
    }

}