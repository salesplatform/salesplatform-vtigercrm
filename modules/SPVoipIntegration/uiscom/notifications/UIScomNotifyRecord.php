<?php
namespace SPVoipIntegration\uiscom\notifications;

use SPVoipIntegration\ProvidersEnum;

class UIScomNotifyRecord extends UIScomNotification {

    protected $dataMapping = array(
        'sp_recordingurl' => 'sp_recordingurl',
        'recordingurl' => 'recordingurl',
        'sourceuuid' => 'call_session_id',
        'sp_is_recorded' => 'sp_is_recorded',
    );

    protected function prepareNotificationModel() {
        $this->set('sp_is_recorded', 1);
        $fileLink = $this->get('file_link');
        if(!empty($fileLink)) {
            $this->downloadToLocal($this->get('file_link'));
        }
    }

    protected function getNotificationDataMapping() {
        return $this->dataMapping;
    }

    protected function getCustomerPhoneNumber() {
        return;
    }

    protected function canCreatePBXRecord() {
        return false;
    }
    
    private function downloadToLocal($link) {
        $soundFileContent = file_get_contents($link);
        if($soundFileContent === false) {
            return;
        }
        
        $filePath = "storage/" . ProvidersEnum::UISCOM . "_{$this->getSourceUUId()}.mp3";
        $status = file_put_contents($filePath, $soundFileContent);
        if($status === false) {
            return;
        }
        
        $this->set('recordingurl', $filePath);
        $this->set('sp_is_local_cached', 1);
        $this->set('sp_recordingurl', $link);
        $this->dataMapping['sp_is_local_cached'] = 'sp_is_local_cached';
    }
}