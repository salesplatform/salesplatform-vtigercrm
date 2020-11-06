/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
// SalesPlatform.ru begin Fixing bug with creating SPPayments from Invoices
Inventory_Detail_Js("Invoice_Detail_Js",{},{
    
    	registerEventForAddingRelatedRecord : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','[name="addButton"]',function(e){
                var element = jQuery(e.currentTarget);
                var selectedTabElement = thisInstance.getSelectedTab();
                var relatedModuleName = thisInstance.getRelatedModuleName();
                var quickCreateNode = jQuery('#EditView').find('[data-name="'+ relatedModuleName +'"]'); 

			if(quickCreateNode.length <= 0 || selectedTabElement.data('labelKey') == 'Activities') {
                window.location.href = element.data('url');
                return;
                }
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.addRelatedRecord(element);
		})
	},
//    /**
//    * Function which will regiter all events for this page
//    */
     registerEvents : function(){
        this._super();
        this.registerEventForAddingRelatedRecord();
    }
});
//Inventory_Detail_Js("Invoice_Detail_Js",{},{});
// SalesPlatform.ru end