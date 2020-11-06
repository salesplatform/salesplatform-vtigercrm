/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class('Vtiger_Widget_Js',{

	widgetPostLoadEvent : 'Vtiget.Dashboard.PostLoad',
	widgetPostRefereshEvent : 'Vtiger.Dashboard.PostRefresh',
    widgetPostResizeEvent : 'Vtiger.DashboardWidget.PostResize',

	getInstance : function(container, widgetName, moduleName) {
		if(typeof moduleName == 'undefined') {
			moduleName = app.getModuleName();
		}
		var widgetClassName = widgetName;
		var moduleClass = window[moduleName+"_"+widgetClassName+"_Widget_Js"];
		var fallbackClass = window["Vtiger_"+widgetClassName+"_Widget_Js"];
		var basicClass = Vtiger_Widget_Js;
		if(typeof moduleClass != 'undefined') {
			var instance = new moduleClass(container);
		}else if(typeof fallbackClass != 'undefined') {
			var instance = new fallbackClass(container);
		} else {
			var instance = new basicClass(container);
		}
		return instance;
	}
},{

	container : false,
	plotContainer : false,

	init : function (container) {
		this.setContainer(jQuery(container));
		this.registerWidgetPostLoadEvent(container);
		this.registerWidgetPostRefreshEvent(container);
        this.registerWidgetPostResizeEvent(container); 
	},

	getContainer : function() {
		return this.container;
	},

	setContainer : function(element) {
		this.container = element;
		return this;
	},

	isEmptyData : function() {
		var container = this.getContainer();
		return (container.find('.noDataMsg').length > 0) ? true : false;
	},

	getUserDateFormat : function() {
		return jQuery('#userDateFormat').val();
	},


	getPlotContainer : function(useCache) {
		if(typeof useCache == 'undefined'){
			useCache = false;
		}
		if(this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = container.find('.widgetChartContainer');
		}
		return this.plotContainer;
	},

	restrictContentDrag : function(){
		this.getContainer().on('mousedown.draggable', function(e){
			var element = jQuery(e.target);
			var isHeaderElement = element.closest('.dashboardWidgetHeader').length > 0 ? true : false;
            var isResizeElement = element.is(".gs-resize-handle") ? true : false;
			if(isHeaderElement || isResizeElement){
				return;
			}
			//Stop the event propagation so that drag will not start for contents
			e.stopPropagation();
		})
	},

	convertToDateRangePicketFormat : function(userDateFormat) {
		if(userDateFormat == 'yyyy-mm-dd') {
			return 'yyyy-MM-dd';
		}else if( userDateFormat == 'mm-dd-yyyy') {
			return 'MM-dd-yyyy';
		}else if(userDateFormat == 'dd-mm-yyyy') {
			return 'dd-MM-yyyy';
		}
	},

	loadChart : function() {

	},

	positionNoDataMsg : function() {
		var container = this.getContainer();
		var widgetContentsContainer = container.find('.dashboardWidgetContent');
        widgetContentsContainer.height(container.height()- 50);
		var noDataMsgHolder = widgetContentsContainer.find('.noDataMsg');
		noDataMsgHolder.position({
				'my' : 'center center',
				'at' : 'center center',
				'of' : widgetContentsContainer
		})
	},
    
    postInitializeCalls : function() {},

	//Place holdet can be extended by child classes and can use this to handle the post load
	postLoadWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
			//this.positionNoDataMsg();
		}
		this.registerFilter();
		this.registerFilterChangeEvent();
		this.restrictContentDrag();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        widgetContent.css({height: widgetContent.height()-40});
	},
    
	postResizeWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
			//this.positionNoDataMsg();
		}
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        widgetContent.css({height: widgetContent.height()-40});
	},

	postRefreshWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
