/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery.Class('Settings_SPCMLConnector_List_Js', {
    
    //holds the currency instance
    cmlInstance : false,

    /**
     * This function used to Add cmlRecord
     */
    triggerAdd : function(event) {
            event.stopPropagation();
            var instance = Settings_SPCMLConnector_List_Js.cmlInstance;
            instance.showEditView();
    },

    /**
     * This function used to trigger Edit Cml Record
     */
    triggerEdit : function(event, id) {
            event.stopPropagation();
            var instance = Settings_SPCMLConnector_List_Js.cmlInstance;
            instance.showEditView(id);
    },
    
    /**
     * This function used to trigger Delete Cml Record
     */
    triggerDelete : function(event, id) {
            event.stopPropagation();
            var instance = Settings_SPCMLConnector_List_Js.cmlInstance;
            instance.deleteStatus(id);
    }
}, 

{  
    /**
     * Constructor of Settings_SPCMLConnector_Js
     */
    init : function() {
            Settings_SPCMLConnector_List_Js.cmlInstance = this;
    },
    
    /*
     * function to show editView for Add/Edit Currency
     * @params: id - currencyId
     */
    showEditView : function(id) {
            var thisInstance = this;
            var aDeferred = jQuery.Deferred();

            var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                            'enabled' : true
                    }
            });

            var params = {};
            params['module'] = app.getModuleName();
            params['parent'] = app.getParentModuleName();
            params['view'] = 'EditStatus';
            params['record'] = id;
            
            /* Send request and get answer - form to edit */
            AppConnector.request(params).then(
                    function(data) {
                        var callBackFunction = function(data) {
                            var form = jQuery('#editStatus');
                            
                            var params = app.validationEngineOptions;
                            params.onValidationComplete = function(form, valid){
                                    if(valid) {
                                            thisInstance.saveStatus(form);
                                            return valid;
                                    }
                            }
                            
                            form.validationEngine(params);

                            form.submit(function(e) {
                                    e.preventDefault();
                            })
                        };
                                    
                        /* Show edit form with setted callbacks to save event*/
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        app.showModalWindow(data,function(data){
                                                     if(typeof callBackFunction == 'function'){
                                                             callBackFunction(data);
                                                     }
                                                 }, {'width':'600px'});
                    },
                    function(error) {
                        //TODO : Handle error
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        aDeferred.reject(error);
                    }
            );
            
            return aDeferred.promise();
    },
    
    saveStatus: function(form) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                        'enabled' : true
                }
        });

        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['parent'] = app.getParentModuleName();
        data['action'] = 'SaveStatus';
        
        /* Send save request */
        AppConnector.request(data).then(
			function(data) {
				if(data['success']) {
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					app.hideModalWindow();
					var params = {};
					params.text = app.vtranslate('JS_STATUS_DETAILS_SAVED');
					Settings_Vtiger_Index_Js.showMessage(params);
					
                                        /* Reload content on list view - get this page */
                                        thisInstance.loadListViewContents();
				} 
			},
			function(error) {
				progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                var params = {};
                                params.text = app.vtranslate('JS_STATUS_SAVE_FAIL');
                                Settings_Vtiger_Index_Js.showMessage(params);
				//TODO : Handle error
			}
		);
    },
    
    deleteStatus : function(id) {
        var thisInstance = this;
        
        /* Show process */
        var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                        'enabled' : true
                }
        });
            
        var params = {};
        params['module'] = app.getModuleName();
        params['parent'] = app.getParentModuleName();
        params['action'] = 'DeleteStatus';
        params['record'] = id;
        
        /* Request to delete */
        AppConnector.request(params).then(
			function(data) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            var params = {};
                            params.text = app.vtranslate('JS_STATUS_DELETED');
                            Settings_Vtiger_Index_Js.showMessage(params);
                            
                            /* Reload content */
                            thisInstance.loadListViewContents();
                            
			}, function(error, err) {
                            //TODO handle error
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            var params = {};
                            params.text = app.vtranslate('JS_STATUS_DELETE_FAIL');
                            Settings_Vtiger_Index_Js.showMessage(params);
			});
    },
    
    /**
     * This function will load the listView contents after Add/Edit currency
     */
    loadListViewContents : function() {
            var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                            'enabled' : true
                    }
            });

            var params = {};
            params['module'] = app.getModuleName();
            params['parent'] = app.getParentModuleName();
            params['view'] = 'List';

            AppConnector.request(params).then(
                    function(data) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            jQuery('#listViewContents').html(data);
                    }, function(error, err) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    }
            );
    }
    
});



jQuery(document).ready(function(){
    var cmlInstance = new Settings_SPCMLConnector_List_Js();
    
});
