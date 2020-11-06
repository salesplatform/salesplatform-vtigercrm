Settings_Vtiger_Index_Js("Settings_SPVoipIntegration_Index_Js", {}, {
    
    getDefaultProviderElement : function() {
        return jQuery('[name="default_provider"]');
    },
    
    registerEditViewEvents: function () {
        var thisInstance = this;
        var form = jQuery('#voipEditFrom');
        var cancelLink = jQuery('.cancelLink', form);

        var params = {
            submitHandler: function (form) {
                var form = jQuery(form);
                form.find('[name="saveButton"]').attr('disabled', 'disabled');
                thisInstance.saveConfigDetails(form);
            }
        }
        form.vtValidate(params);

        form.submit(function (e) {
            e.preventDefault();
        });

        cancelLink.click(function (e) {
            var voipSettingsDetailUrl = form.data('detailUrl');
            app.helper.showProgress();

            thisInstance.loadContents(voipSettingsDetailUrl).then(
                    function (data) {
                        app.helper.hideProgress();
                        thisInstance.registerDetailViewEvents();
                    },
                    function (error, err) {
                        app.helper.hideProgress();
                    }
            );
        });
        
        this.registerDefaultProviderActions();
        this.registerZebraWebhooks();
    },
    
    registerDefaultProviderActions : function() {
        var defaultProviderElement = this.getDefaultProviderElement();
        
        app.showSelect2ElementView(defaultProviderElement);
        defaultProviderElement.on("change", function() {
            var selectedProvider = $(this).val();
            
            $(".providerData").each(function() {
                var currentElement = $(this);
                if( currentElement.data("provider") === selectedProvider) {
                    currentElement.removeClass("hide");
                } else {
                    currentElement.addClass("hide");
                }
            });
        });
    },
    
    saveConfigDetails: function (form) {
        var thisInstance = this;
        var data = form.serializeFormData();

        app.helper.showProgress();
        if (typeof data == 'undefined') {
            data = {};
        }

        data.module = app.getModuleName();
        data.parent = app.getParentModuleName();
        data.action = 'SaveAjax';

        app.request.post({data: data}).then(
                function (err, data) {
                    if (data) {
                        var detailUrl = form.data('detailUrl');
                        thisInstance.loadContents(detailUrl).then(
                                function (data) {
                                    app.helper.hideProgress();
                                    thisInstance.registerDetailViewEvents();
                                },
                                function (error, err) {
                                    app.helper.hideProgress();
                                }
                        );
                    } else {
                        app.helper.hideProgress();
                        jQuery('.errorMessage', form).removeClass('hide');
                    }
                },
                function (error, errorThrown) {
                }
        );
    },
    registerDetailViewEvents: function () {
        var thisInstance = this;
        var editButton = jQuery('.editButton');
        thisInstance.registerZebraWebhooks();
        editButton.click(function (e) {
            var url = jQuery(e.currentTarget).data('url');
            app.helper.showProgress();

            thisInstance.loadContents(url).then(
                    function (data) {
                        thisInstance.registerEditViewEvents();
                        app.helper.hideProgress();
                    },
                    function (error, err) {
                        app.helper.hideProgress();
                    }
            );
        });
    },
    loadContents: function (url) {
        var aDeferred = jQuery.Deferred();
        app.request.get({url: url}).then(
                function (err, data) {
                    jQuery('.settingsPageDiv').html(data);
                    aDeferred.resolve(data);
                },
                function (error, err) {
                    aDeferred.reject();
                }
        );
        return aDeferred.promise();
    },
    
    registerZebraWebhooks: function () {        
        jQuery('#registerWebhooks').on('click', function () {            
            var params = {};
            params.module = app.getModuleName();
            params.parent = app.getParentModuleName();
            params.action = 'RegisterWebhooks';
            params.provider = 'zebra';
            app.request.post({data: params}).then(
                    function (error, data) {                        
                        var params = {};
                        if (error) {
                            params = {
                                title: error.message,
                                type: 'error'
                            }                                           
                        } else {
                            params = {
                                title: app.vtranslate('JS_WEBHOOKS_REGISTERED'),
                                type: 'info'
                            }
                        }
                        Vtiger_Helper_Js.showPnotify(params);             
                    },
                    function (error, err) {                        
                        app.helper.hideProgress();
                    }
            );
        });
    },
    
    registerEvents: function () {
        this.registerDetailViewEvents();
    }

});

jQuery(document).ready(function (e) {
    var instance = new Settings_Vtiger_Index_Js();
    instance.registerEvents();
});