<?php
namespace SPVoipIntegration\domru;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\domru\notifications\DomruContactNotification;
use SPVoipIntegration\domru\notifications\DomruEventNotification;
use SPVoipIntegration\domru\notifications\DomruHistoryNotification;
use SPVoipIntegration\apiManagers\DomruApiManager;

class DomruManagerFactory extends AbstractCallManagerFactory {
    
    public function getCallApiManager() {
        return new DomruApiManager();
    }

    public function getNotificationModel($request) {
        $notificationType = $request['cmd'];
        switch($notificationType) {
            
            case 'history':
                return new DomruHistoryNotification($request);
            
            case 'event':
                return new DomruEventNotification($request);
            
            case 'contact':
                return new DomruContactNotification($request);
                
            default:
                throw new \Exception('Unknow type');
        }
    }

}