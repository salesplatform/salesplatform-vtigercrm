<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

namespace SPVoipIntegration\rostelecom\notifications;

/**
 * Description of Rostelecom event types
 */
class RostelecomEventAndStateType {
    // events
    const INCOMING = 'incoming';
    const OUTBOUND = 'outbound';
    const INTERNAL = 'internal';
    
    //states
    const NEW_STATE = 'new';
    const CONNECTED = 'connected';
    const DISCONNECTED = 'disconnected';
}