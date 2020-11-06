<?php
namespace SPVoipIntegration\mcntelecom;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\mcntelecom\notifications\MCNAbstractNotification;
use SPVoipIntegration\apiManagers\MCNTelecomApiManager;
class MCNFactory extends AbstractCallManagerFactory {    
    
    public function getCallApiManager() {
        return new MCNTelecomApiManager();
    }

    public function getNotificationModel($requestData) {
        return MCNAbstractNotification::getInstance();
    }

}