//			this.positionNoDataMsg();
		}
	},

	getFilterData : function() {
		return {};
	},

	refreshWidget : function() {
		var parent = this.getContainer();
		var element = parent.find('a[name="drefresh"]');
		var url = element.data('url');

        var contentContainer = parent.find('.dashboardWidgetContent');
		var params = {};
        params.url = url;
		var widgetFilters = parent.find('.widgetFilter');
		if(widgetFilters.length > 0) {
			params.url = url;
			params.data = {};
			widgetFilters.each(function(index, domElement){
				var widgetFilter = jQuery(domElement);
                //Filter unselected checkbox, radio button elements
                if((widgetFilter.is(":radio") || widgetFilter.is(":checkbox")) && !widgetFilter.is(":checked")){
                    return true;
                }
				if(widgetFilter.is('.dateRange')){
					var name = widgetFilter.attr('name');
                    var start = widgetFilter.find('input[name="start"]').val();
                    var end = widgetFilter.find('input[name="end"]').val();
                    if(start.length <= 0 || end.length <= 0  ){
                        return true;
                    } 
                    
					params.data[name] = {};
					params.data[name].start = start;
					params.data[name].end = end;
				}else{
					var filterName = widgetFilter.attr('name');
					var filterValue = widgetFilter.val();
					params.data[filterName] = filterValue;
				}
			});
		}
		var filterData = this.getFilterData();
		if(! jQuery.isEmptyObject(filterData)) {
			if(typeof params == 'string') {
				url = params;
				params = {};
				params.url = url;
				params.data = {};
			}
			params.data = jQuery.extend(params.data, this.getFilterData())
		}
		
		//Sending empty object in data results in invalid request
		if(jQuery.isEmptyObject(params.data)) {
			delete params.data;
		}
		
		app.helper.showProgress();
		app.request.post(params).then(
			function(err,data){
                app.helper.hideProgress();
				
				if(contentContainer.closest('.mCustomScrollbar').length) {
					contentContainer.mCustomScrollbar('destroy');
					contentContainer.html(data);
					var adjustedHeight = parent.height()-100;
					app.helper.showVerticalScroll(contentContainer,{'setHeight' : adjustedHeight});
				}else {
					contentContainer.html(data);
				}
                
                /**
                 * we are setting default height in DashBoardWidgetContents.tpl
                 * need to overwrite based on resized widget height 
                 */ 
                var widgetChartContainer = contentContainer.find(".widgetChartContainer");
                if(widgetChartContainer.length > 0){
                    widgetChartContainer.css("height",parent.height() - 60);
                }
				contentContainer.trigger(Vtiger_Widget_Js.widgetPostRefereshEvent);
			}
		);
	},

	registerFilter : function() {
		var thisInstance = this;
		var container = this.getContainer();
		var dateRangeElement = container.find('.input-daterange');
		if(dateRangeElement.length <= 0) {
			return;
		}
		
		dateRangeElement.addClass('dateField');
		
		var pickerParams = {
            format : thisInstance.getUserDateFormat(),
        };
		vtUtils.registerEventForDateFields(dateRangeElement, pickerParams);
		
        dateRangeElement.on("changeDate", function(e){
           var start = dateRangeElement.find('input[name="start"]').val();
           var end = dateRangeElement.find('input[name="end"]').val();
           if(start != '' && end != '' && start !== end){
               container.find('a[name="drefresh"]').trigger('click');
           }
        });
		dateRangeElement.attr('data-date-format',thisInstance.getUserDateFormat());
	},

	registerFilterChangeEvent : function() {
		this.getContainer().on('change', '.widgetFilter, .reloadOnChange', function(e) {
			var target = jQuery(e.currentTarget);
			if(target.hasClass('dateRange')) {
				var start = target.find('input[name="start"]').val();
				var end = target.find('input[name="end"]').val();
				if(start == '' || end == '') return false;
			}
			
			var widgetContainer = target.closest('li');
			widgetContainer.find('a[name="drefresh"]').trigger('click');
		})
	},

	registerWidgetPostLoadEvent : function(container) {
		var thisInstance = this;
		container.off(Vtiger_Widget_Js.widgetPostLoadEvent).on(Vtiger_Widget_Js.widgetPostLoadEvent, function(e) {
			thisInstance.postLoadWidget();
		})
	},

	registerWidgetPostRefreshEvent : function(container) {
		var thisInstance = this;
        //SalesPlatform.ru begin
		container.off(Vtiger_Widget_Js.widgetPostRefereshEvent).on(Vtiger_Widget_Js.widgetPostRefereshEvent, function(e) {
        //container.on(Vtiger_Widget_Js.widgetPostRefereshEvent, function(e) {
        //SalesPlatform.ru end    
			thisInstance.postRefreshWidget();
		});
	},
    
    registerWidgetPostResizeEvent : function(container){
        var thisInstance = this;
		container.on(Vtiger_Widget_Js.widgetPostResizeEvent, function(e) { 
			thisInstance.postResizeWidget();
		});
    },
    
    openUrl : function(url) {
        var win = window.open(url, '_blank');
        win.focus();
    }
});


Vtiger_Widget_Js('Vtiger_KeyMetrics_Widget_Js', {}, {
    postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
	},

	postResizeWidget: function () {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
		var adjustedHeight = this.getContainer().height() - 20;
		widgetContent.css({height: adjustedHeight});
		slimScrollDiv.css({height: adjustedHeight});
	}
});

Vtiger_Widget_Js('Vtiger_TopPotentials_Widget_Js', {}, {
    
   postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
	}
});

