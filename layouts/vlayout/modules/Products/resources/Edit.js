/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Products_Edit_Js",{
	
},{
	baseCurrency : '',
	
	baseCurrencyName : '',
	//Container which stores the multi currency element
	multiCurrencyContainer : false,
	
	//Container which stores unit price
	unitPrice : false,
	
	/**
	 * Function to get unit price
	 */
	getUnitPrice : function(){
		if(this.unitPrice == false) {
			this.unitPrice = jQuery('input.unitPrice',this.getForm());
		}
		return this.unitPrice;
	},
	
	/**
	 * Function to get more currencies container
	 */
	getMoreCurrenciesContainer : function(){
		if(this.multiCurrencyContainer == false) {
			this.multiCurrencyContainer = jQuery('.multiCurrencyEditUI');
		}
		return this.multiCurrencyContainer;
	},
	
	/**
	 * Function which aligns data just below global search element
	 */
	alignBelowUnitPrice : function(dataToAlign) {
		var parentElem = jQuery('input[name="unit_price"]',this.getForm());
		dataToAlign.position({
			'of' : parentElem,
			'my': "left top",
			'at': "left bottom",
			'collision' : 'flip'
		});
		return this;
	},
	
	/**
	 * Function to get current Element
	 */
	getCurrentElem : function(e){
		return jQuery(e.currentTarget);
	},
	/**
	 *Function to register events for taxes
	 */
	registerEventForTaxes : function(){
		var thisInstance = this;
		var formElem = this.getForm();
		jQuery('.taxes').on('change',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var taxBox  = elem.data('taxName');
			if(elem.is(':checked')) {
				jQuery('input[name='+taxBox+']',formElem).removeClass('hide').show();
			}else{
				jQuery('input[name='+taxBox+']',formElem).addClass('hide');
			}

		});
		return this;
	},
	
	/**
	 * Function to register event for enabling base currency on radio button clicked
	 */
	registerEventForEnableBaseCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.baseCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentElem = elem.closest('tr');
			if(elem.is(':checked')) {
				var convertedPrice = jQuery('.convertedPrice',parentElem).val();
				thisInstance.baseCurrencyName = parentElem.data('currencyId');
				thisInstance.baseCurrency = convertedPrice;
			}
		});
		return this;
	},
	
	/**
	 * Function to register event for reseting the currencies
	 */
	registerEventForResetCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('click','.currencyReset',function(e){
			var parentElem = thisInstance.getCurrentElem(e).closest('tr');
			var unitPriceFieldData = thisInstance.getUnitPrice().data();
			var unitPrice = thisInstance.getDataBaseFormatUnitPrice();
			var conversionRate = jQuery('.conversionRate',parentElem).val();
			var price = parseFloat(unitPrice) * parseFloat(conversionRate);
			var userPreferredDecimalPlaces = unitPriceFieldData.numberOfDecimalPlaces;
			price = price.toFixed(userPreferredDecimalPlaces);
			var calculatedPrice = price.toString().replace('.',unitPriceFieldData.decimalSeparator);
			jQuery('.convertedPrice',parentElem).val(calculatedPrice);
		});
		return this;
	},
	
	/**
	 *  Function to return stripped unit price
	 */
		getDataBaseFormatUnitPrice : function(){
			var field = this.getUnitPrice();
			var unitPrice = field.val();
			if(unitPrice == ''){
				unitPrice = 0;
			}else{
				var fieldData = field.data();
				//As replace is doing replace of single occurence and using regex 
				//replace has a problem with meta characters  like (.,$),so using split and join
				var strippedValue = unitPrice.split(fieldData.groupSeparator);
				strippedValue = strippedValue.join("");
				strippedValue = strippedValue.replace(fieldData.decimalSeparator, '.');
				unitPrice = strippedValue;
			}
			return unitPrice;
		},
        
    calculateConversionRate : function() {
        var container = this.getMoreCurrenciesContainer();
        var baseCurrencyRow = container.find('.baseCurrency').filter(':checked').closest('tr');
        var baseCurrencyConvestationRate = baseCurrencyRow.find('.conversionRate');
        //if basecurrency has conversation rate as 1 then you dont have caliculate conversation rate
        if(baseCurrencyConvestationRate.val() == "1") {
            return;
        }
        var baseCurrencyRatePrevValue = baseCurrencyConvestationRate.val();
        
        container.find('.conversionRate').each(function(key,domElement) {
            var element = jQuery(domElement);
            if(!element.is(baseCurrencyConvestationRate)){
                var prevValue = element.val();
                element.val((prevValue/baseCurrencyRatePrevValue));
            }
        });
        baseCurrencyConvestationRate.val("1");
    },
	/**
	 * Function to register event for enabling currency on checkbox checked
	 */
	
	registerEventForEnableCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.enableCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentRow = elem.closest('tr');
			
			if(elem.is(':checked')) {
				elem.attr('checked',"checked");
				var conversionRate = jQuery('.conversionRate',parentRow).val();
				var unitPriceFieldData = thisInstance.getUnitPrice().data();
				var unitPrice = thisInstance.getDataBaseFormatUnitPrice();
				var price = parseFloat(unitPrice)*parseFloat(conversionRate);
				jQuery('input',parentRow).attr('disabled', true).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', true).removeAttr('disabled');
				var userPreferredDecimalPlaces = unitPriceFieldData.numberOfDecimalPlaces;
				price = price.toFixed(userPreferredDecimalPlaces);
				var calculatedPrice = price.toString().replace('.',unitPriceFieldData.decimalSeparator);
				jQuery('input.convertedPrice',parentRow).val(calculatedPrice)
			}else{
				var baseCurrency = jQuery('.baseCurrency', parentRow);
				if (baseCurrency.is(':checked')) {
					var currencyName = jQuery('.currencyName', parentRow).text();
					var params = {
									'type' : 'error',
									'title': app.vtranslate('JS_ERROR'),
									'text' : app.vtranslate('JS_BASE_CURRENCY_CHANGED_TO_DISABLE_CURRENCY') + '"' + currencyName + '"'
								};
					Vtiger_Helper_Js.showPnotify(params);
					elem.prop('checked', true);
					return;
				}
				jQuery('input',parentRow).attr('disabled', true);
				jQuery('input.enableCurrency',parentRow).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', 'disabled');
			}
		})
		return this;
	},
	
	/**
	 * Function to get more currencies UI
	 */
	getMoreCurrenciesUI : function(){
		var aDeferred = jQuery.Deferred();
		var moduleName = app.getModuleName();
		var baseCurrency = jQuery('input[name="base_currency"]').val();
		var recordId = jQuery('input[name="record"]').val();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesUi = moreCurrenciesContainer.find('.multiCurrencyEditUI');
		var moreCurrenciesUi;
			
		if(moreCurrenciesUi.length == 0){
			var moreCurrenciesParams = {
				'module' : moduleName,
				'view' : "MoreCurrenciesList",
				'currency' : baseCurrency,
				'record' : recordId
			}

			AppConnector.request(moreCurrenciesParams).then(
				function(data){
					moreCurrenciesContainer.html(data);
					aDeferred.resolve(data);
				},
				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		} else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},
	
	/*
	 * function to register events for more currencies link
	 */
	registerEventForMoreCurrencies : function(){
		var thisInstance = this;
		var form = this.getForm();
		jQuery('#moreCurrencies').on('click',function(e){
			var progressInstance = jQuery.progressIndicator();
			thisInstance.getMoreCurrenciesUI().then(function(data){
				var moreCurrenciesUi;
				moreCurrenciesUi = jQuery('#moreCurrenciesContainer').find('.multiCurrencyEditUI');
				if(moreCurrenciesUi.length > 0){
					moreCurrenciesUi = moreCurrenciesUi.clone(true,true);
					progressInstance.hide();
					var css = {'text-align' : 'left','width':'65%'};
					var callback = function(data){
						var params = app.validationEngineOptions;
						var form = data.find('#currencyContainer');
						params.onValidationComplete = function(form, valid){
							if(valid) {
								thisInstance.saveCurrencies();
							}
							return false;
						}
						form.validationEngine(params);
						app.showScrollBar(data.find('.currencyContent'), {'height':'400px'});
						thisInstance.baseCurrency = thisInstance.getUnitPrice().val();
						var multiCurrencyEditUI = jQuery('.multiCurrencyEditUI');
						thisInstance.multiCurrencyContainer = multiCurrencyEditUI;
                        thisInstance.calculateConversionRate();
						thisInstance.registerEventForEnableCurrency();
						thisInstance.registerEventForEnableBaseCurrency();
						thisInstance.registerEventForResetCurrency();
						thisInstance.triggerForBaseCurrencyCalc();
					}
                    var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer').find('.multiCurrencyEditUI');
					var contentInsideForm = moreCurrenciesUi.find('.multiCurrencyContainer').html();
					moreCurrenciesUi.find('.multiCurrencyContainer').remove();
					var form = '<form id="currencyContainer"></form>'
					jQuery(form).insertAfter(moreCurrenciesUi.find('.modal-header'));
					moreCurrenciesUi.find('form').html(contentInsideForm);
                    moreCurrenciesContainer.find('input[name^=curname]').each(function(index,element){
                    	var dataValue = jQuery(element).val();
                        var dataId = jQuery(element).attr('id');
                        moreCurrenciesUi.find('#'+dataId).val(dataValue);
                    });

					var modalWindowParams = {
						data : moreCurrenciesUi,
						css : css,
						cb : callback
					}
					app.showModalWindow(modalWindowParams)
				}
			})
		});
	},
	/**
	 * Function to calculate base currency price value if unit
	 * present on click of more currencies
	 */
	triggerForBaseCurrencyCalc : function(){
		var multiCurrencyEditUI = this.getMoreCurrenciesContainer();
		var baseCurrency = multiCurrencyEditUI.find('.enableCurrency');
		jQuery.each(baseCurrency,function(key,val){
			if(jQuery(val).is(':checked')){
				var baseCurrencyRow = jQuery(val).closest('tr');
                if(parseFloat(baseCurrencyRow.find('.convertedPrice').val()) == 0) {
                	baseCurrencyRow.find('.currencyReset').trigger('click');
                }
			} else {
				var baseCurrencyRow = jQuery(val).closest('tr');
                baseCurrencyRow.find('.convertedPrice').val('');
            }
		})
	},
	
	/**
	 * Function to register onchange event for unit price
	 */
	registerEventForUnitPrice : function(){
		var thisInstance = this;
		var unitPrice = this.getUnitPrice();
		unitPrice.on('change',function(){
			thisInstance.triggerForBaseCurrencyCalc();
		})
	},

	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var multiCurrencyContent = jQuery('#moreCurrenciesContainer').find('.currencyContent');
			var unitPrice = thisInstance.getUnitPrice();
			if((multiCurrencyContent.length < 1) && (unitPrice.length > 0)){
				e.preventDefault();
				thisInstance.getMoreCurrenciesUI().then(function(data){
					thisInstance.preSaveConfigOfForm(form);
					InitialFormData = form.serialize();
                    //SalesPlatform.ru begin 
                    form.removeData('submit');
                    form.data('isNeedCheckBeforeSave', false);
                    //SalesPlatform.ru end
					form.submit();
				})
			}else if(multiCurrencyContent.length > 0){
				thisInstance.preSaveConfigOfForm(form);
			}
		})
	},
	
	/**
	 * Function to handle settings before save of record
	 */
	preSaveConfigOfForm : function(form) {
		var unitPrice = this.getUnitPrice();
		if(unitPrice.length > 0){
			var unitPriceValue = unitPrice.val();
			var baseCurrencyName = form.find('[name="base_currency"]').val();
			form.find('[name="'+ baseCurrencyName +'"]').val(unitPriceValue);
			form.find('#requstedUnitPrice').attr('name',baseCurrencyName).val(unitPriceValue);
		}
	},
	
	saveCurrencies : function(){
		var thisInstance = this;
		var errorMessage,params;
		var form = jQuery('#currencyContainer');
		var editViewForm = thisInstance.getForm();
		var modalContainer = jQuery('#globalmodal');
		var enabledBaseCurrency = modalContainer.find('.enableCurrency').filter(':checked');
		if(enabledBaseCurrency.length < 1){
			errorMessage = app.vtranslate('JS_PLEASE_SELECT_BASE_CURRENCY_FOR_PRODUCT');
			params = {
				text: errorMessage,
				'type':'error'
			};
			Vtiger_Helper_Js.showMessage(params);
			form.removeData('submit');
			return;
		}
		enabledBaseCurrency.attr('checked',"checked");
		modalContainer.find('.enableCurrency').filter(":not(:checked)").removeAttr('checked');
		var selectedBaseCurrency = modalContainer.find('.baseCurrency').filter(':checked');
		if(selectedBaseCurrency.length < 1){
			errorMessage = app.vtranslate('JS_PLEASE_ENABLE_BASE_CURRENCY_FOR_PRODUCT');
			params = {
				text: errorMessage,
				'type':'error'
			};
			Vtiger_Helper_Js.showMessage(params);
			form.removeData('submit');
			return;
		}
		selectedBaseCurrency.attr('checked',"checked");
		modalContainer.find('.baseCurrency').filter(":not(:checked)").removeAttr('checked');
		var parentElem = selectedBaseCurrency.closest('tr');
		var convertedPrice = jQuery('.convertedPrice',parentElem).val();
		thisInstance.baseCurrencyName = parentElem.data('currencyId');
		thisInstance.baseCurrency = convertedPrice;
		
		thisInstance.getUnitPrice().val(thisInstance.baseCurrency);
		jQuery('input[name="base_currency"]',editViewForm).val(thisInstance.baseCurrencyName);
		
		var savedValuesOfMultiCurrency = modalContainer.find('.currencyContent').html();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesContainer.find('.currencyContent').html(savedValuesOfMultiCurrency);
        modalContainer.find('input[name^=curname]').each(function(index,element){
        	var dataValue = jQuery(element).val();
            var dataId = jQuery(element).attr('id');
            moreCurrenciesContainer.find('.currencyContent').find('#'+dataId).val(dataValue);
        });
		app.hideModalWindow();
	},
	
	registerSubmitEvent: function() {
		var editViewForm = this.getForm();
        
        //SalesPlatform.ru begin
        var thisInstance = this;
        //SalesPlatform.ru end
        
		editViewForm.submit(function(e){
            
            //SalesPlatform begin
            var mode = jQuery(e.currentTarget).find('[name="mode"]').val();
            //SalesPlatform.ru end
            
			if((editViewForm.find('[name="existingImages"]').length >= 1) || (editViewForm.find('[name="imagename[]"]').length > 1)){
				jQuery.fn.MultiFile.disableEmpty(); // before submiting the form - See more at: http://www.fyneworks.com/jquery/multiple-file-upload/#sthash.UTGHmNv3.dpuf
			}
			//Form should submit only once for multiple clicks also
			if(typeof editViewForm.data('submit') != "undefined") {
				return false;
			} else {
				var module = jQuery(e.currentTarget).find('[name="module"]').val();
                
                //SalesPlatform.ru begin
                editViewForm.removeClass('validating');
                //SalesPlatform.ru end
				if(editViewForm.validationEngine('validate')) {
                    //SalesPlatform.ru begin
                    thisInstance.updateCKFieldsContents();
                    //SalesPlatform.ru end
					//Once the form is submiting add data attribute to that form element
					editViewForm.data('submit', 'true');
                    
                    //SalesPlatform begin
                    e.preventDefault();
                    sp_js_editview_checkBeforeSave(module, editViewForm, mode).then(function () {
                        //on submit form trigger the recordPreSave event
                        var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
                        editViewForm.trigger(recordPreSaveEvent, {'value': 'edit'});
                        if (recordPreSaveEvent.isDefaultPrevented()) {
                            //If duplicate record validation fails, form should submit again
                            editViewForm.removeData('submit');
                            e.preventDefault();
                            return false;
                        }
                        e.preventDefault();
                        var progressIndicator = $.progressIndicator({message: app.vtranslate('JS_SAVE')});
                        editViewForm.ajaxSubmit({
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    location.href = response.result.location;
                                } else {
                                    progressIndicator.hide();
                                    editViewForm.removeData('submit');
                                    Vtiger_Helper_Js.showPnotify({
                                        type: 'error',
                                        title: app.vtranslate('JS_SAVE_ERROR'),
                                        text: response.error.message,
                                        delay: 20000
                                    });
                                }
                            },
                            error: function () {
                                editViewForm.removeData('submit');
                                progressIndicator.hide();
                                Vtiger_Helper_Js.showPnotify({
                                    type: 'error',
                                    text: app.vtranslate('JS_ERROR_SEND_REQUEST'),
                                    delay: 10000
                                });
                            }
                        });
                    },
                    function (error) {
                        editViewForm.removeData('submit');
                        return false;
                    });
                    
                    /*if(!sp_js_editview_checkBeforeSave(module, editViewForm, mode)) {
                        return false;
                    }
                                        
						//on submit form trigger the recordPreSave event
						var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
						editViewForm.trigger(recordPreSaveEvent, {'value' : 'edit'});
						if(recordPreSaveEvent.isDefaultPrevented()) {
							//If duplicate record validation fails, form should submit again
							editViewForm.removeData('submit');
							e.preventDefault();
                        }
                        
                    */
                    //SalesPlatform.ru end
				} else {
					//If validation fails, form should submit again
					editViewForm.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(editViewForm);
				}
			}
		});
	},
	
	registerEvents : function(){
		this._super();
		this.registerEventForMoreCurrencies();
		this.registerEventForTaxes();
		this.registerEventForUnitPrice();
		this.registerRecordPreSaveEvent();
	}
    
});

