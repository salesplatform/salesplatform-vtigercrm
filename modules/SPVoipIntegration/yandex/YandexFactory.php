<?php

namespace SPVoipIntegration\yandex;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\yandex\notifications\AbstractYandexNotification;
use SPVoipIntegration\apiManagers\YandexApiManager;

class YandexFactory extends AbstractCallManagerFactory{
    
    public function getCallApiManager() {
        return new YandexApiManager();
    }

    public function getNotificationModel($requestData) {
        return AbstractYandexNotification::getInstance($requestData);
    }


}
