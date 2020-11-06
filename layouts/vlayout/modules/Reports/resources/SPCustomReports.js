
Reports_Detail_Js("Custom_Reports_Conroller", {}, {
    
    viewTypesData : [],
    
    registerSaveOrGenerateReportEvent : function() {
        var thisInstance = this;
		$('.generateCustomReport').on('click', function(e){
            e.preventDefault();
            var form = thisInstance.getForm();
            var result = form.validationEngine('validate');
            
            if(result === true) {
                var currentMode = $(e.currentTarget).data('mode');
                var postData = thisInstance.getGenerateRequestData(currentMode);
                var progressIndicatorElement = $.progressIndicator();
                AppConnector.request({
                    dataType : 'json',
                    data : postData
                }).then(
                    function(response){
                        progressIndicatorElement.progressIndicator({mode:'hide'});
                        if(response.success) {
                            thisInstance.handleGenerateResponse(response.result);
                        } else {
                            Vtiger_Helper_Js.showPnotify({
                                text : app.vtranslate('JS_ERROR_ON_CUSTOM_REPORT_CALCULATE') + response.error.message
                            });
                        }
                    }, 
                    
                    function(error) {
                        progressIndicatorElement.progressIndicator({mode:'hide'});
                        Vtiger_Helper_Js.showPnotify({
                            text : app.vtranslate('JS_ERROR_ON_CUSTOM_REPORT_CALCULATE')
                        });
                    }
                );
            }
		});
    },
    
    getGenerateRequestData : function(mode) {
        return {
            advanced_filter : this.calculateValues(),
            record : this.getRecordId(),
            view : "SaveAjax",
            module : app.getModuleName(),
            mode : mode,
            displayType : $('#displayType').val(),
            groupBy :  $('#groupingSelect').val(),
            agregateBy : $('#agregateSelect').val(),
            customControls : this.getCustomControls()
        };
    },
    
    getCustomControls : function() {
        var controlsCopy = $('.additionalControls', this.getContentHolder()).clone();
        var virtualForm = $('<form>').append(controlsCopy);
        
        return virtualForm.serializeArray();
        
    },
    
    handleGenerateResponse : function(response) {
        var viewType = this.getViewType();
        this.clearChartContainer();
        this.toggleActionsButtons();
        if(viewType == 'table') {
            this.getContentHolder().find('#reportContentsDiv').html(response.content);
        } else {
            this.getContentHolder().find('#tableReportContents').hide();
            $('input[name=data]', this.getContentHolder()).val(
                JSON.stringify(response.content)
            );
            this.handleLoadView();
        }
    },
    

    registerViewTypeToggle : function() {
        var viewTypesContainer = this.getViewTypeOptionsContainer();
        var thisInstance = this;
        $('#displayType', viewTypesContainer).on('change', function() {
            thisInstance.toggleActionsButtons();
            var selectedView = $(this).val();
            var agregationContainer = thisInstance.getAgregationContainer();
            if(selectedView === 'table')  {
                agregationContainer.hide();
            } else {
                thisInstance.loadSelectedViewAgregations(selectedView);
                agregationContainer.show();
            }
        });
    },
    
    loadSelectedViewAgregations : function(viewName) {
        var agregateList = [];
        var groupList = [];
        if(this.viewTypesData.hasOwnProperty(viewName)) {
            var viewDetails = this.viewTypesData[viewName];
            if(viewDetails.hasOwnProperty('group')) {
                groupList = viewDetails.group;
            }
            
            if(viewDetails.hasOwnProperty('agregate')) {
                agregateList = viewDetails.agregate;
            }
        }
        
        $('#groupingSelect').html('');
        $.each(groupList, function(index, value) {
            $('#groupingSelect').append($('<option/>', {
                value : value,
                text : value
            }));
        });
        $('#groupingSelect').trigger("liszt:updated");
        
        $('#agregateSelect').html('');
        $.each(agregateList, function(index, value) {
            $('#agregateSelect').append($('<option/>', {
                value : index,
                text : value,
                selected : true
            }));
        });
        $('#agregateSelect').trigger("liszt:updated");
    },
    
    getAgregationContainer : function() {
        return $('#agregationContainer');
    },
    
    getViewTypeOptionsContainer : function() {
        return $('#viewTypesContainer');
    },
    
    loadViewTypesData : function() {
        try {
            this.viewTypesData = JSON.parse($('#chartViews').val());
        } catch(e) {
            
        }
    },
    
    clearChartContainer : function() {
        $('.gridster .widgetChartContainer').html('');
    },
    
    handleLoadView : function() {
        var viewType = this.getViewType();
        this.toggleActionsButtons();
        if(viewType != 'table') {
            if(this.checkForEmptyData()) {
                var renderer = this.getChartRenderer(viewType);
                renderer.loadChart();
            } else {
                $('.gridster .widgetChartContainer').html(
                    '<div style="text-align: center; margin-top: 70px; font-weight: bold; font-size: 18px;">' + app.vtranslate('JS_LBL_NO_DATA_FOR_SELECTED_FILTERS') + '<div>'
                );
            }
        }
    },
    
    checkForEmptyData : function() {
        var viewType = this.getViewType();
        if(viewType != 'table') {
            var chartData = {};
            try {
                chartData = JSON.parse($('input[name=data]', this.getContentHolder()).val());
            } catch(e) {

            }
            
            return (chartData.hasOwnProperty('values'));
        }
        
        return true;
    },
    
    toggleActionsButtons : function() {
        var viewType = this.getViewType();
        if(viewType == 'table') {
            $('[data-href*="GetXLS"]').show();
            $('[data-href*="GetCSV"]').show();
            $('[data-href*="GetPrintReport"]').show();
            
        } else {
            $('[data-href*="GetXLS"]').hide();
            $('[data-href*="GetCSV"]').hide();
            $('[data-href*="GetPrintReport"]').hide();
        }
    },
    
    getViewType : function() {
      return $('#displayType').val();
    },
    
    getChartRenderer : function(viewType) {
        var container = $('.gridster', this.getContentHolder());
        var renderer = null;
        switch(viewType) {
            case 'pie':
                renderer = new Report_Piechart_Js(container);
                break;
                
            case 'barchart':
                renderer = new Report_Verticalbarchart_Js(container);
                break;
                
            case 'linear':
                renderer = new Report_Linechart_Js(container);
                break;
        }
        
        return renderer;
    },
    
    registerEventsForActions : function() {
        var thisInstance = this;
        $('.reportActions').click(function(e){
            var element = jQuery(e.currentTarget); 
            var href = element.data('href');
            var type = element.attr("name");
            var advFilterCondition = thisInstance.calculateValues();
            var headerContainer = thisInstance.getHeaderContentsHolder();
            if(type.indexOf("Print") != -1){
                var newEle = '<form action='+href+' method="POST" target="_blank">'+
                        '<input type = "hidden" name ="'+csrfMagicName+'"  value=\''+csrfMagicToken+'\'>'+
                    '<input type="hidden" value="" name="advanced_filter" id="advanced_filter" /></form>'; 
            }else{
                newEle = '<form action='+href+' method="POST">'+
                        '<input type = "hidden" name ="'+csrfMagicName+'"  value=\''+csrfMagicToken+'\'>'+
                    '<input type="hidden" value="" name="advanced_filter" id="advanced_filter" /></form>'; 
            }
            var ele = jQuery(newEle); 
            var form = ele.appendTo(headerContainer);
            form.find('#advanced_filter').val(advFilterCondition); 
            form.append('<input name="displayType" type="hidden" value="' + $('#displayType').val() + '">');
            form.append('<input name="groupBy" type="hidden" value="' + $('#groupingSelect').val() + '">');
            form.append('<input name="agregateBy" type="hidden" value="' + $('#agregateSelect').val() + '">');
            form.append('<input name="customControls" type="hidden" value=\'' + JSON.stringify(thisInstance.getCustomControls()) + '\'>');
            form.submit();
        });
    },
    
    registerEvents : function() {
        this._super(true);

        app.changeSelectElementView(this.getViewTypeOptionsContainer());
        this.loadViewTypesData();
        this.registerViewTypeToggle();
        this.handleLoadView();
        this.registerEventsForActions();
    }
});

