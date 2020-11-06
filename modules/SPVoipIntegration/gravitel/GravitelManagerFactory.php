<?php
namespace SPVoipIntegration\gravitel;

use SPVoipIntegration\gravitel\notifications\GravitelHistoryNotification;
use SPVoipIntegration\gravitel\notifications\GravitelEventNotification;
use SPVoipIntegration\gravitel\notifications\GravitelContactNotification;
use SPVoipIntegration\apiManagers\GravitelApiManager;
use SPVoipIntegration\integration\AbstractCallManagerFactory;

class GravitelManagerFactory extends AbstractCallManagerFactory {
    
    public function getCallApiManager() {
        return new GravitelApiManager();
    }

    public function getNotificationModel($request) {
        $notificationType = $request['cmd'];
        switch($notificationType) {
            
            case 'history':
                return new GravitelHistoryNotification($request);
            
            case 'event':
                return new GravitelEventNotification($request);
            
            case 'contact':
                return new GravitelContactNotification($request);
                
            default:
                throw new \Exception('Unknow type');
        }
    }

}
