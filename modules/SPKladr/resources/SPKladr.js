
jQuery.Class("SPKladr_Js", {}, {
    
    defaultsMappings: {
        
        Vtiger: {
            standartAddressFields: [
                {code: 'bill_code', city: 'bill_city', state: 'bill_state', region: 'bill_region', street: 'bill_street'},
                {code: 'ship_code', city: 'ship_city', state: 'ship_state', region: 'ship_region', street: 'ship_street'}
            ],
            fullAddressFields: []
        },
        
        Vendors: {
            standartAddressFields: [
                {code: 'postalcode', city: 'city', state: 'state', street: 'street'}
            ],
            fullAddressFields: []
        },
        
        Leads: {
            standartAddressFields: [
                {code: 'code', city: 'city', state: 'state', street: 'lane'}
            ],
            fullAddressFields: []
        },
        
        Contacts: {
            standartAddressFields: [
                {code: 'mailingzip', city: 'mailingcity', state: 'mailingstate', street: 'mailingstreet'},
                {code: 'otherzip', city: 'othercity', state: 'otherstate', street: 'otherstreet'}
            ],
            fullAddressFields: []
        }
        
    },
    
    kladrFieldsList: {},
    
    /**
     * Check is KLADR module enabled, and if it is - register helper callbacks.
     * To check module enable - send request to it
     * @param {$} container
     * @returns {undefined}
     */
    registerKladrIntegration: function () {
        var container = $('.editViewPageDiv');

        var currentInstance = this;
        if(app.getViewName() === 'Edit') {
            AppConnector.request({
                module: 'SPKladr',
                action: 'EnterAddress',
                mode: 'checkEnable'
            }).then(function (data) {
                if (data != null && data.success && data.result) {
                    if (currentInstance.isLocalStorageAvailible()) {
                        currentInstance.initializateModuleKladrFiledsMap();
                        currentInstance.registerAddressFieldsActions(container);
                    } else {
                        alert(app.vtranslate('JS_LBL_LOCAL_STORAGE_FAIL'));
                    }
                }
            });
        }
    },
    
    initializateModuleKladrFiledsMap : function () {
        var moduleName = app.getModuleName();
        if(this.defaultsMappings.hasOwnProperty(moduleName)) {
            this.kladrFieldsList = this.defaultsMappings[moduleName];
        } else {
            this.kladrFieldsList = this.defaultsMappings["Vtiger"];
        }
    },
    
    /**
     * Check support of current browser of local storage. If it not exists - kladr
     * will not to work.
     * 
     * @returns {window|Boolean|String}
     */
    isLocalStorageAvailible: function () {
        try {
            return 'localStorage' in window && window['localStorage'] !== null;
        } catch (e) {
            return false;
        }
    },
    
    /**
     * Register all needed callbacks for KLADR integration
     * @param {$} editForm
     * @returns {undefined}
     */
    registerAddressFieldsActions: function (editForm) {

        /* Set of callbacks for standart address fields in modules */
        for (var currentNumber in this.kladrFieldsList.standartAddressFields) {
            this.addAddressCallback(editForm, this.kladrFieldsList.standartAddressFields[currentNumber]);
            this.addCityCallback(editForm, this.kladrFieldsList.standartAddressFields[currentNumber]);
            this.addStateCallback(editForm, this.kladrFieldsList.standartAddressFields[currentNumber]);
        }

        /* Callbacks for fields, which contain all address in one string */
        for (var currentNumber in this.kladrFieldsList.fullAddressFields) {
            this.addFullAddressFieldCallback(editForm, this.kladrFieldsList.fullAddressFields[currentNumber]);
        }
    },
    
    /**
     * provides autocomplete in full addres field which include city strret and house number
     * 
     * @param {$} editForm
     * @param {string} fieldName
     * @returns {undefined}
     */
    addFullAddressFieldCallback: function (editForm, fieldName) {
        var parentEntity = this;
        var requestParams = {
            module: 'SPKladr',
            action: 'EnterAddress',
            mode: 'fullAddressTyped',
            cityRecordsLimit: 10,
            cityOffset: 0
        };

        editForm.find('[name="' + fieldName + '"]').autocomplete({
            delay: 300,
            minLength: 3,
            source: function (request, response) {
                var addressParts = request.term.split(",");
                switch (addressParts.length) {

                    /* Step 1 - get city */
                    case 1:
                        requestParams.requestStep = 1;
                        requestParams.cityName = request.term;
                        parentEntity.loadCities(request, response, requestParams, fieldName);
                        break;

                        /* Step 2 - get street name in selected city */
                    case 2:
                        requestParams.requestStep = 2;
                        requestParams.cityCode = localStorage.getItem(fieldName + 'cityCode');
                        requestParams.streetName = addressParts[1];

                        /* Request for second param - street or another small location */
                        AppConnector.request(requestParams).then(function (data) {
                            var selectValues = [];
                            if (data !== null && data.success) {
                                selectValues = data.result;
                            }

                            response($.map(selectValues, function (item) {
                                item.label = addressParts[0] + ', ' + item.streetSocr + ' ' + item.streetName;
                                item.value = item.label + ', ';

                                /* Prepare to save item in storage on click */
                                item.saveFieldName = fieldName + 'streetCode';
                                item.saveFieldValue = item.streetCode;

                                return item;
                            }));
                        });
                        break;

                        /* Step 3 - get hoouse number on selected street */
                    case 3:
                        requestParams.requestStep = 3;
                        requestParams.houseNumber = addressParts[2];
                        requestParams.streetCode = localStorage.getItem(fieldName + 'streetCode');

                        /* Get help info abount number of house */
                        AppConnector.request(requestParams).then(function (data) {
                            var selectValues = [];
                            if (data !== null && data.success) {
                                selectValues = data.result;
                            }

                            response($.map(selectValues, function (item) {
                                item.label = addressParts[0] + ',' + addressParts[1] + ', ' + item.houseNumber;
                                item.value = item.label;

                                return item;
                            }));
                        });
                        break;

                    default:
                        response();
                        break;
                }
            },
            select: function (event, selectedObject) {
                var clickedItem = selectedObject.item;

                /* Handling first step - load more cities pagination */
                if (clickedItem.isLoadMoreCities) {
                    requestParams.cityOffset = clickedItem.cityOffset;
                    editForm.find('[name="' + fieldName + '"]').autocomplete("search");

                    /* Restore normal pagination */
                    requestParams.cityOffset = 0;
                } else {
                    localStorage.setItem(clickedItem.saveFieldName, clickedItem.saveFieldValue);
                }
            }
        });
    },
    /**
     * Provides pagination cities loading
     * 
     * @param {Object} request
     * @param {function} response
     * @param {Object} requestParams
     * @param {string} fieldName
     * @returns {undefined}
     */
    loadCities: function (request, response, requestParams, fieldName) {

        /* Prepare special list value */
        var item = {
            isLoadMoreCities: true,
            value: request.term,
            cityOffset: requestParams.cityOffset,
            cityRecordsLimit: requestParams.cityRecordsLimit
        };

        /* Send request */
        AppConnector.request(requestParams).then(function (data) {
            var selectValues = [];
            if (data !== null && data.success) {
                selectValues = data.result.selectedCities;
            }

            /* Format search result with delimiter */
            selectValues = $.map(selectValues, function (item) {
                item.value = item.citySocr + ' ' + item.cityName + ', ';
                item.label = item.citySocr + ' ' + item.cityName + '(' +
                        item.stateSocr + ' ' + item.stateName;
                if (item.regionName !== '') {
                    item.label += ', ' + item.regionSocr + ' ' + item.regionName;
                }
                item.label += ')';

                /* Prepare to save item in storage on click */
                item.saveFieldName = fieldName + 'cityCode';
                item.saveFieldValue = item.cityCode;

                return item;
            });

            /* Insert special more load item if loaded full limit cities */
            if (selectValues.length === item.cityRecordsLimit) {

                /* Prepare pagination display */
                var startNextLoadNumber = item.cityOffset + selectValues.length;
                var endNextLoadNumber = startNextLoadNumber + item.cityRecordsLimit;
                if (endNextLoadNumber > data.result.totalCities) {
                    endNextLoadNumber = data.result.totalCities;
                }

                /* Increase offset and add pagination info */
                item.cityOffset += selectValues.length;
                item.label = "*** " +
                        app.vtranslate("JS_LBL_LOAD_MORE_CITIES") +
                        "(" + startNextLoadNumber + "-" + endNextLoadNumber + " " +
                        app.vtranslate("JS_LBL_OF") + " " + data.result.totalCities + ")" +
                        " ***";

                selectValues.push(item);
            }

            response(selectValues);
        });
    },
    /*
     * Callback on street field edit - provides help to enter street, city, state and code by click mouse on needed element.
     * @param {$} editForm - form on thich address fields are placed
     * @param {Object} kladrState - address fields set of current helper by kladr 
     * @returns {undefined}
     */
    addAddressCallback: function (editForm, kladrState) {
        editForm.find('[name="' + kladrState.street + '"]').attr('placeholder', app.vtranslate('JS_LBL_HELP_ADDRESS_TYPE'));
        editForm.find('[name="' + kladrState.street + '"]').autocomplete({
            delay: 400,
            minLength: 3,
            source: function (request, response) {
                if (localStorage.getItem(kladrState.city + 'cityCode') != '') {

                    /* Common request params */
                    var requestParams = {
                        module: 'SPKladr',
                        action: 'EnterAddress',
                        mode: 'fullAddressTyped'
                    };

                    var addressParts = request.term.split(",");
                    switch (addressParts.length) {

                        /* Step 1 - get street in selected city */
                        case 1:
                            requestParams.requestStep = 2;
                            requestParams.streetName = request.term;
                            requestParams.cityCode = localStorage.getItem(kladrState.city + 'cityCode');
                            AppConnector.request(requestParams).then(function (data) {
                                var selectValues = [];
                                if (data !== null && data.success) {
                                    selectValues = data.result;
                                }

                                /* Format search result with delimiter */
                                response($.map(selectValues, function (item) {
                                    item.label = item.streetSocr + ' ' + item.streetName;
                                    item.value = item.label + ', ';

                                    /* Prepare to save item in storage on click */
                                    item.saveStreetCode = true;

                                    return item;
                                }));
                            });
                            break;

                            /* Step 2 - get house number */
                        case 2:
                            requestParams.requestStep = 3;
                            requestParams.houseNumber = addressParts[1];
                            requestParams.streetCode = localStorage.getItem(kladrState.street + 'streetCode');

                            /* Request for second param - street or another small location  */
                            AppConnector.request(requestParams).then(function (data) {
                                var selectValues = [];
                                if (data !== null && data.success) {
                                    selectValues = data.result;
                                }

                                /* Format before display */
                                response($.map(selectValues, function (item) {
                                    item.label = addressParts[0] + ', ' + item.houseNumber;
                                    item.value = item.label;
                                    item.autofill = true;

                                    return item;
                                }));
                            });
                            break;

                        default:
                            response();
                            break;
                    }
                } else {
                    response();
                }
            },
            select: function (event, selectedObject) {

                /* Street was selected - need save it code */
                if (selectedObject.item.saveStreetCode) {
                    localStorage.setItem(kladrState.street + 'streetCode', selectedObject.item.streetCode);
                }

                /* Adds info of clicked item in other address fields */
                if (selectedObject.item.autofill) {
                    editForm.find('[name="' + kladrState.code + '"]').val(selectedObject.item.mailIndex);
                }
            }
        });
    },
    /**
     * Add helper callback on city field. Provides helped select on typing in city input field.
     * @param {$} editForm - form on thich address fields are placed
     * @param {Object} kladrState - address fields set of current helper by kladr
     * @returns {undefined}
     */
    addCityCallback: function (editForm, kladrState) {
        var parentEntity = this;
        var requestParams = {
            module: 'SPKladr',
            action: 'EnterAddress',
            mode: 'fullAddressTyped',
            cityRecordsLimit: 10,
            cityOffset: 0,
            requestStep: 1
        };

        editForm.find('[name="' + kladrState.city + '"]').autocomplete({
            delay: 100,
            minLength: 3,
            source: function (request, response) {
                requestParams.cityName = request.term;
                parentEntity.loadCities(request, response, requestParams, kladrState.city);
            },
            select: function (event, selectedObject) {
                var clickedItem = selectedObject.item;

                /* Handling first step - load more cities pagination */
                if (clickedItem.isLoadMoreCities) {
                    requestParams.cityOffset = clickedItem.cityOffset;
                    editForm.find('[name="' + kladrState.city + '"]').autocomplete("search");
                    requestParams.cityOffset = 0;
                } else {
                    selectedObject.item.value = clickedItem.citySocr + ' ' + clickedItem.cityName;
                    localStorage.setItem(kladrState.city + 'cityCode', clickedItem.cityCode);
                    editForm.find('[name="' + kladrState.state + '"]').val(clickedItem.stateSocr + ' ' + clickedItem.stateName);
                    editForm.find('[name="' + kladrState.region + '"]').val(clickedItem.regionSocr + ' ' + clickedItem.regionName);
                }
            }
        });
    },
    /**
     * Add helper callback on state field. Provides helped select on typing in state input field.
     * @param {$} editForm - form on thich address fields are placed
     * @param {Object} kladrState - address fields set of current helper by kladr
     * @returns {undefined}
     */
    addStateCallback: function (editForm, kladrState) {
        editForm.find('[name="' + kladrState.state + '"]').autocomplete({
            delay: 100,
            minLength: 2,
            source: function (request, response) {
                AppConnector.request({
                    module: 'SPKladr',
                    action: 'EnterAddress',
                    mode: 'stateTyped',
                    stateName: request.term
                }).then(function (data) {
                    var selectValues = [];
                    if (data !== null && data.success) {
                        selectValues = data.result;
                    }

                    response($.map(selectValues, function (item) {
                        item.value = item.stateSocr + ' ' + item.stateName;
                        item.label = item.value;

                        return item;
                    }));
                });
            }
        });
    },
});


$(document).ready(function () {
    var kladrController = new SPKladr_Js();
    kladrController.registerKladrIntegration();
});