Vtiger_Widget_Js('Vtiger_History_Widget_Js', {}, {

	postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
        //this.initSelect2Elements(widgetContent);
		this.registerLoadMore();
	},
    
    postResizeWidget: function() {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
	},
        
	initSelect2Elements : function(widgetContent) {
		var container = widgetContent.closest('.dashboardWidget');
		var select2Elements = container.find('.select2');
		if(select2Elements.length > 0 && jQuery.isArray(select2Elements)) {
			select2Elements.each(function(index, domElement){
				domElement.chosen();
			});
		}else{
			select2Elements.chosen();
		}
	},

	postRefreshWidget: function() {
		this._super();
		this.registerLoadMore();
	},

	registerLoadMore: function() {
		var thisInstance  = this;
		var parent = thisInstance.getContainer();
		var contentContainer = parent.find('.dashboardWidgetContent');

		var loadMoreHandler = contentContainer.find('.load-more');
		loadMoreHandler.off('click');
		loadMoreHandler.click(function(){
			var parent = thisInstance.getContainer();
			var element = parent.find('a[name="drefresh"]');
			var url = element.data('url');
			var params = url;

			var widgetFilters = parent.find('.widgetFilter');
			if(widgetFilters.length > 0) {
				params = { url: url, data: {}};
				widgetFilters.each(function(index, domElement){
					var widgetFilter = jQuery(domElement);
					//Filter unselected checkbox, radio button elements
					if((widgetFilter.is(":radio") || widgetFilter.is(":checkbox")) && !widgetFilter.is(":checked")){
						return true;
					}
					
					if(widgetFilter.is('.dateRange')) {
						var name = widgetFilter.attr('name');
						var start = widgetFilter.find('input[name="start"]').val();
						var end = widgetFilter.find('input[name="end"]').val();
						if(start.length <= 0 || end.length <= 0  ){
							return true;
						} 

						params.data[name] = {};
						params.data[name].start = start;
						params.data[name].end = end;
					} else {
						var filterName = widgetFilter.attr('name');
						var filterValue = widgetFilter.val();
						params.data[filterName] = filterValue;
					}
				});
			}

			var filterData = thisInstance.getFilterData();
			if(! jQuery.isEmptyObject(filterData)) {
				if(typeof params == 'string') {
					params = { url: url, data: {}};
				}
				params.data = jQuery.extend(params.data, thisInstance.getFilterData())
			}

			// Next page.
			params.data['page'] = loadMoreHandler.data('nextpage');

            app.helper.showProgress();
			app.request.post(params).then(function(err,data){
				app.helper.hideProgress();
				loadMoreHandler.parent().parent().replaceWith(jQuery(data).html());
				thisInstance.registerLoadMore();
			}, function(){
				app.helper.hideProgress();
			});
		});
	}

});


Vtiger_Widget_Js('Vtiger_Funnel_Widget_Js',{},{

    postInitializeCalls: function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },
    
    //SalesPlatform.ru begin
    postLoadWidget: function() {
        this._super();
        var thisInstance = this;

        this.getContainer().on('jqplotDataClick', function(ev, gridpos, datapos, neighbor, plot) {
            var jData = thisInstance.getContainer().find('.widgetData').val(),
                data = JSON.parse(jData),
                linkUrl = data[datapos][3];
            if(linkUrl) {
                window.location.href = linkUrl;
            }
        });

        this.getContainer().on("jqplotDataHighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas').css( 'cursor', 'pointer' );
        });
        this.getContainer().on("jqplotDataUnhighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas').css( 'cursor', 'auto' );
        });
    },
    //SalesPlatform.ru end
    
    generateLinks : function() {
        var data = this.getContainer().find('.widgetData').val();
        var parsedData = JSON.parse(data);
        var linksData = [];
        for(var index in parsedData) {
            var newData = {};
            var itemDetails = parsedData[index];
            newData.name = itemDetails[0];
            newData.links = itemDetails[3];
            linksData.push(newData);
        }
        return linksData;
    },
    
    //SalesPlatform.ru begin
    /*
    loadChart : function() {
		var container = this.getContainer();
		var data = container.find('.widgetData').val();
        var chartOptions = {
            renderer:'funnel',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(data,chartOptions);
    }
    */

    loadChart : function() {
        var container = this.getContainer();
            data = container.find('.widgetData').val(),
            labels = new Array(),
            dataInfo = JSON.parse(data),
            data = [];
        for(var i=0; i<dataInfo.length; i++) {
            labels[i] = dataInfo[i][2];
            dataInfo[i][1] = parseFloat(dataInfo[i][1]);
            data.push([]);
            $.each(dataInfo[i], function(index, value) {
                data[i].push(value);
            }); 
        }

        this.getPlotContainer(false).jqplot([data],  {
            seriesDefaults: {
                renderer:jQuery.jqplot.FunnelRenderer,
                rendererOptions:{
                    sectionMargin: 6,
                    widthRatio: 0.1,
                    showDataLabels:true,
                    dataLabelThreshold: 0,
                    dataLabels: 'value',
                    sort: false 
                }
            },
            legend: {
                show: true,
                //SalesPlatform.ru begin
                location: 'e',
                placement: 'outsideGrid',
                //location: 'en',
                //placement: 'outside',
                //SalesPlatform.ru end
                labels:labels,
                xoffset:20
            }
        });
    }
    //SalesPlatform.ru end
    
});



