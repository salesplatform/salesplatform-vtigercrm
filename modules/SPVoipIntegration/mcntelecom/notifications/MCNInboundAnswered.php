<?php

namespace SPVoipIntegration\mcntelecom\notifications;

class MCNInboundAnswered extends MCNAbstractNotification{
    
    protected $fieldsMapping = array(
        'callstatus' => 'callstatus',
        'totalduration' => 'totalduration'
    );
    
    protected function prepareNotificationModel() {
        $currentTimestamp = time();
        $callStartDateTime = $this->pbxManagerModel->get('starttime');
        $diff = $currentTimestamp - strtotime($callStartDateTime);
        $totalDuration = 0;
        if ($diff > 0) {
            $totalDuration = $diff;
        }
        $this->set('callstatus', 'in-progress');
        $this->set('totalduration', $totalDuration);
    }

    protected function canCreatePBXRecord() {
        return false;
    }

}
