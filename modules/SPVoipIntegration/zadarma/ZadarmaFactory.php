<?php
namespace SPVoipIntegration\zadarma;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\apiManagers\ZadarmaApiManager;
use SPVoipIntegration\zadarma\notifications\ZadarmaNotification;

class ZadarmaFactory extends AbstractCallManagerFactory {    
    
    /**
     * 
     * @return ZadarmaCallApiManager
     */
    public function getCallApiManager() {
        return new ZadarmaApiManager();
    }

    /**
     * 
     * @return ZadarmaNotificationManager
     */
    public function getNotificationModel($requestData) {
        return ZadarmaNotification::getInstance($requestData);
    }

}