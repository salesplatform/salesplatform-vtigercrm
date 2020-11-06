/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

Vtiger.Class('Settings_SPDynamicBlocks_Js', {}, {    
	
    registerChangeModule : function() {        
        var thisInstance = this;
        var fieldPicklistElement = jQuery('[name="field_name"]');
        var valuesElement = jQuery('[name="values[]"]');
        jQuery('[name="module_name"]').on('change', function () {
            var selectedModule = jQuery(this).val();
            var params = {};
            params['mode'] = 'getPicklists';
            params['module'] = app.getModuleName();
            params['parent'] = app.getParentModuleName();
            params['action'] = 'Index';
            params['module_name'] = selectedModule;
            
            fieldPicklistElement.html('');
            fieldPicklistElement.select2("val", "");
            valuesElement.html('');
            valuesElement.select2("val", "");
            app.request.post({data: params}).then(function (e, result) {
                if (result) {
                    var fieldPicklists = result.fieldPicklists;                   
                    jQuery.each(fieldPicklists, function(value, label){
                        thisInstance.appendSelect(fieldPicklistElement, value, label);
                    });
                    fieldPicklistElement.trigger('liszt:updated');
                    fieldPicklistElement.trigger('change');
                }
            });            
        });
    },
    
    registerChangePicklist : function() {
        var thisInstance = this;
        var valuesElement = jQuery('[name="values[]"]');   
        jQuery('[name="field_name"]').on('change', function () {       
            var selectedModule = jQuery('[name="module_name"]').val();
            var selectedField =  jQuery(this).val();
            var params = {};
            params['mode'] = 'getValues';
            params['module'] = app.getModuleName();
            params['parent'] = app.getParentModuleName();
            params['action'] = 'Index';
            params['module_name'] = selectedModule;
            params['field_name'] = selectedField;
            
            valuesElement.html('');
            valuesElement.select2("val", "");
            app.request.post({data: params}).then(function (e, result) {                
                if (result) {
                    var values = result.values;                   
                    jQuery.each(values, function(value, label){
                        thisInstance.appendSelect(valuesElement, value, label);
                    });
                    valuesElement.trigger('liszt:updated');
                    thisInstance.setBlocks(selectedModule,  selectedField);
                }
            });
            
        });
    },
    
    setBlocks: function (moduleName, fieldName) {
        var thisInstance = this;
        var blocksElement = jQuery('[name="blocks[]"]');        
        var params = {};
        params['mode'] = 'getBlocks';
        params['module'] = app.getModuleName();
        params['parent'] = app.getParentModuleName();
        params['action'] = 'Index';
        params['module_name'] = moduleName;    
        params['field_name'] = fieldName;

        blocksElement.html('');
        blocksElement.select2("val", "");
        app.request.post({data: params}).then(function (e, result) {           
            if (result) {
                var values = result.values;                
                jQuery.each(values, function (value, label) {
                    thisInstance.appendSelect(blocksElement, value, label);
                });
                blocksElement.trigger('liszt:updated');
            }
        });

    },
    
    appendSelect : function (element, value, label) {
        element.append(jQuery("<option></option>").text(label).val(value));
    },
    
    
    registerEvents : function() {
		this.registerChangeModule();
        this.registerChangePicklist();
	}
	
});

jQuery(document).ready(function() {
	var instance = new Settings_SPDynamicBlocks_Js();
	instance.registerEvents();
});