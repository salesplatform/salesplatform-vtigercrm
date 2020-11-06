<?php
namespace SPVoipIntegration\uiscom;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\apiManagers\UIScomApiManager;
use SPVoipIntegration\uiscom\notifications\UIScomNotification;

class UIScomFactory extends AbstractCallManagerFactory {
    
    /**
     * 
     * @return UIScomApiManager
     */
    public function getCallApiManager() {
        return new UIScomApiManager();
    }

    /**
     * 
     * @param type $request
     * @return type
     */
    public function getNotificationModel($request) {
        return UIScomNotification::getInstance($request);
    }

}