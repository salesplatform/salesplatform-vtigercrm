<?php
namespace SPVoipIntegration\zebra;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\apiManagers\ZebraTelecomApiManager;
use SPVoipIntegration\zebra\notifications\AbstractZebraNotification;

class ZebraTelecomFactory extends AbstractCallManagerFactory {        
    
    public function getCallApiManager() {
        return new ZebraTelecomApiManager();
    }

    public function getNotificationModel($requestData) {
        return AbstractZebraNotification::getInstance($requestData);
    }

}