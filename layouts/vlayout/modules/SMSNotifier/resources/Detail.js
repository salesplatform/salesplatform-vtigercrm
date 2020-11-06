/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Vtiger_Detail_Js("SMSNotifier_Detail_Js",{
    
	/*
	 * Checks statuses of sms
	 */
	checkStatus : function(checkStatusUrl, currentElement) {
        AppConnector.request(checkStatusUrl).then(
            function(data) {
                app.showModalWindow(data);
            },
            function(error,err){

            }
        );
	}
},{});