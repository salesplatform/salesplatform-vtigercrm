<?php
namespace SPVoipIntegration\sipuni;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\apiManagers\SipuniApiManager;
use SPVoipIntegration\sipuni\notifications\SipuniNotification;

class SipuniFactory extends AbstractCallManagerFactory {
       
    /**
     * 
     * @return SipuniCallApiManager
     */
    public function getCallApiManager() {
        return new SipuniApiManager();
    }

    /**
     * 
     * @return SipuniNotificationManager
     */
    public function getNotificationModel($request) {
        return SipuniNotification::getInstance($request);
    }

}