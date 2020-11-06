<?php
namespace SPVoipIntegration\uiscom\notifications;

class UIScomNotifyEnd extends UIScomNotifyEndRing {                
        
    public function prepareNotificationModel() {
        $this->set('endtime', date('Y-m-d H:i:s'));        
        $totalDuration = $this->pbxManagerModel->get('totalduration'); 
        $billDuration = $this->get('talk_time_duration');       
        if ($this->get('is_lost') === 'true'){
            $callStatus = 'no-answer';
        } else {
            $callStatus = 'completed';
        }

        $this->set('disposition', $callStatus);

        if ($billDuration > 0) {
            $totalDuration += $billDuration;
        }
                
        $this->set('billduration', $billDuration);
        $this->set('duration', $totalDuration);
    }
}