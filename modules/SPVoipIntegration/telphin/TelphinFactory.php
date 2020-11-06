<?php

namespace SPVoipIntegration\telphin;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\telphin\notifications\TelphinAbstractNotification;
use SPVoipIntegration\apiManagers\TelphinApiManager;
class TelphinFactory extends AbstractCallManagerFactory {    
    
    /**
     * 
     * @return TelphinApiManager
     */
    public function getCallApiManager() {
        return new TelphinApiManager();
    }

    /**
     * 
     * @return TelphinFactory
     */
    public function getNotificationModel($requestData) {
        return TelphinAbstractNotification::getInstance($requestData);
    }

}