// SalesPlatform.ru begin
var gValidationCall='';

if (document.all)

	var browser_ie=true

else if (document.layers)

	var browser_nn4=true

else if (document.layers || (!document.all && document.getElementById))

	var browser_nn6=true

var gBrowserAgent = navigator.userAgent.toLowerCase();

function getObj(n,d) {

	var p,i,x;

	if(!d) {
		d=document;
	}

	if(n != undefined) {
		if((p=n.indexOf("?"))>0&&parent.frames.length) {
			d=parent.frames[n.substring(p+1)].document;
			n=n.substring(0,p);
		}
	}

	if(d.getElementById) {
		x=d.getElementById(n);
		// IE7 was returning form element with name = n (if there was multiple instance)
		// But not firefox, so we are making a double check
		if(x && x.id != n) x = false;
	}

	for(i=0;!x && i<d.forms.length;i++) {
		x=d.forms[i][n];
	}

	for(i=0; !x && d.layers && i<d.layers.length;i++) {
		x=getObj(n,d.layers[i].document);
	}

	if(!x && !(x=d[n]) && d.all) {
		x=d.all[n];
	}

	if(typeof x == 'string') {
		x=null;
	}

	return x;
}

/** Javascript dialog box utility functions **/
VtigerJS_DialogBox = {
	_olayer : function(toggle) {
		var olayerid = "__vtigerjs_dialogbox_olayer__";
		VtigerJS_DialogBox._removebyid(olayerid);

		if(typeof(toggle) == 'undefined' || !toggle) return;

		var olayer = document.getElementById(olayerid);
		if(!olayer) {
			olayer = document.createElement("div");
			olayer.id = olayerid;
			olayer.className = "small veil";
			olayer.style.zIndex = (new Date()).getTime();

			// Avoid zIndex going beyond integer max
			// http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7146#comment:1
			olayer.style.zIndex = parseInt((new Date()).getTime() / 1000);

			// In case zIndex goes to negative side!
			if(olayer.style.zIndex < 0) olayer.style.zIndex *= -1;
			if (browser_ie) {
				olayer.style.height = document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight) + "px";
			} else if (browser_nn4 || browser_nn6) {
				olayer.style.height = document.body.offsetHeight + "px";
			}
			olayer.style.width = "100%";
			document.body.appendChild(olayer);

			var closeimg = document.createElement("img");
			closeimg.src = 'test/logo/popuplay_close.png';
			closeimg.alt = 'X';
			closeimg.style.right= '10px';
			closeimg.style.top  = '5px';
			closeimg.style.position = 'absolute';
			closeimg.style.cursor = 'pointer';
			closeimg.onclick = VtigerJS_DialogBox.unblock;
			olayer.appendChild(closeimg);
		}
		if(olayer) {
			if(toggle) olayer.style.display = "block";
			else olayer.style.display = "none";
		}
		return olayer;
	},
	_removebyid : function(id) {
		if($(id)) $(id).remove();
	},
	unblock : function() {
		VtigerJS_DialogBox._olayer(false);
	},
	block : function(opacity) {
		if(typeof(opactiy)=='undefined') opacity = '0.3';
		var olayernode = VtigerJS_DialogBox._olayer(true);
		olayernode.style.opacity = opacity;
	},
	hideprogress : function() {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_progress_id__');
	},
	progress : function(imgurl) {
		VtigerJS_DialogBox._olayer(true);
		if(typeof(imgurl) == 'undefined') imgurl = 'themes/images/plsWaitAnimated.gif';

		var prgbxid = "__vtigerjs_dialogbox_progress_id__";
		var prgnode = document.getElementById(prgbxid);
		if(!prgnode) {
			prgnode = document.createElement("div");
			prgnode.id = prgbxid;
			prgnode.className = 'veil_new';
			prgnode.style.position = 'absolute';
			prgnode.style.width = '100%';
			prgnode.style.top = '0';
			prgnode.style.left = '0';
			prgnode.style.display = 'block';

			document.body.appendChild(prgnode);

			prgnode.innerHTML =
			'<table border="5" cellpadding="0" cellspacing="0" align="center" style="vertical-align:middle;width:100%;height:100%;">' +
			'<tr><td class="big" align="center"><img src="'+ imgurl + '"></td></tr></table>';

		}
		if(prgnode) prgnode.style.display = 'block';
	},
	hideconfirm : function() {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_alert_boxid__');
	},
	confirm : function(msg, onyescode) {
		VtigerJS_DialogBox._olayer(true);

		var dlgbxid = "__vtigerjs_dialogbox_alert_boxid__";
		var dlgbxnode = document.getElementById(dlgbxid);
		if(!dlgbxnode) {
			dlgbxnode = document.createElement("div");
			dlgbxnode.style.display = 'none';
			dlgbxnode.className = 'veil_new small';
			dlgbxnode.id = dlgbxid;
			dlgbxnode.innerHTML =
			'<table cellspacing="0" cellpadding="18" border="0" class="options small">' +
			'<tbody>' +
			'<tr>' +
			'<td nowrap="" align="center" style="color: rgb(255, 255, 255); font-size: 15px;">' +
			'<b>'+ msg + '</b></td>' +
			'</tr>' +
			'<tr>' +
			'<td align="center">' +
			'<input type="button" style="text-transform: capitalize;" onclick="$(\''+ dlgbxid + '\').hide();VtigerJS_DialogBox._olayer(false);VtigerJS_DialogBox._confirm_handler();" value="'+ alert_arr.YES + '"/>' +
			'<input type="button" style="text-transform: capitalize;" onclick="$(\''+ dlgbxid + '\').hide();VtigerJS_DialogBox._olayer(false)" value="' + alert_arr.NO + '"/>' +
			'</td>'+
			'</tr>' +
			'</tbody>' +
			'</table>';
			document.body.appendChild(dlgbxnode);
		}
		if(typeof(onyescode) == 'undefined') onyescode = '';
		dlgbxnode._onyescode = onyescode;
		if(dlgbxnode) dlgbxnode.style.display = 'block';
	},
	_confirm_handler : function() {
		var dlgbxid = "__vtigerjs_dialogbox_alert_boxid__";
		var dlgbxnode = document.getElementById(dlgbxid);
		if(dlgbxnode) {
			if(typeof(dlgbxnode._onyescode) != 'undefined' && dlgbxnode._onyescode != '') {
				eval(dlgbxnode._onyescode);
			}
		}
	}
};

