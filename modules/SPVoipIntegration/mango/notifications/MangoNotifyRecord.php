<?php

namespace SPVoipIntegration\mango\notifications;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\ProvidersEnum;

class MangoNotifyRecord extends MangoNotification {

    protected $fieldsMapping = array(
        'sp_recorded_call_id' => 'recording_id',
        'sp_recordingurl' => 'sp_recordingurl',
        'recordingurl' => 'recordingurl',
    );
    


    protected function canCreatePBXRecord() {
        return false;
    }

    protected function prepareNotificationModel() {
        if ($this->get('recording_state') == 'Completed') {
                $factory = AbstractCallManagerFactory::getEventsFacory(ProvidersEnum::MANGO);
                $callApiManager = $factory->getCallApiManager();
                $link = $callApiManager->recieveRecord($this->get('recording_id'));
                $this->set('sp_recordingurl', $link);
                $this->downloadToLocal($link);
        }
    }

    private function downloadToLocal($link) {
        $soundFileContent = file_get_contents($link);
        if ($soundFileContent === false) {
            return;
        }

        $filePath = $this->generateSoundFileName();
        $status = file_put_contents($filePath, $soundFileContent);
        if ($status === false) {
            return;
        }

        $this->set('recordingurl', $filePath);
        $this->set('sp_is_local_cached', 1);
        $this->dataMapping['sp_is_local_cached'] = 'sp_is_local_cached';
    }

    private function generateSoundFileName() {
        return "storage/" . ProvidersEnum::MANGO . "_{$this->getSourceUUId()}.mp3";
    }

    protected function getCustomerPhoneNumber() {
        
    }

}
