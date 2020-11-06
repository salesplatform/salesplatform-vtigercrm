{*
/*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: SalesPlatform Ltd
* The Initial Developer of the Original Code is SalesPlatform Ltd.
* All Rights Reserved.
* If you have any questions or comments, please email: devel@salesplatform.ru
************************************************************************************/
*}
{strip}
    <div class="col-sm-12 col-xs-12 ">
        <div id="listview-actions" class="listview-actions-container marginTop10px">

            <div class="list-content row">
                <div class="col-sm-12 col-xs-12 ">
                    <h4 style="margin-top: 30px;">{vtranslate('LBL_EXISTING_RULES', $QUALIFIED_MODULE)}</h4>
                    
                    <div class="marginTop15px">
                        <button id="addRule" class="btn btn-default pull-left marginBottom10px">
                            <strong>{vtranslate('LBL_CREATE_RULE', $QUALIFIED_MODULE)}</strong>
                        </button>
                        {include file="RulesTable.tpl"|@vtemplate_path:$QUALIFIED_MODULE}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

{/strip}