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
    <div class="container-fluid">
        <div class="col-sm-12 col-xs-12">
            <div class=" vt-default-callout vt-info-callout">
                <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
                <p>{vtranslate('LBL_DIFFERENT_RULES_FOR_PROVIDERS', $QUALIFIED_MODULE)}</p>
                <p>{vtranslate('LBL_CURRENT_PROVIDER', $QUALIFIED_MODULE)}&nbsp;<strong>{$PROVIDER->getName()}</strong></p>
            </div>
            <div class="editViewContainer marginTop10px">
                <form id="rulesForm" class="form-horizontal" method="POST">
                    <div class="editViewBody">
                        <div class="editViewContents">
                            {* select with modules *}
                            <div class="form-group">
                                <label class="muted control-label col-sm-2 col-xs-2">{vtranslate('LBL_SELECT_MODULE', $QUALIFIED_MODULE)}</label>
                                <div class="controls col-sm-3 col-xs-3">
                                    <select name="sourceModule" class="select2 form-control marginLeftZero" data-rule-required="true">
                                        {foreach item=MODULE_MODEL from=$AVAILABLE_MODULES}
                                            {assign var=MODULE_NAME value=$MODULE_MODEL->get('name')}
                                            <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $RECORD_MODEL->getModuleName()} selected {/if}>
                                                {vtranslate($MODULE_NAME, $MODULE_NAME)}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            
                            {* Tip type *}
                            <div class="form-group">
                                <label class="muted control-label col-sm-2 col-xs-2">
                                    {vtranslate('LBL_TIP_TYPE', $QUALIFIED_MODULE)}
                                </label>
                                <div class="controls col-sm-3 col-xs-3">
                                    <select name="type" class="select2 form-control" data-rule-required="true">
                                        {foreach item=TIP_TYPE from=$SUPPORTED_TYPES}
                                            <option value="{$TIP_TYPE}" {if $RECORD_MODEL->getType() eq $TIP_TYPE} selected {/if}>{vtranslate($TIP_TYPE, $QUALIFIED_MODULE)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            
                                    
                            {* select with mandatory source fields *}
                            <div class="form-group">
                                <label class="muted control-label col-sm-2 col-xs-2">
                                    {vtranslate('LBL_SELECTED_AUTOCFOMPLETE_FIELD', $QUALIFIED_MODULE)}
                                </label>
                                <div class="controls col-sm-3 col-xs-3">
                                    <select name="sourceField" class="select2 form-control" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}" data-rule-required="true">
                                        <option value=''></option>
                                        {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                            <option value="{$FIELD_NAME}" {if $RECORD_MODEL->getTipFieldName() eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_LABEL, $RECORD_MODEL->getModuleName())}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>


                            {* Fill fields mapping *}
                            <div style="margin-top: 50px;">
                                <h4>{vtranslate('LBL_AUTOCOMPLETE_MAPPING', $QUALIFIED_MODULE)}</h4>
                            </div>
                            <div class="btn-toolbar marginTop15px" style="margin-bottom: 15px;">
                                <span class="btn-group">
                                    <button type="button" class="btn btn-default" id="addDependendField" data-module-name="SPTips">
                                        <i class="fa fa-plus"></i>
                                        &nbsp;&nbsp;
                                        <strong>{vtranslate('LBL_ADD_MAPPING', $QUALIFIED_MODULE)}</strong>
                                    </button> 
                                </span>
                            </div>
                            
                            <div class="form-group">
                                <div class="controls col-sm-3 col-xs-3">
                                    <label class="muted">{vtranslate('LBL_CRM_FIELD', $QUALIFIED_MODULE)}</label>
                                </div>
                                <div class="controls col-sm-3 col-xs-3">
                                    <label class="muted">{vtranslate('LBL_PROVIDER_FIELD', $QUALIFIED_MODULE)}</label>
                                </div>
                            </div>
                                    
                            {if !$SKIP_DEPENDENT}
                                {foreach item=ITEM from=$RECORD_MODEL->getDependentFields()}
                                    <div class="form-group">
                                        <div class="controls col-sm-3 col-xs-3">
                                            <select name="dependentFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}" data-rule-required="true">
                                                {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                                    <option value="{$FIELD_NAME}" {if $ITEM->getVtigerFieldName() eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_LABEL, $RECORD_MODEL->getModuleName())}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="controls col-sm-3 col-xs-3">
                                            <select name="providerFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}" data-rule-required="true">
                                                <option value=''></option>
                                                {foreach key=FIELD_NAME item=FIELD_LABEL from=$PROVIDER_PICKLIST_FIELDS}
                                                    <option value="{$FIELD_NAME}" {if $ITEM->getProviderFieldName() eq $FIELD_NAME} selected {/if}>{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <a role="javascript:void(0)" class="deleteFieldsItemLine">
                                            <strong><i class="fa fa-trash" style="vertical-align: middle"></i></strong>
                                        </a>
                                    </div>
                                {/foreach}
                            {/if}


                            {* template for adding new fields mapping *}
                            <div class="form-group hide lineItemCopy">
                                <div class="controls col-sm-3 col-xs-3">
                                    <select name="dependentFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                        {foreach key=FIELD_NAME item=FIELD_LABEL from=$PICKLIST_FIELDS}
                                            <option value="{$FIELD_NAME}">{vtranslate($FIELD_LABEL, $RECORD_MODEL->getModuleName())}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="controls col-sm-3 col-xs-3">
                                    <select name="providerFields[]" class="select2 form-control select2-offscreen" data-placeholder="{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}">
                                        <option value=''></option>
                                        {foreach key=FIELD_NAME item=FIELD_LABEL from=$PROVIDER_PICKLIST_FIELDS}
                                            <option value="{$FIELD_NAME}">{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <a role="javascript:void(0)" class="deleteFieldsItemLine">
                                    <strong><i class="fa fa-trash" style="vertical-align: middle"></i></strong>
                                </a>
                            </div>
                        </div>




                        <input type="hidden" name="module" value="SPTips"/>
                        <input type="hidden" name="parent" value="Settings"/>
                        <input type="hidden" name="action" value="SaveRule"/>
                        <input type="hidden" name="record" value="{$RECORD_MODEL->getId()}"/>
                        <input type="hidden" name="providerId" value="{$PROVIDER->getId()}"/>
                        <div class='modal-overlay-footer clearfix'>
                            <div class="row clearfix">
                                <div class=' textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                                    <button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                                    <a class='cancelLink'  href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
{/strip}