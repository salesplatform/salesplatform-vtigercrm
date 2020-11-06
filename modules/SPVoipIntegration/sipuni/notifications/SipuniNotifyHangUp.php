<?php

namespace SPVoipIntegration\sipuni\notifications;

use SPVoipIntegration\ProvidersEnum;

class SipuniNotifyHangUp extends SipuniNotification {

    protected $fieldsMapping = array(
        'callstatus' => 'callstatus',
        'sp_recordingurl' => 'sp_recordingurl',
        'recordingurl' => 'recordingurl',
        'endtime' => 'endtime',
        'totalduration' => 'totalduration',
        'billduration' => 'billduration'
    );

    protected function prepareNotificationModel() {
        switch ($this->get('status')) {
            case 'ANSWER':
                $this->set('callstatus', 'completed');
                break;
            case 'BUSY':
                $this->set('callstatus', 'busy');
                break;
            case 'NOANSWER':
                $this->set('callstatus', 'no-answer');
                break;
            case 'CANCEL':
                $this->set('callstatus', 'cancelled');
                break;
            case 'CONGESTION':
                $this->set('callstatus', 'overloaded');
                break;
            case 'CHANUNAVAIL':
                $this->set('callstatus', 'no-answer');
                break;
        }
        $time = date('Y-m-d H:i:s');
        $createdTime = strtotime($this->pbxManagerModel->get('starttime'));
        if ($createdTime !== FALSE) {
            $this->set('totalduration', strtotime($time) - $createdTime);
        }
        $this->set('endtime', $time);
        $answerTimestamp = $this->get('call_answer_timestamp');
        if (!empty($answerTimestamp)) {
            $billDuration = $this->get('timestamp') - $answerTimestamp;
            $this->set('billduration', $billDuration);
        }
        $link = $this->get('call_record_link');
        if (!empty($link)) {
            $recordLink = urldecode($link);
            $this->set('sp_recordingurl', $recordLink);
            $this->downloadToLocal($recordLink);
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
        return "storage/" . ProvidersEnum::SIPUNI . "_{$this->getSourceUUId()}.mp3";
    }

    protected function canCreatePBXRecord() {
        return false;
    }

    protected function getCustomerPhoneNumber() {
        
    }

}