Vtiger_Widget_Js('Vtiger_Pie_Widget_Js',{},{

	/**
	 * Function which will give chart related Data
	 */
	generateData : function() {
		var container = this.getContainer();
		var jData = container.find('.widgetData').val();
		var data = JSON.parse(jData);
		var chartData = [];
		for(var index in data) {
			var row = data[index];
			var rowData = [row.last_name, parseFloat(row.amount), row.id];
			chartData.push(rowData);
		}
		return {'chartData':chartData};
	},
    
    generateLinks : function() {
        var jData = this.getContainer().find('.widgetData').val();
        var statData = JSON.parse(jData);
        var links = [];
        for(var i = 0; i < statData.length ; i++){
            links.push(statData[i]['links']);
        }
        return links;
    },
    
    postInitializeCalls: function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },

	loadChart : function() {
            
        //SalesPlatform.ru begin
            var chartData = this.generateData();

            this.getPlotContainer(false).jqplot([chartData['chartData']], {
                seriesDefaults:{
                    renderer:jQuery.jqplot.PieRenderer,
                    rendererOptions: {
                        showDataLabels: true,
                        dataLabels: 'value'
                    }
                },
                legend: {
                    show: true,
                    location: 'e'
                },
                title : chartData['title']
            });
                /*
        var chartData = this.generateData();
        var chartOptions = {
            renderer:'pie',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(chartData,chartOptions);
        */
        //SalesPlatform.ru end
	}
});


Vtiger_Widget_Js('Vtiger_Barchat_Widget_Js',{},{

	generateChartData : function() {
		var container = this.getContainer();
		var jData = container.find('.widgetData').val();
		var data = JSON.parse(jData);
		var chartData = [];
		var xLabels = new Array();
		var yMaxValue = 0;
		for(var index in data) {
			var row = data[index];
			row[0] = parseFloat(row[0]);
			xLabels.push(app.getDecodedValue(row[1]));
			chartData.push(row[0]);
			if(parseInt(row[0]) > yMaxValue){
				yMaxValue = parseInt(row[0]);
			}
		}
        // yMaxValue Should be 25% more than Maximum Value
		yMaxValue = yMaxValue + 2 + (yMaxValue/100)*25;
		return {'chartData':[chartData], 'yMaxValue':yMaxValue, 'labels':xLabels};
	},
    
    generateLinks : function() {
        var container = this.getContainer();
        var jData = container.find('.widgetData').val();
        var statData = JSON.parse(jData);
        var links = [];
        for(var i = 0; i < statData.length ; i++){
            links.push(statData[i]['links']);
        }
        return links;
    },
    
    postInitializeCalls : function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },

    loadChart : function() {
        var data = this.generateChartData();
        //SalesPlatform.ru begin
        var chartData = data['chartData'];
        var yaxis = {
            min:0,
            max: data['yMaxValue'],
            tickOptions: {
                formatString: '%.2f'
            },
            pad : 1.2
        };
        
        /* we using format string with %d, so if max value less than 5
         * y-axis values will be duplicated (0,1,1,2,2,3,..) instead of (0,1,2,3,...)
         */
        yaxis['numberTicks'] = 5;

        this.getPlotContainer(false).jqplot(chartData , {
            title: data['title'],
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:jQuery.jqplot.BarRenderer,
                rendererOptions: {
                showDataLabels: true,
                dataLabels: 'value',
                barDirection : 'vertical'
            },
            pointLabels: {
                show: true,edgeTolerance: -15}
            },
            axes: {
                xaxis: {
                    tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer,
                    renderer: jQuery.jqplot.CategoryAxisRenderer,
                    ticks: data['labels'],
                    tickOptions: {
                        angle: -45
                    }
                },
                yaxis: yaxis
            },
            legend: {
                show            : (data['data_labels']) ? true:false,
                location	: 'e',
                //SalesPaltform.ru begin
                placement	: (data['data_labels']) ? 'outsideGrid' : 'outside',
                //placement	: 'outside',
                //SalesPatlform.ru end
                showLabels	: (data['data_labels']) ? true:false,
                showSwatch	: (data['data_labels']) ? true:false,
                labels		: data['data_labels']
            }
        });
            
        /*
        var data = this.generateChartData();
        var chartOptions = {
            renderer:'bar',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(data,chartOptions);
        */
        //SalesPlatform.ru end
    }
    
});

