/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Vtiger_Detail_Js("SPSocialConnector_Detail_Js",{

    detailInstance : false,
        
	getInstance: function(){
        if( SPSocialConnector_Detail_Js.detailInstance == false ){
            var module = app.getModuleName();
            var moduleClassName = module+"_Detail_Js";
            var fallbackClassName = SPSocialConnector_Detail_Js;
            if(typeof window[moduleClassName] != 'undefined'){
                var instance = new window[moduleClassName]();
            }else{
                var instance = new fallbackClassName();
            }
            SPSocialConnector_Detail_Js.detailInstance = instance;
        }
        return SPSocialConnector_Detail_Js.detailInstance;
	},
    
    /*
	 * function to trigger send message to social nets
	 * @params: send message url
	 */
    triggerSendMessage : function(detailActionUrl) {
        
        SPSocialConnector_Detail_Js.triggerDetailViewActionSendMessage(detailActionUrl);	
        
    },
    
    /*
	 * function to trigger get message from social nets
	 * @params: get message url
	 */
    triggerGetMessage : function(detailActionUrl) {
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showWindow("module=SPSocialConnector&view=AuthWindow&popuptype=get_msg&" + detailActionUrl,'', '', '', function(params){
        });         
    },
    
    /*
	 * function to trigger Detail view actions for SPSocialConnector module
	 * @params: Action url , callback function.
	 */
    triggerDetailViewActionSendMessage : function(detailActionUrl, callBackFunction){
		var detailInstance = SPSocialConnector_Detail_Js.getInstance();
        var selectedIds = new Array();
        selectedIds.push(detailInstance.getRecordId());              
                
        var params = {};   
        params['module'] = app.getModuleName();
        params['parent'] = app.getParentModuleName();
        params['view'] = 'MassActionAjax';
        params['mode'] = 'showSendMessageForm';
        params['selected_ids'] = JSON.stringify(selectedIds);
        
         AppConnector.request(params).then(
			function(data) {
				if(data) {
					app.showModalWindow(data, function(data){
                        SPSocialConnector_Detail_Js.registerURLFieldSelectionEvent();
					});			
				}
			},
			function(error,err){

			}
		);
    },
    
    /*
	 * function to call the register events of send message to the social nets
	 */
    registerURLFieldSelectionEvent : function() {
		var selectEmailForm = jQuery("#massSave");
		selectEmailForm.on('submit',function(e){
			var form = jQuery(e.currentTarget);
			var params = JSON.stringify(form.serializeFormData());
            var obj = JSON.parse(params);
            var str = '&source_module='+obj.source_module+'&record_id='+obj.selected_ids+'&text='+obj.message+'&URL='+obj.fields;
            var popupInstance = Vtiger_Popup_Js.getInstance();
            popupInstance.showWindow("module=SPSocialConnector&view=AuthWindow&popuptype=send_msg"+str,'', '', '', function(params){
            });
		});
    }
	
},{
    registerEvents : function(){
        this._super();
    }
});


