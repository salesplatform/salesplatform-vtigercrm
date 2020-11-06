<?php

namespace SPVoipIntegration\mcntelecom\notifications;

class MCNInboundMissed extends MCNAbstractNotification{
    
    public function process() {
        $callId = $this->get('call_id');
        $currentTimestamp = time();
        $callModels = MCNAbstractNotification::getActiveCallsByCallid($callId);
        foreach ($callModels as $pbxModel) {
            $pbxModel->set('mode', 'edit');
            $pbxModel->set('callstatus', 'no-answer');
            $starttime = $pbxModel->get('starttime');            
            $diff = $currentTimestamp - strtotime($starttime);
            if ($diff > 0) {
                $pbxModel->set('totalduration', $diff);
            }
            $pbxModel->set('endtime', date('Y-m-d H:i:s'));
            $pbxModel->save();
        }
    }
    
    
    protected function canCreatePBXRecord() {
        return false;
    }

    protected function prepareNotificationModel() {
        
    }

}