Vtiger_Widget_Js('Vtiger_MultiBarchat_Widget_Js',{

	/**
	 * Function which will give char related Data like data , x labels and legend labels as map
	 */
	getCharRelatedData : function() {
		var container = this.getContainer();
		var data = container.find('.widgetData').val();
		var users = new Array();
		var stages = new Array();
		var count = new Array();
		for(var i=0; i<data.length;i++) {
			if($.inArray(data[i].last_name, users) == -1) {
				users.push(data[i].last_name);
			}
			if($.inArray(data[i].sales_stage, stages) == -1) {
				stages.push(data[i].sales_stage);
			}
		}

		for(j in stages) {
			var salesStageCount = new Array();
			for(i in users) {
				var salesCount = 0;
				for(var k in data) {
					var userData = data[k];
					if(userData.sales_stage == stages[j] && userData.last_name == users[i]) {
						salesCount = parseInt(userData.count);
						break;
					}
				}
				salesStageCount.push(salesCount);
			}
			count.push(salesStageCount);
		}
		return {
			'data' : count,
			'ticks' : users,
			'labels' : stages
		}
	},
    
    postInitializeCalls : function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },
    
	loadChart : function(){
		var chartRelatedData = this.getCharRelatedData();
        var chartOptions = {
            renderer:'multibar',
            links:chartRelatedData.links
        };
        this.getPlotContainer(false).data('widget-data',JSON.stringify(this.getCharRelatedData()));
        this.getPlotContainer(false).vtchart(chartRelatedData,chartOptions);
	}

});

// NOTE Widget-class name camel-case convention
Vtiger_Widget_Js('Vtiger_MiniList_Widget_Js', {
    
    registerMoreClickEvent : function(e) {
        var moreLink = jQuery(e.currentTarget);
        var linkId = moreLink.data('linkid');
        var widgetId = moreLink.data('widgetid');
        var currentPage = jQuery('#widget_'+widgetId+'_currentPage').val();
        var nextPage = parseInt(currentPage) + 1;
        var params = {
            'module' : app.getModuleName(),
            'view' : 'ShowWidget',
            'name' : 'MiniList',
            'linkid' : linkId,
            'widgetid' : widgetId,
            'content' : 'data',
            'currentPage' : currentPage
        }
        app.request.post({"data":params}).then(function(err,data) {
            var htmlData = jQuery(data);
            var htmlContent = htmlData.find('.miniListContent');
            moreLink.parent().before(htmlContent);
            jQuery('#widget_'+widgetId+'_currentPage').val(nextPage);
            var moreExists = htmlData.find('.moreLinkDiv').length;
            if(!moreExists) {
                moreLink.parent().remove();
            }
        });
    }
    
}, {
	postLoadWidget: function() {
        app.helper.hideModal();
        this.restrictContentDrag();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        widgetContent.css({height: widgetContent.height()-40});
	},
    
    postResizeWidget: function() {
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
	}
});

Vtiger_Widget_Js('Vtiger_TagCloud_Widget_Js',{},{

	postLoadWidget : function() {
		this._super();
		this.registerTagCloud();
		this.registerTagClickEvent();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        widgetContent.css({height: widgetContent.height()-40});
	},

	registerTagCloud : function() {
		jQuery('#tagCloud').find('a').tagcloud({
			size: {
			  start: parseInt('12'),
			  end: parseInt('30'),
			  unit: 'px'
			},
			color: {
			  start: "#0266c9",
			  end: "#759dc4"
			}
		});
	},

	registerChangeEventForModulesList : function() {
		jQuery('#tagSearchModulesList').on('change',function(e) {
			var modulesSelectElement = jQuery(e.currentTarget);
			if(modulesSelectElement.val() == 'all'){
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
			} else{
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
				var selectedOptionValue = modulesSelectElement.val();
				jQuery('[name="tagSearchModuleResults"]').filter(':not(#'+selectedOptionValue+')').addClass('hide');
			}
		});
	},

	registerTagClickEvent : function(){
		var thisInstance = this;
		var container = this.getContainer();
		container.on('click','.tagName',function(e) {
			var tagElement = jQuery(e.currentTarget);
			var tagId = tagElement.data('tagid');
			var params = {
				'module' : app.getModuleName(),
				'view' : 'TagCloudSearchAjax',
				'tag_id' : tagId,
				'tag_name' : tagElement.text()
			}
			app.request.post({"data":params}).then(
				function(err,data) {
                    app.helper.showModal(data);
                    vtUtils.applyFieldElementsView(jQuery(".myModal"));
					thisInstance.registerChangeEventForModulesList();
				}
			)
		});
	},

	postRefreshWidget : function() {
		this._super();
		this.registerTagCloud();
	},

	postResizeWidget: function () {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
		var adjustedHeight = this.getContainer().height() - 20;
		widgetContent.css({height: adjustedHeight});
		slimScrollDiv.css({height: adjustedHeight});
	}
});

