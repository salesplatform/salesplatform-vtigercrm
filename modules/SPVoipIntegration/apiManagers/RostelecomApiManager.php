<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\RostelecomClient;

class RostelecomApiManager extends AbstractCallApiManager {
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $userNumber = $currentUser->get('sp_rostelecom_extension');
        $client = new RostelecomClient();
        $client->makeCall($userNumber, $number); 
    }
    
}

