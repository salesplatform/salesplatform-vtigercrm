<?php
namespace SPVoipIntegration\zebra\notifications;

class ZebraChannelAnswerNotification extends AbstractZebraNotification {
    
    protected $fieldsMapping = array(
        'totalduration' => 'totalduration',
        'callstatus' => 'answer_state',
    );

    protected function canCreatePBXRecord() {
        return false;
    }
    
    protected function prepareNotificationModel() {
        $notificationTimeStamp = $this->args['Timestamp'];
        $startTime = $this->pbxManagerModel->get('starttime');
        $startTimestamp = strtotime($startTime);
        $diff = $notificationTimeStamp - $startTimestamp;
        if ($diff > 0) {
            $this->set('totalduration', $diff);
        }
        $this->set('answer_state', 'in-progress');        
    }

}
