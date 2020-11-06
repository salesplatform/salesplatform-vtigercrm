<?php
namespace SPVoipIntegration\uiscom\notifications;

class UIScomNotifyLost extends UIScomNotifyEndRing {                
    
    public function prepareNotificationModel() {
        $this->set('endtime', date('Y-m-d H:i:s'));        
        $totalDuration = $this->pbxManagerModel->get('totalduration');        
        $billDuration = $this->get('duration');        
        $callStatus = 'no-answer';
        $this->set('disposition', $callStatus);
        
        if ($this->get('disposition') == 'answered') {
            $this->set('disposition', 'completed');
        }

        if ($billDuration > 0) {
            $totalDuration += $billDuration;
        }
                
        $this->set('billduration', $billDuration);
        $this->set('duration', $totalDuration);
    }
}