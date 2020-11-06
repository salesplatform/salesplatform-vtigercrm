/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

jQuery.Class("SPVoipIntegration_Js", {}, {
    
    registerClick2Call : function() {
        var thisInstance = this;
        var params = {};
        params['mode'] = 'getOutgoingPermissions';
        params['module'] = 'SPVoipIntegration';        
        params['action'] = 'IntegrationActions';
        app.request.post({data: params}).then(function (e, result) {
            if(result.permission) {
                Vtiger_PBXManager_Js.makeOutboundCall = function(number, record){
                    thisInstance.click2Call(number);
                };
            }            
        });        
    },
    
    click2Call : function(phoneNumber) {
        var params = {};
        params['mode'] = 'startOutgoingCall';
        params['module'] = 'SPVoipIntegration';        
        params['action'] = 'IntegrationActions';        
        params['number'] = phoneNumber;
        app.request.post({data: params}).then(function (e, result) {
            if (result) {    
                params = {
                    text : app.vtranslate('JS_PBX_OUTGOING_SUCCESS'),
                    type : 'info'
                }                                                         
            } else if (e){
                params = {
                    text : app.vtranslate('JS_PBX_OUTGOING_FAILURE'),
                    type : 'info'
                }
            }
            Vtiger_PBXManager_Js.showPnotify(params);
        });
    },
    
    checkRegisterClick2Call : function() {
        var thisInstance = this;
        app.request.post({data: {
            module : 'SPVoipIntegration',
            action : 'IntegrationActions',
            mode : 'checkClickToCall'
        }}).then(function (error, response) {
            if(response.enabled) {
                thisInstance.registerClick2Call();  
            }
        });
    },
    
    registerEvents : function () {              
        this.checkRegisterClick2Call();    
    }
});

jQuery(document).ready(function () {
    var controller = new SPVoipIntegration_Js();
    controller.registerEvents();
});

