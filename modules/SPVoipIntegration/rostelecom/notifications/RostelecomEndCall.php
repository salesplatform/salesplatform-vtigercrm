<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\rostelecom\notifications;

use SPVoipIntegration\api\RostelecomClient;
use SPVoipIntegration\ProvidersEnum;

class RostelecomEndCall extends RostelecomAbstractNotification {
    
    protected $direction = 'outbound';
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'totalduration' => 'totalduration',
        'callstatus' => 'callstatus',
        'sp_voip_provider' => 'sp_voip_provider',
        'recordingurl' => 'recordingurl',
        'sp_is_local_cached' => 'sp_is_local_cached'
    );
    
    protected function prepareNotificationModel() {
        $endTime = $this->get('timestamp');
        $this->set('endtime', $endTime);
        $this->set('sp_voip_provider', ProvidersEnum::ROSTELECOM);
        
        $userModel = self::getUserByNumber($this->getUserPhoneNumber());
        if ($userModel != null) {
            $this->set('user', $userModel->getId());
        }
        
        $startTime = $this->pbxManagerModel->get('starttime');
        $totalDuration = $this->getTotalDurationTime($startTime, $endTime);
        $this->set('totalduration', $totalDuration); 
        
        $previousCallStatus = $this->pbxManagerModel->get('callstatus');
        if ($previousCallStatus == 'ringing') {
            $this->set('callstatus', 'no-answer');
        }
        else {
            $this->set('callstatus', 'completed');
        }
        
        // get record
        $sessionId = $this->getSourceUUId();
        
        $client = new RostelecomClient();
        $audioContent = $client->getRecord($sessionId);
        if (!is_null($audioContent)) {
            $filePath = $this->generateSoundFileName();
            $status = file_put_contents($filePath, $audioContent);
            if($status) {
                $this->set('recordingurl', $filePath);
                $this->set('sp_is_local_cached', 1);
            }
        }
    }
    
    protected function canCreatePBXRecord() {
        return false;
    }
    
    private function generateSoundFileName() {
        return "storage/" . ProvidersEnum::ROSTELECOM . "{$this->getSourceUUId()}.mp3";
    }
    
}

