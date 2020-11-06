<?php
namespace SPVoipIntegration\westcallspb;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\westcallspb\notifications\WestCallSPBContactNotification;
use SPVoipIntegration\westcallspb\notifications\WestCallSPBEventNotification;
use SPVoipIntegration\westcallspb\notifications\WestCallSPBHistoryNotification;
use SPVoipIntegration\apiManagers\WestCallSPBApiManager;

class WestCallSPBManagerFactory extends AbstractCallManagerFactory {
    
    public function getCallApiManager() {
        return new WestCallSPBApiManager();
    }

    public function getNotificationModel($request) {
        $notificationType = $request['cmd'];
        switch($notificationType) {            
            case 'history':
                return new WestCallSPBHistoryNotification($request);
            
            case 'event':
                return new WestCallSPBEventNotification($request);
            
            case 'contact':
                return new WestCallSPBContactNotification($request);
                
            default:
                throw new \Exception('Unknow type');
        }
    }

}
