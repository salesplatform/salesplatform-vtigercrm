<?php
namespace SPVoipIntegration\gravitel\notifications;

class GravitelHistoryNotification extends AbstractGraviltelNotification {
    
    const INBOUND_TYPE = 'in';
    const OUTBOUND_TYPE = 'out';
    
    private $dataMapping = array(
        'direction' => 'direction',
        'callstatus' => 'callstatus'
    );
    
    protected function getNotificationDataMapping() {
        return $this->dataMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('sourceuuid', $this->getSourceUUId());
        
        $type = $this->getType();
        $direction = ($type == self::INBOUND_TYPE) ? 'inbound' : 'outbound';
        $this->set('direction', $direction);
        
        $recordingLink = $this->get("link");
        if(!empty($recordingLink)) {
            $this->dataMapping['recordingurl'] = 'recordingurl';
            $this->set('recordingurl', $recordingLink);
            $this->downloadToLocal($recordingLink);
        }
        
        $this->processStatus();
        $this->processDuration();
    }
    
    private function processStatus() {
        $status = $this->get('status');
        if($status == 'Success') {
            $this->set('callstatus', 'completed');
            return;
        }
        
        if($status == 'missed' || $status == 'Cancel') {
            $this->set('callstatus', 'no-answer');
            return;
        }
        
        $this->set('callstatus', 'busy');
    }
    
    
    private function processDuration() {
        $startTime = strtotime($this->get('start'));
        if($startTime) {
            $this->dataMapping['starttime'] = 'starttime';
            $this->set('starttime', date('Y-m-d H:i:s', $startTime));
        }
        
        $duration = $this->get('duration');
        if(is_numeric($duration) && $duration > 0) {
            $this->dataMapping['totalduration'] = 'totalduration';
            $this->set('totalduration', $duration);
        }
        
        if ($this->pbxManagerModel) {
            $billduration = $this->pbxManagerModel->get('billduration');
            if ($billduration > $duration) {
                $this->dataMapping['billduration'] = 'billduration';
                $this->set('billduration', $duration);
            }
        }
    }
    
    private function downloadToLocal($link) {
        $soundFileContent = file_get_contents($link);
        if($soundFileContent === false) {
            return;
        }
        
        $filePath = $this->generateSoundFileName();
        $status = file_put_contents($filePath, $soundFileContent);
        if($status === false) {
            return;
        }
        
        $this->set('recordingurl', $filePath);
        $this->set('sp_is_local_cached', 1);
        $this->dataMapping['sp_is_local_cached'] = 'sp_is_local_cached';
    }
    
    private function generateSoundFileName() {
        return "storage/{$this->getSourceUUId()}.mp3";
    }
}
