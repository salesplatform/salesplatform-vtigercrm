/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * Copyright (C) 2011-2015 SalesPlatform Ltd
 * All Rights Reserved.                                                              
 * Source code may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Settings_Vtiger_Index_Js("Settings_SPTips_Index_Js", {}, {
    
    registerEventForEditProvider : function() {
        var thisInstance = this;
        jQuery('#editProvider').on('click', function() {
            var selectedProviderId = thisInstance.getSelectedProviderId();
            if (selectedProviderId) {
                location.href = "index.php?module=SPTips&view=EditProvider&parent=Settings&providerId=" + selectedProviderId;
            }
        });
    },
    
    registerEventForAddNewRule : function() {
        var thisInstance = this;
        jQuery('#addRule').on('click', function() {
            var selectedProviderId = thisInstance.getSelectedProviderId();
            if (selectedProviderId) {
                location.href = "index.php?module=SPTips&view=EditRules&parent=Settings&providerId=" + selectedProviderId;
            }
        });
    },
    
    getSelectedProviderId : function() {
        return jQuery('#existingProviders').find(':selected').val();
    },
    
    registerEventForLoadRulesTable : function() {
        var thisInstance = this;
        jQuery('#existingProviders').on('change', function() {
            thisInstance.loadRulesTable();
        });
    },
    
    loadRulesTable : function() {
        var thisInstance = this;
        var params = {
            module: 'Settings:SPTips',
            view: 'ListRules',
            selectedProvider : this.getSelectedProviderId()
        };
        
        var aDeferred = jQuery.Deferred();
        app.request.post({data:params}).then(
            function(err, response){
                if (empty(err) && !empty(response)) {
                    jQuery('.rulesTable').html(response);
                    thisInstance.registerEventForDeleteRule();
                }
                aDeferred.resolve(response);
            },
            function(error){
                aDeferred.reject();
            }
        );

        return aDeferred.promise();
    },
    
    registerEventForDeleteRule : function() {
        jQuery('.deleteRule').on('click', function(e) {
            app.helper.showProgress();
            e.preventDefault();
            var ruleId = jQuery(e.currentTarget).attr('data-rule-id');
            if (ruleId !== undefined) {
                var data = {
                    module: 'SPTips',
                    parent: 'Settings',
                    action: 'DeleteRule',
                    record: ruleId
                };
            }
            
            
            app.request.post({'data': data}).then(
                function(error, responseObj) {
                    if (empty(error) && !empty(responseObj)) {
                        if (responseObj['success']) {
                            jQuery(e.currentTarget).closest("tr").remove();
                        }
                        app.helper.hideProgress();
                    } else {
                        app.helper.hideProgress();
                        app.helper.showErrorNotification({
                            message: app.vtranslate('JS_UNSUCCESSFULL')
                        });
                    }
                }, 
                function error(error) {
                    
                }
            );
    
            return false;
        });
    },
    
    registerEvents: function () {
        this.registerEventForLoadRulesTable();
        this.registerEventForEditProvider();
        this.registerEventForAddNewRule();
        this.registerEventForDeleteRule();
    }
});




