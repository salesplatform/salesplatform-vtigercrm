<?php
namespace SPVoipIntegration\telphin\notifications;

use SPVoipIntegration\api\TelphinClient;
use SPVoipIntegration\loggers\Logger;
class TelphinHangup extends TelphinAbstractNotification{
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'billduration' => 'billduration',
        'totalduration' => 'totalduration',
        'callstatus' => 'callstatus',                
        'sp_recorded_call_id' => 'RecID',
        'recordingurl' => 'recordingurl',
        'sp_is_local_cached' => 'sp_is_local_cached'
    );
    
    protected function prepareNotificationModel() {
        $this->set('endtime', $this->getEventDatetime());
        
        $microsecDuration = $this->get('Duration');
        $billDuration = $microsecDuration/self::MICRO_DELIMETER;
        $this->set('billduration', $billDuration);
        
        $totalDuration = $this->pbxManagerModel->get('totalduration') + $billDuration;
        $this->set('totalduration', $totalDuration);                
        
        $this->applyStatus();
        $recordUUID = $this->get('RecID');        
        if ($recordUUID) {
            try {
                $client = new TelphinClient();
                $userId = $this->pbxManagerModel->get('user');
                $userModel = \Users_Record_Model::getInstanceById($userId, 'Users');
                $extensionNumber = $userModel->get('sp_telphin_extension');
                $audioContent = $client->getRecord($extensionNumber, $recordUUID);
                
                $filePath = $this->generateSoundFileName();
                $status = file_put_contents($filePath, $audioContent);
                if($status === false) {
                    throw  new Exception('Cant save audio file');
                }

                $this->set('recordingurl', $filePath);
                $this->set('sp_is_local_cached', 1);
            } catch (\Exception $ex) {
                Logger::log('Error on save audiofile', $ex);
            }
        }
    }

    protected function canCreatePBXRecord() {
        return false;
    }
    
    private function generateSoundFileName() {
        return "storage/{$this->getSourceUUId()}.mp3";
    }
    
    private function applyStatus() {
        $notificationStatus = $this->get('CallStatus');

        switch ($notificationStatus) {
            case 'BUSY':
                $callStatus = 'busy';
                break;
            case 'NOANSWER':
                $callStatus = 'no-answer';
                break;
            case 'ANSWER':
                $callStatus = 'completed';
                break;
            case 'CANCEL':
                $callStatus = 'no-answer';
                break;
            default :
                $callStatus = $notificationStatus;
        }
        
        $this->set('callstatus', $callStatus);
    }

}
