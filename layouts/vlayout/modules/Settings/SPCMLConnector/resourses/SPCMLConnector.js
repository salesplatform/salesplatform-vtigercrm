
jQuery.Class('Settings_SPCMLConnector_Js', {}, {
    
    //This will store the cmlSettings Form
    cmlSettingsForm : false,
   
    /**
     * Function to get the cmlSettings form
     */
    getForm : function() {
            if(this.cmlSettingsForm == false) {
                    this.cmlSettingsForm = jQuery('#cmlSettingsForm');
            }
            return this.cmlSettingsForm;
    },
    
    /**
     * Function to get the statuses settings button
     */
    getStatusesButton : function() {
        return jQuery('#editStatusesSettings');
    },
     
    /*
     * function to save the customer portal settings
     * @params: form - cmlSettings form.
     */
    saveCmlSettings : function(form) {
            var aDeferred = jQuery.Deferred();

            var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                            'enabled' : true
                    }
            });

            var data = form.serializeFormData();
            data['module'] = app.getModuleName();
            data['parent'] = app.getParentModuleName();
            data['action'] = 'Save';

            AppConnector.request(data).then(
                    function(data) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            aDeferred.resolve(data);
                    },
                    function(error) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            //TODO : Handle error
                            aDeferred.reject(error);
                    }
            );
            return aDeferred.promise();
    },
    
    setSubmitEvent : function(e){
        var thisInstance = this;
        var form = thisInstance.getForm();
        var statusesButton = thisInstance.getStatusesButton();
        
        /* Set event on click */
        statusesButton.click(function(e) {
            location.href = 'index.php?module=SPCMLConnector&view=List&parent=Settings';
        });
        
        form.submit(function(e) {
                e.preventDefault();

                //save the cmlSettings
                thisInstance.saveCmlSettings(form).then(
                        function(data) {
                                var result = data['result'];
                                if(result['success']) {
                                        var params = {
                                                text: app.vtranslate('JS_CML_INFO_SAVED')
                                        };
                                        Settings_Vtiger_Index_Js.showMessage(params);
                                }
                        },
                        function(error){
                            var params = {
                                text: app.vtranslate('JS_CML_INFO_SAVE_ERROR')
                            };
                            Settings_Vtiger_Index_Js.showMessage(params);
                        }
                );
        });
    }
});



/* Load page settings */
jQuery(document).ready(function(){
    var instance = new Settings_SPCMLConnector_Js();
    instance.setSubmitEvent();
});


