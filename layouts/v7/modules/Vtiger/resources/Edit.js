 /*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Index_Js("Vtiger_Edit_Js",{
    
    file : false,
    
    editInstance : false,
    
    recordPresaveEvent : "Pre.Record.Save",
    
    preReferencePopUpOpenEvent : "Vtiger.Referece.Popup.Pre",
    
    postReferencePopUpOpenEvent : "Vtiger.Referece.Popup.Post",
    
    referenceSelectionEvent : "Vtiger.Reference.Selection",
    
    postReferenceSelectionEvent: "Vtiger.PostReference.Selection",
    
    postReferenceQuickCreateSave: "Vtiger.PostReference.QuickCreateSave",
    
    refrenceMultiSelectionEvent : "Vtiger.MultiReference.Selection",
    
    postReferenceQuickCreateSaveEvent : "Vtiger.PostReferenceQuickCreate.Save",
    
    popupSelectionEvent : "Vtiger.Reference.Popup.Selection",
    
    referenceDeSelectionEvent : "Vtiger.Reference.Deselection",
    
    /**
    * Function to get Instance by name
    * @params moduleName:-- Name of the module to create instance
    */
    getInstanceByModuleName : function(moduleName){
        if(typeof moduleName == "undefined"){
            moduleName = app.getModuleName();
        }
        var parentModule = app.getParentModuleName();
        if(parentModule == 'Settings'){
            var moduleClassName = parentModule+"_"+moduleName+"_Edit_Js";
            if(typeof window[moduleClassName] == 'undefined'){
                moduleClassName = moduleName+"_Edit_Js";
            }
            var fallbackClassName = parentModule+"_Vtiger_Edit_Js";
            if(typeof window[fallbackClassName] == 'undefined') {
                fallbackClassName = "Vtiger_Edit_Js";
            }
        } else {
            moduleClassName = moduleName+"_Edit_Js";
            fallbackClassName = "Vtiger_Edit_Js";
        }
        if(typeof window[moduleClassName] != 'undefined'){
            var instance = new window[moduleClassName]();
        }else{
            var instance = new window[fallbackClassName]();
        }
        return instance;
    },
    
    getInstance: function(){
        if(Vtiger_Edit_Js.editInstance == false){
            var instance = Vtiger_Edit_Js.getInstanceByModuleName();
            Vtiger_Edit_Js.editInstance = instance;
            return instance;
        }
        return Vtiger_Edit_Js.editInstance;
    }
},{
    
    editViewContainer : false,
    formValidatorInstance : false,
	
    getEditViewContainer : function(){
        if(this.editViewContainer === false){
            this.editViewContainer = jQuery('.editViewPageDiv');
        }
        return this.editViewContainer;
    },
    setEditViewContainer: function(container){
        this.editViewContainer = container;
    },
    
    formElement : false,
    
    getForm : function() {
        if(this.formElement === false){
                this.formElement = jQuery('#EditView');
        }
        return this.formElement;
    },
    _moduleName : false,
    
    getModuleName : function() {
        if(this._moduleName != false){
            return this._moduleName;
        }
        return app.module();
    },

    setModuleName : function(module){
        this._moduleName = module;
        return this;
    },
    
    /**
	 * Function which will give you all details of the selected record
	 * @params - an Array of values like {'record' : recordId, 'source_module' : searchModule, 'selectedName' : selectedRecordName}
	 */
	getRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
		app.request.get({'url':url}).then(
			function(error, data){
				if(error == null) {
					aDeferred.resolve(data);
				} else {
					//aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
			)
		return aDeferred.promise();
	},
    
    //SalesPlatform.ru begin
 	registerSpMobilePhoneFields : function(container) { 
        $('.spMobilePhone', container).inputmask("+9{11,15}"); 
    }, 
    //SalesPlatform.ru end
    
    /**
     * Function to Validate and Save Event 
     * @returns {undefined}
     */
    //SalesPlatform.ru begin porting CheckBeforeSave
    registerCheckBeforeSaveAndValidation : function () {
        var editViewForm = this.getForm();
        var spFlagCheckBeforeSave = false;
        this.formValidatorInstance = editViewForm.vtValidate({
            submitHandler : function() {
                if (spFlagCheckBeforeSave) {
                    return true;
                }
                var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                app.event.trigger(e);
                // JS validation
                if(e.isDefaultPrevented()) {
                    return false;
                }
                var mode = jQuery(editViewForm).find('[name="mode"]').val();
                //Form should submit only once for multiple clicks also
                if(typeof editViewForm.data('submit') != "undefined") {
                    return false;
                }
                //CheckBeforeSave
                else {
                    var module = jQuery(editViewForm).find('[name="module"]').val();
                    //Once the form is submiting add data attribute to that form element
                    editViewForm.data('submit', 'true');
                    e.preventDefault();
                    sp_js_editview_checkBeforeSave(module, editViewForm, mode).then(function () {
                        spFlagCheckBeforeSave = true;
                        
                        //on submit form trigger the recordPreSave event
                        var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                        editViewForm.trigger(recordPreSaveEvent, {'value': 'edit'});

                        if (recordPreSaveEvent.isDefaultPrevented()) {
                            //If duplicate record validation fails, form should submit again
                            editViewForm.removeData('submit');
                            e.preventDefault();
                            return false;
                        }
                        var progressIndicator = $.progressIndicator({message: app.vtranslate('JS_SAVE')});
                        
                        editViewForm.submit();
                    },
                    function (error) {
                        editViewForm.removeData('submit');
                        return false;
                    });
                    }            
            }
        });
    },
    //registerValidation : function () {
    //SalesPlatform.ru end porting CheckBeforeSave

    /**
    * Function which will register event to prevent form submission on pressing on enter
    * @params - container <jQuery> - element in which auto complete fields needs to be searched
    */
    registerPreventingEnterSubmitEvent : function(container) {
        container.on('keypress', function(e){
            //Stop the submit when enter is pressed in the form
            var currentElement = jQuery(e.target);
            if(e.which == 13 && (!currentElement.is('textarea'))) {
                    e. preventDefault();
            }
        });
    },
	
	/**
	 * Function to register event for setting up picklistdependency
	 * for a module if exist on change of picklist value
	 */
	registerEventForPicklistDependencySetup : function(container){
		var picklistDependcyElemnt = jQuery('[name="picklistDependency"]',container);
		if(picklistDependcyElemnt.length <= 0) {
			return;
		}
		var picklistDependencyMapping = JSON.parse(picklistDependcyElemnt.val());
		
		var sourcePicklists = Object.keys(picklistDependencyMapping);
		if(sourcePicklists.length <= 0){
			return;
		}
		
		var sourcePickListNames = "";
		for(var i=0;i<sourcePicklists.length;i++){
			if(i != sourcePicklists.length-1)
				sourcePickListNames += '[name="'+sourcePicklists[i]+'"],';
			else
				sourcePickListNames += '[name="'+sourcePicklists[i]+'"]';
		}
		var sourcePickListElements = container.find(sourcePickListNames);

		sourcePickListElements.on('change',function(e){
			var currentElement = jQuery(e.currentTarget);
			var sourcePicklistname = currentElement.attr('name');
			var configuredDependencyObject = picklistDependencyMapping[sourcePicklistname];
			var selectedValue = currentElement.val();
			var targetObjectForSelectedSourceValue = configuredDependencyObject[selectedValue];
			var picklistmap = configuredDependencyObject["__DEFAULT__"];
			
			if(typeof targetObjectForSelectedSourceValue == 'undefined'){
				targetObjectForSelectedSourceValue = picklistmap;
			}
			
			jQuery.each(picklistmap,function(targetPickListName,targetPickListValues){
				var targetPickListMap = targetObjectForSelectedSourceValue[targetPickListName];
				if(typeof targetPickListMap == "undefined"){
					targetPickListMap = targetPickListValues;
				}
				var targetPickList = jQuery('[name="'+targetPickListName+'"]',container);
				if(targetPickList.length <= 0){
					return;
				}
				
				var listOfAvailableOptions = targetPickList.data('availableOptions');
				if(typeof listOfAvailableOptions == "undefined"){
					listOfAvailableOptions = jQuery('option',targetPickList);
					targetPickList.data('available-options', listOfAvailableOptions);
				}
				
				var targetOptions = new jQuery();
				var optionSelector = [];
				optionSelector.push('');
				for(var i=0; i<targetPickListMap.length; i++){
					optionSelector.push(targetPickListMap[i]);
				}
				jQuery.each(listOfAvailableOptions, function(i,e) {
					var picklistValue = jQuery(e).val();
					if(jQuery.inArray(picklistValue, optionSelector) != -1) {
						targetOptions = targetOptions.add(jQuery(e));
					}
				})
				var targetPickListSelectedValue = '';
				var targetPickListSelectedValue = targetOptions.filter('[selected]').val();
                if(targetPickListMap.length == 1) { 
                    var targetPickListSelectedValue = targetPickListMap[0]; // to automatically select picklist if only one picklistmap is present.
                }
				if((targetPickListName == 'group_id' || targetPickListName == 'assigned_user_id') && jQuery("[name="+ sourcePicklistname +"]").val() == ''){
					return false;
				}
				targetPickList.html(targetOptions).val(targetPickListSelectedValue).trigger("change");
				
			})
		});

		//To Trigger the change on load
		sourcePickListElements.trigger('change');
	},
        
    registerImageChangeEvent : function() {
        var formElement = this.getForm();
        formElement.find('input[name="imagename[]"]').on('change',function() {
            var deleteImageElement = jQuery(this).closest('td.fieldValue').find('.imageDelete');
            if(deleteImageElement.length) deleteImageElement.trigger('click');
        });
    },
    
	/**
	 * Function to register event for image delete
	 */
	registerEventForImageDelete : function(){
		var formElement = this.getForm();
		formElement.find('.imageDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var imageId = element.closest('div').find('img').data().imageId;
			var parentTd = element.closest('td');
			var imageUploadElement = parentTd.find('[name="imagename[]"]');
			element.closest('div').remove();
            
			if(formElement.find('[name=imageid]').length !== 0) {
				var imageIdValue = JSON.parse(formElement.find('[name=imageid]').val());
				imageIdValue.push(imageId);
				formElement.find('[name=imageid]').val(JSON.stringify(imageIdValue));
			} else {
				var imageIdJson = [];
				imageIdJson.push(imageId);
				formElement.append('<input type="hidden" name="imgDeleted" value="true" />');
				formElement.append('<input type="hidden" name="imageid" value="'+JSON.stringify(imageIdJson)+'" />');
			}
			
			if(formElement.find('.imageDelete').length === 0 && imageUploadElement.attr('data-rule-required') == 'true'){
				imageUploadElement.removeClass('ignore-validation')
			}
		});
	},
        
        registerFileElementChangeEvent : function(container) {
            var thisInstance = this;
            container.on('change', 'input[name="imagename[]"],input[name="sentdocument"]', function(e){
                if(e.target.type == "text") return false;
                var moduleName = jQuery('[name="module"]').val();
                if(moduleName == "Products") return false;
                Vtiger_Edit_Js.file = e.target.files[0];
                var element = container.find('[name="imagename[]"],input[name="sentdocument"]');
                //ignore all other types than file 
                if(element.attr('type') != 'file'){
                        return ;
                }
                var uploadFileSizeHolder = element.closest('.fileUploadContainer').find('.uploadedFileSize');
                var fileSize = e.target.files[0].size;
                var fileName = e.target.files[0].name;
                var maxFileSize = thisInstance.getMaxiumFileUploadingSize(container);
                if(fileSize > maxFileSize) {
                    alert(app.vtranslate('JS_EXCEEDS_MAX_UPLOAD_SIZE'));
                    element.val('');
                    uploadFileSizeHolder.text('');
                }else{
                    if(container.length > 1){
                        jQuery('div.fieldsContainer').find('form#I_form').find('input[name="filename"]').css('width','80px');
                        jQuery('div.fieldsContainer').find('form#W_form').find('input[name="filename"]').css('width','80px');
                    } else {
                        container.find('input[name="filename"]').css('width','80px');
                    }
                    uploadFileSizeHolder.text(fileName+' '+thisInstance.convertFileSizeInToDisplayFormat(fileSize));
                }
				
				jQuery(e.currentTarget).addClass('ignore-validation');
            });
	},
    
        //SalesPlatform.ru begin initializing the field with uitype 19 with the CKEditor editor
        registerEventForCkEditor : function(container){
            var form = this.getForm();
            if(typeof container != 'undefined'){
                form = container;
            }
            var noteContentElement = form.find('.sp_cke_field');
            jQuery(noteContentElement).each(function(index, element){
                jQuery(element).removeAttr('data-validation-engine').addClass('ckEditorSource');
                var ckEditorInstance = new Vtiger_CkEditor_Js();
                ckEditorInstance.loadCkEditor(jQuery(element));
            });
	},
        //SalesPlatform.ru end initializing the field with uitype 19 with the CKEditor editor
        
    /** 
     * Function to register Basic Events
     * @returns {undefined}
     */
    registerBasicEvents : function(form){
        app.event.on('post.editView.load',function(event,container){
        });
        this.registerEventForPicklistDependencySetup(form);
        this.registerFileElementChangeEvent(form);
        this.registerAutoCompleteFields(form);
        this.registerClearReferenceSelectionEvent(form);
        this.registerReferenceCreate(form);
        this.referenceModulePopupRegisterEvent(form);
        this.registerPostReferenceEvent(this.getEditViewContainer());
    },
    proceedRegisterEvents : function(){
		if(jQuery('.recordEditView').length > 0){
			return true;
		}else{
			return false;
		}
	},
	
	registerPageLeaveEvents : function() {
		app.helper.registerLeavePageWithoutSubmit(this.getForm());
		app.helper.registerModalDismissWithoutSubmit(this.getForm());
	},
	
    registerEvents: function(callParent) {
        //donot call parent if registering Events from overlay.
        if(callParent != false){
            this._super();
        }
        var editViewContainer = this.getEditViewContainer();
        this.registerPreventingEnterSubmitEvent(editViewContainer);
        this.registerBasicEvents(this.getForm());
        this.registerEventForImageDelete();
        this.registerImageChangeEvent();
        //SalesPlatform.ru begin porting CheckBeforeSave
        this.registerCheckBeforeSaveAndValidation();
        //SalesPlatform.ru end porting CheckBeforeSave
        //SalesPlatform.ru begin initializing the field with uitype 19 with the CKEditor editor
        this.registerEventForCkEditor(editViewContainer);
        this.registerSpMobilePhoneFields(editViewContainer);
        //SalesPlatform.ru end initializing the field with uitype 19 with the CKEditor editor
        app.event.trigger('post.editView.load', editViewContainer);
		this.registerPageLeaveEvents();
    }
});

