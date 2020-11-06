/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Vtiger_Edit_Js("SPPayments_Edit_Js",{},{
    
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

