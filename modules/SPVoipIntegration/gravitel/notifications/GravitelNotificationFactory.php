<?php
namespace SPVoipIntegration\gravitel\notifications;

class GravitelNotificationFactory {
    
    public static function getInstance($requestData) {
        $type = $requestData['cmd'];
        switch ($type) {
            case "history":
                return new GravitelHistoryNotification($requestData);
            case "event":
                return new GravitelEventNotification($requestData);
            case "contact":
                return new GravitelContactNotification($requestData);
            default:
                throw new \Exception("Unknown notification type");
        }
    }
    
}