// SalesPlatform.ru begin porting CheckBeforeSave
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

function sp_js_editview_checkBeforeSave(module, thisForm, mode) {    
    var values = thisForm.serializeFormData();
    var createMode;
    if(mode == 'edit') {
        createMode = 'edit';
    } else {
        createMode = 'create';
    }
    
    var data = {
        module : module,
        action : 'CheckBeforeSave',
        checkBeforeSaveData : values,
        editViewAjaxMode : true,
        createMode : createMode,
        record : values['record']
    };
    
    var checkResult = jQuery.Deferred();
    //Disable twice check before save
    if (typeof thisForm.data('isNeedCheckBeforeSave') != 'undefined' && !thisForm.data('isNeedCheckBeforeSave')) {
        checkResult.resolve();
    } else {
        app.request.post({'data': data}).then(
                function (error, responseObj) {
                // if checkBeforeSave handler exists, var error is empty, else - its not empty
                    if (empty(error) && !empty(responseObj)) {
                        responseObj = JSON.parse(responseObj);
                        
                        if (responseObj.response === "OK") {
                            if (responseObj.message !== undefined && !empty(responseObj.message)) {
                                app.helper.showAlertBox({'message': responseObj.message}).then(
                                        function (e) {
                                            
                                        },
                                        function (error) {
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
                            app.helper.showAlertBox({'message': alertMessage}).then(
                                    function (e) {
                                            
                                    },
                                    function (error) {
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
                            app.helper.showConfirmationBox({'message': confirmMessage}).then(
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
//SalesPlatform.ru end porting CheckBeforeSave