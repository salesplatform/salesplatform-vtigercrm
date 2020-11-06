/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

jQuery.Class("SPDynamicBlocks_Js", {}, {
    
    selectedModuleName : false,
    
    setSelectedModuleName : function(moduleName) {
        this.selectedModuleName = moduleName;
    },
    
    getSelectedModuleName : function() {
        if (this.selectedModuleName == false) {
            this.selectedModuleName = app.module();
        }
        return this.selectedModuleName;
    },
    
    getRecordId : function(){
	if(app.getRecordId())
    	    return app.getRecordId();
    	else
    	    return 0;
    },
    
    registerEditBlocksConfigurations : function () {                
        var form = jQuery('#EditView');
        var selectElements = jQuery('select.inputElement', form);
        if (form.length > 0) {
            selectElements.on('change', function(e){
                var params = {};
                params['mode'] = 'getBlocksToHide';
                params['module'] = 'SPDynamicBlocks';
                params['parent'] = 'Settings';
                params['action'] = 'Index';                
                params['formFields'] = encodeURIComponent(JSON.stringify(form.serializeFormData()));                                             
                app.request.post({data: params}).then(function (e, result) {
                    if (result) {
                        var blocksToHide = result.blocks;
                        var formBlocks = form.find('.fieldBlockContainer');                        
                        jQuery.each(formBlocks, function(key, formBlock) {
                            var blockLabel = jQuery(formBlock).find('.fieldBlockHeader').text();                                                      
                            if (typeof blocksToHide[blockLabel] != 'undefined') {
                                jQuery(this).hide();
                                jQuery(formBlock).find('input, select').prop('disabled', true);
                            } else {
                                jQuery(this).show();
                                jQuery(formBlock).find('input, select').prop('disabled', false);
                            }
                        });
                    }
                });
            });
            jQuery.each(selectElements, function(key, element){               
                var value = jQuery(this).val();               
                if (value != '' && typeof value != 'undefined' &&  value != null) {
                    jQuery(this).trigger('change');
                }
            });
        }                
    },
    
    registerDetailBlocksConfiguration : function() {
        var thisInstance = this;
        var form = jQuery('#detailView');
        
        form.on('click','.inlineAjaxSave',function(e){            
            var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = thisInstance.getInlineWrapper(currentTarget); 
			var editElement = jQuery('.edit',currentTdElement);
			var fieldBasicData = jQuery('.fieldBasicData', editElement);
			var fieldName = fieldBasicData.data('name');
			var fieldType = fieldBasicData.data("type");
            var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var ajaxEditNewValue = fieldElement.val();
            
            if(fieldType === 'multipicklist' || fieldType === 'picklist'){
                var configData = thisInstance.getDetailFormValues();
                configData[fieldName] = ajaxEditNewValue;
                thisInstance.hideDetailBlocksOnChange(configData);
            }                     
        });
    },
    
    getDetailFormValues : function () {        
        var form = jQuery('#detailView');
        var configData = {};
        var thisInstance = this;
        
        jQuery.each(jQuery('.block:visible', form), function (key, blockElem) { 
            jQuery.each(jQuery('.edit', blockElem), function (key, fieldElem) {                
                var fieldData = jQuery('.fieldBasicData', jQuery(this));
                var formFieldName = fieldData.data('name');
                var formFieldType = fieldData.data('type');

                if (formFieldType == 'multipicklist') {
                    configData[formFieldName] = fieldData.data('value').split(' |##| ');
                    configData[formFieldName.substr(0, formFieldName.indexOf('[]'))] = fieldData.data('value').split(' |##| ');
                } else {
                    configData[formFieldName] = fieldData.data('value');
                }
            });
        });
        configData['module'] = thisInstance.getSelectedModuleName();
        return configData;
    },
    
    hideDetailBlocksOnChange: function (configData) {                
        var thisInstance = this;
        var params = {};
        params['mode'] = 'getBlocksToHide';
        params['module'] = 'SPDynamicBlocks';
        params['parent'] = 'Settings';
        params['action'] = 'Index';        
        params['formFields'] = encodeURIComponent(JSON.stringify(configData));
        app.request.post({data: params}).then(function (e, result) {
            if (result) {                
                var blocksToHide = result.blocks;                                
                thisInstance.hideBlocks(blocksToHide);         
            }
        });
    },
    
    hideBlocks: function (blocksToHide) {
        var form = jQuery('#detailView');
        var formBlocks = form.find('.block');
        formBlocks.show();
        jQuery.each(blocksToHide, function (key, blockToHide) {   
            $.each(formBlocks, function(index, formBlock) {
                var blockLabel = $(formBlock).data("block");
                if(blockLabel === blockToHide) {
                    $(formBlock).hide();
                }
            }) ;
        });
    },
    
    getInlineWrapper : function(element) {
		var wrapperElement = element.closest('td');
		if(!wrapperElement.length) {
			wrapperElement = element.closest('.td');
		}
		return wrapperElement;
	},
    
    hideDetailsBlocksOnLoad : function() {
        var thisInstance = this;
        var params = {};
        params['mode'] = 'getBlocksToHideOnLoadDetail';
        params['module'] = 'SPDynamicBlocks';
        params['parent'] = 'Settings';
        params['action'] = 'Index';        
        params['record'] = this.getRecordId();        
        app.request.post({data: params}).then(function (e, result) {                 
            if (result) {                
                var blocksToHide = result.blocks;                                
                thisInstance.hideBlocks(blocksToHide);         
            }
        });
        
    },
    
    registerRelatedTabClick : function() {
        var thisInstance = this;
        app.event.on("post.relatedListLoad.click", function() {
            thisInstance.hideDetailsBlocksOnLoad();
            thisInstance.setSelectedModuleName(app.module());
            thisInstance.registerDetailBlocksConfiguration();
        });
    },
    
    setDetailMinWidth : function () {
        var pageHeight = jQuery('#page').css('height');
        jQuery('.content-area', '.editViewPageDiv').css({
            "min-height": pageHeight           
        });
    },
    
    registerOverlayEditOpened : function() {
        var thisInstance = this;
        app.event.on('post.overLayEditView.loaded', function() {
            thisInstance.registerEditBlocksConfigurations();
        });
    },
    
    registerRelatedListClick: function () {
        var thisInstance = this;
        var contentHolder = jQuery('div.details');
        contentHolder.on('click', '.listViewEntries', function (e) {
            var elem = jQuery(e.currentTarget);
			var recordUrl = elem.data('recordurl');
            if(typeof recordUrl != "undefined"){
				var params = app.convertUrlToDataParams(recordUrl);
                var module = params.module;
                thisInstance.setSelectedModuleName(module);
            }
            
        });
        app.event.on("post.overlay.load", function (event, data) {
            thisInstance.hideDetailsBlocksOnLoad();
            thisInstance.registerDetailBlocksConfiguration();
        });
    },
    
    registerEvents : function () {
        this.registerRelatedTabClick();
        this.registerRelatedListClick();
        this.registerEditBlocksConfigurations();
        this.hideDetailsBlocksOnLoad();
        this.registerDetailBlocksConfiguration();
        this.setDetailMinWidth();
        this.registerOverlayEditOpened();        
    }

});


jQuery(document).ready(function () {
    var controller = new SPDynamicBlocks_Js();
    controller.registerEvents();
});

