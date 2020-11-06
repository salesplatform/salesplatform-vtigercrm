/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Vtiger.Class("Settings_Vtiger_CompanyDetailsEdit_Js",{},{
    
    existsCompanies : [],
    
    registerSaveCompanyDetailsEvent : function() {
		var thisInstance = this;
		var form = jQuery('#updateCompanyDetailsForm');
		var params = {
			submitHandler : function(form) {
                var companyName = $("[name='company']").val();
                var organizationId = $("#organizationId").val();
                if( organizationId == "" && $.inArray($.trim(companyName), thisInstance.existsCompanies) !== -1 ) {
                    app.helper.showErrorNotification({
                        message : app.vtranslate('JS_COMPANY_EXISTS')
                    });
                    
                    return false;
                }
                
                return true;
			}
		};
		form.vtValidate(params);
	},
    
    registerCompanyLogoDimensionsValidation : function() {
        var allowedDimensions = {
            width : 150,
            height : 40
        };
        
        var updateCompanyDetailsForm = jQuery('form#updateCompanyDetailsForm');
        var logoFile = updateCompanyDetailsForm.find('#logoFile');
        logoFile.on('change', function() {
            var _URL = window.URL || window.webkitURL;
            var image, file = this.files[0];
            if(file && typeof Image === 'function') {
                image = new Image();
                image.onload = function() {
                    var width = this.width;
                    var height = this.height;
                    if(width > allowedDimensions.width || height > allowedDimensions.height ) {
                        app.helper.showErrorNotification({
                            'message' : app.vtranslate('JS_LOGO_IMAGE_DIMENSIONS_WRONG')
                        });
                        logoFile.val(null);
                    }
                };
                image.src = _URL.createObjectURL(file);
            }
        });
    },
    
    loadCompanies : function() {
        try {
            var thisInstance = this;
            var rawCompanies = JSON.parse($("#existsCompanies").val());
            $.each(rawCompanies, function(index, value) {
                thisInstance.existsCompanies.push($("<div/>").html(value).text());
            });
            
        } catch(e) {
        }
    },
    
    registerEvents: function() {
        this.loadCompanies();
        this.registerSaveCompanyDetailsEvent();
        this.registerCompanyLogoDimensionsValidation();
    }
    
});