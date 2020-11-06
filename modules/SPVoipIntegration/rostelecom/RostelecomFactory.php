<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\rostelecom;

use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\rostelecom\notifications\RostelecomAbstractNotification;
use SPVoipIntegration\apiManagers\RostelecomApiManager;

class RostelecomFactory extends AbstractCallManagerFactory{
    
    public function getCallApiManager() {
        return new RostelecomApiManager();
    }

    public function getNotificationModel($requestData) {
        // rostelecom's answer has Content-Type: application/json
        // json stores in HTTP_RAW_POST_DATA
        $postData = file_get_contents("php://input");
        $postDataArr = json_decode($postData, true);
        return RostelecomAbstractNotification::getInstance($postDataArr);
    }


}