/* Notebook Widget */
Vtiger_Widget_Js('Vtiger_Notebook_Widget_Js', {

}, {

	// Override widget specific functions.
	postLoadWidget: function() {
		this.reinitNotebookView();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        //widgetContent.css({height: widgetContent.height()-40});
	},
    
    postResizeWidget: function() {
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
        widgetContent.find('.dashboard_notebookWidget_viewarea').css({height:adjustedHeight});
	},
    
    postRefreshWidget : function() {
        this.reinitNotebookView();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
    },
    
	reinitNotebookView: function() {
		var self = this;
		jQuery('.dashboard_notebookWidget_edit', this.container).click(function(){
			self.editNotebookContent();
		});
		jQuery('.dashboard_notebookWidget_save', this.container).click(function(){
			self.saveNotebookContent();
		});
	},

	editNotebookContent: function() {
		jQuery('.dashboard_notebookWidget_text', this.container).show();
		jQuery('.dashboard_notebookWidget_view', this.container).hide();
	},

	saveNotebookContent: function() {
		var self = this;
		var refreshContainer = this.container.find('.refresh');
		var textarea = jQuery('.dashboard_notebookWidget_textarea', this.container);

		var url = this.container.data('url');
		var params = url + '&content=true&mode=save&contents=' + textarea.val();

		app.helper.showProgress();
		app.request.post({"url":params}).then(function(err,data) {
            app.helper.hideProgress();
			var parent = self.getContainer();
			var widgetContent = parent.find('.dashboardWidgetContent');
			widgetContent.mCustomScrollbar('destroy');
			widgetContent.html(data);
			var adjustedHeight = parent.height() - 50;
			app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
			
			self.reinitNotebookView();
		});
	},
	
	refreshWidget : function() {
		var parent = this.getContainer();
		var element = parent.find('a[name="drefresh"]');
		var url = element.data('url');

        var contentContainer = parent.find('.dashboardWidgetContent');
		var params = {};
        params.url = url;
		
		app.helper.showProgress();
		app.request.post(params).then(
			function(err,data){
                app.helper.hideProgress();
				
				if(contentContainer.closest('.mCustomScrollbar').length) {
					contentContainer.mCustomScrollbar('destroy');
					contentContainer.html(data);
					var adjustedHeight = parent.height()-50;
					app.helper.showVerticalScroll(contentContainer,{'setHeight' : adjustedHeight});
				}else {
					contentContainer.html(data);
				}
                
				contentContainer.trigger(Vtiger_Widget_Js.widgetPostRefereshEvent);
			}
		);
	},
});

Vtiger_History_Widget_Js('Vtiger_OverdueActivities_Widget_Js', {}, {

	registerLoadMore: function() {
		var thisInstance  = this;
		var parent = thisInstance.getContainer();
        parent.off('click', 'a[name="history_more"]'); 
        parent.on('click','a[name="history_more"]', function(e) {
			var parent = thisInstance.getContainer();
            var element = jQuery(e.currentTarget);
            var type = parent.find("[name='type']").val();
			var url = element.data('url');
			var params = url+'&content=true&type='+type;
            app.request.post({"url":params}).then(function(err,data) {
                element.parent().remove();
				var widgetContent = jQuery('.dashboardWidgetContent', parent);
				var dashboardWidgetData = parent.find('.dashboardWidgetContent .dashboardWidgetData');
				var scrollTop = dashboardWidgetData.height() * dashboardWidgetData.length - 100;
				widgetContent.mCustomScrollbar('destroy');
                parent.find('.dashboardWidgetContent').append(data);
				
				var adjustedHeight = parent.height()-100;
				app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight, 'setTop' : scrollTop+'px'});
				
            });
		});
	}

});

Vtiger_OverdueActivities_Widget_Js('Vtiger_CalendarActivities_Widget_Js', {}, {});


