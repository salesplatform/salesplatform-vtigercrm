/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Settings_Vtiger_List_Js('Settings_SPDynamicBlocks_List_Js', {
    
    triggerDelete : function(recordId) {
        var params = {};
        params['mode'] = 'deleteConfiguration';
        params['module'] = 'SPDynamicBlocks';
        params['parent'] = 'Settings';
        params['action'] = 'Index';
        params['record'] = recordId;
        app.request.post({data: params}).then(function (e, result) {           
            if (result) {
                window.location.reload();
            }
        });
    },
    
}, {  
   
});
