/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Reports_Edit_Js("Reports_Edit3_Js",{},{
	
	step3Container : false,
	
	advanceFilterInstance : false,
	
	init : function() {
		this.initialize();
	},
	/**
	 * Function to get the container which holds all the report step3 elements
	 * @return jQuery object
	 */
	getContainer : function() {
		return this.step3Container;
	},

	/**
	 * Function to set the report step3 container
	 * @params : element - which represents the report step3 container
	 * @return : current instance
	 */
	setContainer : function(element) {
		this.step3Container = element;
		return this;
	},
	
	/**
	 * Function  to intialize the reports step3
	 */
	initialize : function(container) {
		if(typeof container == 'undefined') {
			container = jQuery('#report_step3');
		}
		
		if(container.is('#report_step3')) {
			this.setContainer(container);
		}else{
			this.setContainer(jQuery('#report_step3'));
		}
	},
	
	calculateValues : function(){
		//handled advanced filters saved values.
		var advfilterlist = this.advanceFilterInstance.getValues();
		jQuery('#advanced_filter').val(JSON.stringify(advfilterlist));
	},
	
	registerSubmitEvent : function(){
		var thisInstance = this;
		var form = this.getContainer();
		form.submit(function(e){
            //SalesPlatform.ru begin
            thisInstance.updateCKFieldsContents();
            //SalesPlatform.ru end
			thisInstance.calculateValues();
            
            //SalesPlatform.ru begin
            e.preventDefault();
            var progressIndicator = $.progressIndicator({message : app.vtranslate('JS_SAVE')});
            form.ajaxSubmit({
                dataType : 'json',
                success : function(response) {
                    if(response.success) {
                        location.href = response.result.location;
                    } else {
                        progressIndicator.hide();
                        form.removeData('submit');
                        Vtiger_Helper_Js.showPnotify({
                            type : 'error',
                            title : app.vtranslate('JS_SAVE_ERROR'),
                            text : response.error.message,
                            delay : 20000
                        });
                    }
                },

                error : function() {
                    form.removeData('submit');
                    progressIndicator.hide();
                    Vtiger_Helper_Js.showPnotify({
                        type : 'error',
                        text : app.vtranslate('JS_ERROR_SEND_REQUEST'),
                        delay : 10000
                    });
                }
            });
            
            return false;
            //SalesPlatform.ru end
		});
	},
	
	registerEvents : function(){
		var container = this.getContainer();
		app.changeSelectElementView(container);
		this.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',container));
		this.registerSubmitEvent();
		container.validationEngine();
	}
});
	