//SalesPlatform.ru begin
Vtiger_Widget_Js("Vtiger_Chartreportwidget_Widget_Js",{},{
    
    widgetInstance : false,
    
    postLoadWidget: function() {
        var chartType = jQuery('input[name=charttype]', this.getContainer()).val();
        var chartClassName = chartType.toCamelCase();
        var chartClass = window["Report_"+chartClassName + "_Js"];
        
        /* Instantinate concrece widget builder */
        if(typeof chartClass != 'undefined') {
            this.widgetInstance = new chartClass(this.getContainer());
        }
        
        /* Display widget content */
        if(!this.isEmptyData()) {
            this.loadChart();
        } else {
            this.positionNoDataMsg();
        }
        this.widgetInstance.postInitializeCalls();
    },
    
    loadChart : function() {
        this.widgetInstance.loadChart();
    },
    
    isEmptyData : function() {
            var jsonData = jQuery('input[name=data]', this.getContainer()).val();
            var data = JSON.parse(jsonData);
            var values = data['values'];
            if(jsonData == '' || values == '' || values.length == 0) {
                    return true;
            }
            return false;
    },
        
    positionNoDataMsg : function() {
        $('.widgetChartContainer', this.getContainer()).html('<div>'+app.vtranslate('JS_NO_REPORT_WIDGET_DATA_AVAILABLE')+'</div>').css(                                                    {'text-align':'center', 'position':'relative', 'top':'100px'});
    }
});


Vtiger_Pie_Widget_Js('Report_Piechart_Js',{},{

    postInitializeCalls : function() {
        var clickThrough = jQuery('input[name=clickthrough]', this.getContainer()).val();
        if(clickThrough != '') {
            var thisInstance = this;
            this.getContainer().on("jqplotDataClick", function(evt, seriesIndex, pointIndex, neighbor) {
                var linkUrl = thisInstance.data['links'][pointIndex];
                if(linkUrl) window.location.href = linkUrl;
            });
            this.getContainer().on("jqplotDataHighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'pointer' );
            });
            this.getContainer().on("jqplotDataUnhighlight", function(evt, seriesIndex, pointIndex, neighbor) {
                $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'auto' );
            });
        }
    },

    generateData : function() {
        var jsonData = jQuery('input[name=data]', this.getContainer()).val();
        var data = this.data = JSON.parse(jsonData);
        var values = data['values'];

        var chartData = [];
        for(var i in values) {
            chartData[i] = [];
            chartData[i].push(data['labels'][i]);
            chartData[i].push(values[i]);
        }
        return {'chartData':chartData, 'labels':data['labels'], 'data_labels':data['data_labels'], 'title' : data['graph_label']};
    }
});

Vtiger_Barchat_Widget_Js('Report_Verticalbarchart_Js', {},{
    
    postInitializeCalls : function() {
        jQuery('table.jqplot-table-legend', this.getContainer()).css('width','95px');
        var thisInstance = this;

        this.getContainer().on('jqplotDataClick', function(ev, gridpos, datapos, neighbor, plot) {
            var linkUrl = thisInstance.data['links'][neighbor[0]-1];
            if(linkUrl) window.location.href = linkUrl;
        });

        this.getContainer().on("jqplotDataHighlight", function(evt, seriesIndex, pointIndex, neighbor) {
            $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'pointer' );
        });
        this.getContainer().on("jqplotDataUnhighlight", function(evt, seriesIndex, pointIndex, neighbor) {
            $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'auto' );
        });
    },

    generateChartData : function() {
        var jsonData = jQuery('input[name=data]', this.getContainer()).val();
        var data = this.data = JSON.parse(jsonData);
        var values = data['values'];

        var chartData = [];
        var yMaxValue = 0;

        if(data['type'] == 'singleBar') {
            chartData[0] = [];
            for(var i in values) {
                var multiValue = values[i];
                for(var j in multiValue) {
                        chartData[0].push(multiValue[j]);
                        if(multiValue[j] > yMaxValue) yMaxValue = multiValue[j];
                }
            }
        } else {
            for(var i in values) {
                var multiValue = values[i];
                var info = [];
                for(var j in multiValue) {
                    if(!$.isArray(chartData[j])) {
                        chartData[j] = [];
                    }
                    chartData[j].push(multiValue[j]);
                    if(multiValue[j] > yMaxValue) yMaxValue = multiValue[j];
                }
            }
        }
        yMaxValue = yMaxValue + (yMaxValue*0.15);

        return {'chartData':chartData,
                'yMaxValue':yMaxValue,
                'labels':data['labels'],
                'data_labels':data['data_labels'],
                'title' : data['graph_label']
        };
    }
});


