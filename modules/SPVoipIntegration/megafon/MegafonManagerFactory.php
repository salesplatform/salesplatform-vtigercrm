<?php
namespace SPVoipIntegration\megafon;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\megafon\notifications\MegafonHistoryNotification;
use SPVoipIntegration\megafon\notifications\MegafonEventNotification;
use SPVoipIntegration\megafon\notifications\MegafonContactNotification;
use SPVoipIntegration\apiManagers\MegafonApiManager;

class MegafonManagerFactory extends AbstractCallManagerFactory {
    
    public function getCallApiManager() {
        return new MegafonApiManager();
    }

    public function getNotificationModel($request) {
        $notificationType = $request['cmd'];
        switch($notificationType) {
            
            case 'history':
                return new MegafonHistoryNotification($request);
            
            case 'event':
                return new MegafonEventNotification($request);
            
            case 'contact':
                return new MegafonContactNotification($request);
                
            default:
                throw new \Exception('Unknow type');
        }
    }

}