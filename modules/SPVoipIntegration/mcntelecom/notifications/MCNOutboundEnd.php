<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace SPVoipIntegration\mcntelecom\notifications;

use SPVoipIntegration\mcntelecom\MCNFactory;
use SPVoipIntegration\loggers\Logger;
/**
 * Description of MCNOutboundEnd
 *
 * @author nikita
 */
class MCNOutboundEnd extends MCNAbstractNotification{
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'billduration' => 'billsec',
        'totalduration' => 'totalduration',
        'callstatus' => 'callstatus',
        'sp_is_recorded' => 'record',
        'recordingurl' => 'recordingurl',
        'sp_is_local_cached' => 'sp_is_local_cached'
    );
    
    protected function canCreatePBXRecord() {
        return false;
    }
       
    protected function prepareNotificationModel() {        
        $totalDuration = $this->pbxManagerModel->get('totalduration');        
        $this->set('endtime', date('Y-m-d H:i:s'));
        $this->set('totalduration', $totalDuration + $this->get('billsec'));
        $this->set('callstatus', 'completed');
        if ($this->get('record')) {
            $this->saveRecord();
        }
    }
    
    protected function saveRecord() {
        sleep(3);
        try {
            $factory = new MCNFactory();
            $apiManager = $factory->getCallApiManager();
            $callId = $this->get('call_id');
            
            $audioContent = $apiManager->getRecord($callId);

            $filePath = $this->generateSoundFileName();
            $status = file_put_contents($filePath, $audioContent);
            if ($status === false) {
                throw new Exception('Cant save audio file');
            }

            $this->set('recordingurl', $filePath);
            $this->set('sp_is_local_cached', 1);
        } catch (\Exception $ex) {
            Logger::log('Error on save audiofile', $ex);
        }
    }
    
    private function generateSoundFileName() {
        return "storage/{$this->getSourceUUId()}.mp3";
    }

}