Report_Verticalbarchart_Js('Report_Horizontalbarchart_Js', {},{

    generateChartData : function() {
        var jsonData = jQuery('input[name=data]', this.getContainer()).val();
        var data = this.data = JSON.parse(jsonData);
        var values = data['values'];

        var chartData = [];
        var yMaxValue = 0;

        if(data['type'] == 'singleBar') {
            for(var i in values) {
                var multiValue = values[i];
                chartData[i] = [];
                for(var j in multiValue) {
                    chartData[i].push(multiValue[j]);
                    chartData[i].push(parseInt(i)+1);
                    if(multiValue[j] > yMaxValue){
                        yMaxValue = multiValue[j];
                    }
                }
            }
            chartData = [chartData];
        } else {
            chartData = [];
            for(var i in values) {
                var multiValue = values[i];
                for(var j in multiValue) {
                    if(!$.isArray(chartData[j])) {
                        chartData[j] = [];
                    }
                    
                    chartData[j][i] = [];
                    chartData[j][i].push(multiValue[j]);
                    chartData[j][i].push(parseInt(i)+1);
                    if(multiValue[j] > yMaxValue){
                        yMaxValue = multiValue[j];
                    }
                }
            }
        }
        yMaxValue = yMaxValue + (yMaxValue*0.15);

        return {
            'chartData':chartData,
            'yMaxValue':yMaxValue,
            'labels':data['labels'],
            'data_labels':data['data_labels'],
            'title' : data['graph_label']
        };
    },

    loadChart : function() {
        var data = this.generateChartData();
        var labels = data['labels'];
        
        this.getPlotContainer(false).jqplot(data['chartData'], {
            title: data['title'],
            animate: !$.jqplot.use_excanvas,
            seriesDefaults: {
                renderer:$.jqplot.BarRenderer,
                showDataLabels: true,
                pointLabels: { 
                    show: true, 
                    location: 'e', 
                    edgeTolerance: -15 
                },
                shadowAngle: 135,
                rendererOptions: {
                    barDirection: 'horizontal'
                }
            },
            axes: {
                yaxis: {
                    tickRenderer: jQuery.jqplot.CanvasAxisTickRenderer,
                    renderer: jQuery.jqplot.CategoryAxisRenderer,
                    ticks: labels,
                    tickOptions: {
                      angle: -45
                    }
                }
            },
            legend: {
                show: true,
                location: 'e',
                placement: 'outside',
                showSwatch : true,
                showLabels : true,
                labels:data['data_labels']
            }
        });
        jQuery('table.jqplot-table-legend', this.getContainer()).css('width','95px');
    },

    postInitializeCalls : function() {
        var thisInstance = this;
        this.getContainer().on("jqplotDataClick", function(ev, gridpos, datapos, neighbor, plot) {
            var linkUrl = thisInstance.data['links'][neighbor[1]-1];
            if(linkUrl) window.location.href = linkUrl;
        });
        this.getContainer().on("jqplotDataHighlight", function(evt, seriesIndex, pointIndex, neighbor) {
            $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'pointer' );
        });
        this.getContainer().on("jqplotDataUnhighlight", function(evt, seriesIndex, pointIndex, neighbor) {
            $('.jqplot-event-canvas', thisInstance.getContainer()).css( 'cursor', 'auto' );
        });
    }
});


Report_Verticalbarchart_Js('Report_Linechart_Js', {},{
        
    generateData : function() {
        var jsonData = jQuery('input[name=data]', this.getContainer()).val();
        var data = this.data = JSON.parse(jsonData);
        var values = data['values'];

        
        var yMaxValue = 0;
        var chartData = [];
        var currentValue = 0;
        for(var i in values) {
            var multiValue = values[i];
            for(var j in multiValue) {
                if(!$.isArray(chartData[j])) {
                    chartData[j] = [];
                }

                currentValue = parseFloat(multiValue[j]);
                chartData[j].push(currentValue);
                if(currentValue > yMaxValue) {
                    yMaxValue = currentValue;
                }
            }
        }
            
        yMaxValue = yMaxValue + yMaxValue * 0.15;

        return {
            'chartData':chartData,
            'yMaxValue':yMaxValue,
            'labels':data['labels'],
            'data_labels':data['data_labels'],
            'title' : data['graph_label']
        };
    },

    loadChart : function() {
        var data = this.generateData();
        this.getPlotContainer(false).jqplot(data['chartData'], {
            title: data['title'],
            legend:{
                show:true,
                labels:data['data_labels'],
                location:'ne',
                showSwatch : true,
                showLabels : true,
                placement: 'outside'
            },
            seriesDefaults: {
                pointLabels: {
                    show: true
                }
            },
            axes: {
                xaxis: {
                    min:0,
                    pad: 1,
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks:data['labels'],
                    tickOptions: {
                        formatString: '%b %#d'
                    }
                }
            },
            cursor: {
                show: true
            }
        });
        jQuery('table.jqplot-table-legend', this.getContainer()).css('width','95px');
    }
});
//SalesPlatform.ru end