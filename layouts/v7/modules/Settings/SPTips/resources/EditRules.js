/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * Copyright (C) 2011-2015 SalesPlatform Ltd
 * All Rights Reserved.                                                              
 * Source code may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Settings_Vtiger_Index_Js("Settings_SPTips_EditRules_Js", {}, {
    
    getCurrentModuleName : function() {
        return jQuery('[name="module"]').val();
    },

    registerEventForChangingModule : function() {
        var thisInstance = this;
        var form = jQuery("#rulesForm");
        form.find('[name="sourceModule"]').on('change', function() {
            thisInstance.loadRuleSettings().then(
                function(data) {
                    thisInstance.registerEventForChangingModule();
                    thisInstance.registerChangeTipType();
                    thisInstance.registerEventForAddingNewField();
                }
            );
        });
    },
    
    registerChangeTipType : function() {
        var thisInstance = this;
        var form = jQuery("#rulesForm");
        form.find('[name="type"]').on('change', function() {
            thisInstance.loadRuleSettings().then(
                function(data) {
                    thisInstance.registerEventForChangingModule();
                    thisInstance.registerChangeTipType();
                    thisInstance.registerEventForAddingNewField();
                }
            );
        });
    },
    
    getSourceModule : function() {
        return jQuery("#rulesForm").find('[name="sourceModule"]').val();
    },
    
    getTipType : function() {
        return jQuery("#rulesForm").find('[name="type"]').val();
    },
    
    loadRuleSettings : function() {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress();
        var data = {
            module: thisInstance.getCurrentModuleName(),
            parent: app.getParentModuleName(),
            view: 'EditRules',
            record: jQuery('[name="record"]').val(),
            sourceModule: thisInstance.getSourceModule(),
            type : thisInstance.getTipType(),
            providerId: jQuery('[name="providerId"]').val()
        };
        
        app.request.pjax({data: data}).then(
            function(error, data) {
                app.helper.hideProgress();
                var container = jQuery('.settingsPageDiv div');
                container.html(data);
                vtUtils.showSelect2ElementView(container.find('select.select2'));
                aDeferred.resolve(data);
            },
            function(error) {
                app.helper.hideProgress();
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
        
    },
    
    registerEventForAddingNewField : function() {
        var thisInstance = this;
        var lineItemCopy = jQuery('.lineItemCopy');
        // before copy we need to disable select2 on the select element
        lineItemCopy.find('select').each(function(index, elem) {
            jQuery(elem).select2('destroy');
            jQuery(elem).attr('disabled','disabled');
        });
        
        jQuery("#addDependendField").on('click', function(e) {
            // restore select2 after copy
            var newElement = lineItemCopy.clone().appendTo('.editViewContents');
            newElement.find('select').each(function(index, elem) {
                jQuery(elem).attr("data-rule-required", "true");
                jQuery(elem).select2();
                jQuery(elem).removeAttr('disabled');
            });
            
            newElement.removeClass('lineItemCopy');
            newElement.removeClass('hide');
            
            thisInstance.registerEventForDeleteFieldsItemLine();
        });
    },
    
    registerEventForDeleteFieldsItemLine : function() {
        jQuery('.deleteFieldsItemLine').on('click', function(e) {
            var lineWithItems = jQuery(e.currentTarget).closest('div.form-group');
            lineWithItems.remove();
        });
    },
    
    loadFieldsForNewProvider : function(selectedProvider) {
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress();
        var sourceModule = jQuery('[name="sourceModule"]').val();
        var data = {
            module: this.getCurrentModuleName(),
            parent: app.getParentModuleName(),
            view: 'EditRules',
            selectedProvider: selectedProvider,
            sourceModule: sourceModule
        };
        
        app.request.pjax({data: data}).then(
            function(error, data) {
                app.helper.hideProgress();
                var container = jQuery('.settingsPageDiv div');
                container.html(data);
                //register all select2 Elements
                vtUtils.showSelect2ElementView(container.find('select.select2'));
                aDeferred.resolve(data);
            },
            function(error) {
                app.helper.hideProgress();
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },
    
    registerEvents: function () {
        this.registerEventForChangingModule();
        this.registerChangeTipType();
        this.registerEventForAddingNewField();
        this.registerEventForDeleteFieldsItemLine();
    }
});