//Search element in array
function in_array(what, where) {
    for(var i=0; i < where.length; i++) {
        if(what == where[i]) { 
            return true;
        }
    }    
    return false;
}

//Empty check
function empty (mixed_var) {

  var undef, key, i, len;
  var emptyValues = [undef, null, false, 0, "", "0"];

  for (i = 0, len = emptyValues.length; i < len; i++) {
    if (mixed_var === emptyValues[i]) {
      return true;
    }
  }

  if (typeof mixed_var === "object") {
    for (key in mixed_var) {
      // TODO: should we check for own properties only?
      //if (mixed_var.hasOwnProperty(key)) {
      return false;
      //}
    }
    return true;
  }

  return false;
}

//Json check
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

//Check before save implementation
function sp_js_editview_checkBeforeSave(module, thisForm, mode) {
    var values = thisForm.serializeFormData();
    
    var data = encodeURIComponent(JSON.stringify(values));
    var createMode;
    if(mode == 'edit') {
        createMode = 'edit';
    } else {
        createMode = 'create';
    }
    
    var urlstring = "index.php?module="+module+"&action=CheckBeforeSave&checkBeforeSaveData="+
            data+"&EditViewAjaxMode=true&CreateMode="+createMode;
    
    if (typeof values['record'] != 'undefined') {
        urlstring += "&record="+values['record'];
    }
    var params = {  
        url : urlstring,
        async : false, 
        data : {} 
    }; 
    
    var checkResult = jQuery.Deferred();
    //Disable twice check before save
    if (typeof thisForm.data('isNeedCheckBeforeSave') != 'undefined' && !thisForm.data('isNeedCheckBeforeSave')) {
        checkResult.resolve();
    } else {
        AppConnector.request(params).then(
                function (responseObj) {
                    if (!empty(responseObj)) {
                        if (responseObj.response === undefined) {
                            checkResult.resolve();
                        }
                        if (responseObj.response === "OK") {
                            if (responseObj.message !== undefined && !empty(responseObj.message)) {
                                Vtiger_Helper_Js.showAlertBox({'message': responseObj.message}).then(
                                        function (e) {
                                            checkResult.resolve();
                                        });
                            } else {
                                checkResult.resolve();
                            }
                        } else if (responseObj.response === "ALERT") {
                            var alertMessage;
                            if (responseObj.message !== undefined) {
                                alertMessage = responseObj.message;
                            } else {
                                alertMessage = 'Alert';
                            }
                            Vtiger_Helper_Js.showAlertBox({'message': alertMessage}).then(
                                    function (e) {
                                        checkResult.reject();
                                    }
                            );
                            checkResult.reject();
                        } else if (responseObj.response === "CONFIRM") {
                            var confirmMessage;
                            if (responseObj.message !== undefined) {
                                confirmMessage = responseObj.message;
                            } else {
                                confirmMessage = 'Confirm';
                            }
                            Vtiger_Helper_Js.showConfirmationBox({'message': confirmMessage}).then(
                                    function (e) {
                                        checkResult.resolve();
                                    },
                                    function (error) {
                                        checkResult.reject();
                                    }
                            );
                        } else {
                            checkResult.resolve();
                        }
                    } else {
                        checkResult.resolve();
                    }
                },
                function (error) {
                    checkResult.resolve();
                }
        );
    }
    return checkResult.promise();
}
//SalesPlatform.ru end