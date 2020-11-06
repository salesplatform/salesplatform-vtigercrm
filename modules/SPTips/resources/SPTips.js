/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * Copyright (C) 2011-2015 SalesPlatform Ltd
 * All Rights Reserved.                                                              
 * Source code may not be redistributed unless expressly permitted by SalesPlatform Ltd.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

jQuery.Class("SPTips_Js", {}, {

    makeFieldBinding : function(bindData) {
        var thisInstance = this;
        var ruleId = bindData.ruleId;
        var autocompleteFieldName = bindData.autocomplete;
        
        var fieldElem = jQuery('[name="' + autocompleteFieldName + '"]');
        fieldElem.autocomplete({
            minLength : 3,
            wordCount : 1, // need for textarea elements
            source : function(request, response){
                var search = request.term;
                thisInstance.search(search, ruleId).then(
                    function(responseData) {
                        var source = [];
                        jQuery.each(responseData, function(index, item) {
                            source.push({
                               label : item.tip,
                               value : item.tip,
                               fill : item.fill
                            });
                        }); 
                        
                        response(source);
                    },
                    function() {
                        response();
                    }
                );
            },
            
            select : function(event, ui) {
                var fillData = ui.item.fill;
                jQuery.each(fillData, function(index, fillMapping) {
                    var value = fillMapping.value;
                    if(!empty(value)) {
                        jQuery('[name="' + fillMapping.vtigerField + '"]').val(value);
                    }
                });
            }
        });
    },
    
    search : function(value, ruleId) {
        var checkResult = jQuery.Deferred();
        var data = {
            search: value,
            module: 'SPTips',
            action: 'Search',
            ruleId : ruleId
        };
        app.request.post({'data': data}).then(
            function (error, responseObj) {
                if (empty(error) && !empty(responseObj)) {
                    checkResult.resolve(responseObj);
                }
                else {
                    checkResult.reject();  
                }
            },
            function (error) {
                checkResult.reject();
        });
        return checkResult.promise();
    },
    
    checkModuleForTipsRule : function() {
        var thisInstace = this;
        if (app.getViewName() === 'Edit') {
            var params = {
                sourceModule : app.getModuleName(),
                action : 'GetBindingFields',
                module : 'SPTips'
            };
            
            app.request.post({data:params}).then(
                function(err, response){
                    if (err === null) {
                        jQuery.each(response, function(index, data) {
                            thisInstace.makeFieldBinding(data);
                        });
                    }
                },
                
                function(error){

                }
            );
        }
    },
    
    registerEvents: function () {
        this.checkModuleForTipsRule();
    }
});


$(document).ready(function () {
    var tipsController = new SPTips_Js();
    tipsController.registerEvents();
});



