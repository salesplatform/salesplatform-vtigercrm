<?php
namespace SPVoipIntegration\mango;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\apiManagers\MangoApiManager;
use SPVoipIntegration\mango\notifications\MangoNotification;

class MangoFactory extends AbstractCallManagerFactory {
       
    /**
     * 
     * @return MangoCallApiManager
     */
    public function getCallApiManager() {
        return new MangoApiManager();
    }

    /**
     * 
     * @return MangoNotificationManager
     */
    public function getNotificationModel($request) {
        $mangoRequest = (array)json_decode($request['json']);
        $mangoRequest['sign'] =  $request['sign'];
        $mangoRequest['vpbx_api_key'] = $request['vpbx_api_key'];
        $mangoRequest['json'] = $request['json'];
        return MangoNotification::getInstance($mangoRequest);
    }
}