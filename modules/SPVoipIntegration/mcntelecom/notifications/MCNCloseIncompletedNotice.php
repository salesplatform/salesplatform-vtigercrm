<?php
namespace SPVoipIntegration\mcntelecom\notifications;

class MCNCloseIncompletedNotice extends MCNAbstractNotification{
    
    protected $fieldsMapping = array(
        'callstatus' => 'callstatus',
        'totalduration' => 'totalduration'
    );
    
    protected function canCreatePBXRecord() {
        return false;
    }

    protected function prepareNotificationModel() {
        $currentTimestamp = time();
        $callStartDateTime = $this->pbxManagerModel->get('starttime');
        $diff = $currentTimestamp - strtotime($callStartDateTime);
        $totalDuration = 0;
        if ($diff > 0) {
            $totalDuration = $diff;
        }
        $this->set('totalduration', $totalDuration);
        $this->set('callstatus', 'no-answer');        
    }


}
