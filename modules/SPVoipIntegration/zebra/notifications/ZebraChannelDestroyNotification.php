<?php
namespace SPVoipIntegration\zebra\notifications;

class ZebraChannelDestroyNotification extends AbstractZebraNotification {

    protected $fieldsMapping = array(
        'totalduration' => 'totalduration',
        'billduration' => 'billduration',
        'callstatus' => 'answer_state',
        'endtime' => 'endtime',
    );
    
    protected function canCreatePBXRecord() {
        return false;
    }
    
    protected function prepareNotificationModel() {
        $notificationTimeStamp = $this->args['Timestamp'];
        $currentTotalDuration = $this->pbxManagerModel->get('totalduration');
        $startTime = $this->pbxManagerModel->get('starttime');
        $startTimestamp = strtotime($startTime);
        $totalDiff = $notificationTimeStamp - $startTimestamp;
                
        if ($totalDiff > 0) {
            $this->set('totalduration', $totalDiff);
        }       
        $billDurationDiff = $totalDiff - $currentTotalDuration;
        $billDuration = ($billDurationDiff > 0) ? $billDurationDiff : 0;
        $this->set('billduration', $billDuration);
        $this->set('endtime', date('Y-m-d H:i:s', $notificationTimeStamp));
        $hangupCause = $this->args['Hangup-Cause'];
        switch ($hangupCause) {
            case 'NORMAL_CLEARING' :
                $callStatus = 'completed';
                break;
            case 'USER_BUSY' :
                $callStatus = 'busy';
                break;
            case 'NO_USER_RESPONSE' :
                $callStatus = 'no-answer';
                break;
            case 'ORIGINATOR_CANCEL' :
                $callStatus = 'no-answer';
                break;
            default :
                $callStatus = $hangupCause;
        }
        $this->set('answer_state', $callStatus);                        
    }
}
