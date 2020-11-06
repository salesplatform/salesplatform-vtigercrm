/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

jQuery.Class("SPSocialConnector_Edit_Js",{
    
    editViewForm : false,
    
    /**
	 * Function which gives edit view form
     * 
	 * @return : jQuery object which represents the form element
	 */
	getForm : function() {
		if(this.editViewForm == false){
			this.editViewForm = jQuery('#EditView');
		}
		return this.editViewForm;
	},
    
    /*
	 * function to trigger enter URL to social nets
	 * @params: display enter url window
	 */
    triggerEnterURL : function(editActionUrl) {
        SPSocialConnector_Edit_Js.triggerDetailViewActionSendMessage(editActionUrl);	
        
    },
    
    /*
	 * function to trigger Edit view action (Import button) for SPSocialConnector module
	 * @params: Action url , callback function.
	 */
    triggerDetailViewActionSendMessage : function(editActionUrl, callBackFunction){
        var thisInstance = this;
        var form = thisInstance.getForm();
        var editActionUrl_array = SPSocialConnector_Edit_Js.urlTOarray(editActionUrl);

        var postData = {
           "selected_ids": '['+JSON.stringify(editActionUrl_array['record_id'])+']'
        };
        var actionParams = {
			"type":"POST",
			"url":editActionUrl,
			"dataType":"html",
			"data" : postData
		};

        app.request.post(actionParams).then(
			function(data) {
				if(data) {
					app.showModalWindow(data, function(data){
                        SPSocialConnector_Edit_Js.registerEnterURLEvent();
					});			
				}
			},
			function(error,err){

			}
		);
    },
    
    urlTOarray: function(url) {
        var request = {};
        var pairs = url.substring(url.indexOf('?') + 1).split('&');
        for (var i = 0; i < pairs.length; i++) {
          var pair = pairs[i].split('=');
          request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
        }
        return request;
    },
    
    /*
	 * function to call the register events of send message to the social nets
	 */
    registerEnterURLEvent : function() {
        var thisInstance = this;
		var selectEmailForm = jQuery("#enterURL");
		selectEmailForm.on('submit',function(e){
			var form = jQuery(e.currentTarget);
			var params = JSON.stringify(form.serializeFormData());
            var obj = JSON.parse(params);
            var str = "&URL="+obj.message+"&sourcemodule="+obj.source_module+"&recordid="+obj.record_id;
            app.hideModalWindow();
            e.preventDefault();
            var popupInstance = Vtiger_Popup_Js.getInstance();
            var win = popupInstance.show("module=SPSocialConnector&view=AuthWindow&popuptype=load_profile"+str);
            var timer = setInterval(function() {
                if(win.closed) {
                    clearInterval(timer);
                    location.reload();
                }
            }, 500);
        });
    }
},{
    
	editViewForm : false,
    
    /**
	 * Function which gives edit view form
     * 
	 * @return : jQuery object which represents the form element
	 */
	getForm : function() {
		if(this.editViewForm == false){
			this.editViewForm = jQuery('#EditView');
		}
		return this.editViewForm;
	},
    
	/**
	 * Function which will register event for Reference Fields Selection
     * 
     * @param container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
            thisInstance.referenceSelectionEventHandler(data);
		});
	},

    /**
	 * Reference Fields Selection Event Handler
     * 
     * @param data - info array of selected entity
	 */
	referenceSelectionEventHandler : function(data){
        var thisInstance = this;
        thisInstance.getRecordDetails(data).then(
			function(data){
                var form = thisInstance.getForm();
				thisInstance.addAmount(data, form);
                thisInstance.addPayer(data, form);
			},
			function(error, err){
                
            });
		
	},
    
    /**
	 * Function which autocomplete amount field 
     * 
	 * @param data - info array of selected entity
     * @param form - element in which auto complete fields needs to be searched
	 */
    addAmount : function(data, form) {
        var thisInstance = this;
        var response = data['result']['data'];
        form.find('[name=amount]').val(response['hdnGrandTotal']);
        form.find('[name=amount]').trigger('change');
    },
    
    /**
	 * Function which autocomplete payer field
     * 
	 * @param data - info array of selected entity
     * @param form - element in which auto complete fields needs to be searched
	 */
    addPayer : function(data, form) {
        var thisInstance = this;
        var response = data['result']['data'];
        var recordModule = data['result']['data']['record_module'];
        if(recordModule == 'PurchaseOrder') {
            var PO_data = {
                record : response['vendor_id'],
                source_module : 'Vendors'
            };
            thisInstance.getRecordDetails(PO_data).then(
            function(PO_data){
                form.find('[name=payer]').val(response['vendor_id']);
                form.find('[name=payer]').trigger('change');
                form.find('[name=payer_display]').val(PO_data['result']['data']['vendorname']);
                form.find('[name=payer_display]').trigger('change');
            },
            function(error, err){
                
            });
        } else {
            var SOandINV_data = {
                record : response['account_id'],
                source_module : 'Accounts'
            };
            thisInstance.getRecordDetails(SOandINV_data).then(
            function(SOandINV_data){
                form.find('[name=payer]').val(response['account_id']);
                form.find('[name=payer]').trigger('change');
                form.find('[name=payer_display]').val(SOandINV_data['result']['data']['accountname']);
                form.find('[name=payer_display]').trigger('change');
            },
            function(error, err){
                
            });
        }
    },
    
	registerEvents : function(){
		this._super();
        this.registerReferenceSelectionEvent();
	}
});

