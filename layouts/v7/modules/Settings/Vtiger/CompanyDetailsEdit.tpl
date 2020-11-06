{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Settings/Vtiger/views/CompanyDetailsEdit.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
    {* SalesPlatform.ru begin *}
    <div class="editViewContainer">
        <input type="hidden" id="existsCompanies" value='{ZEND_JSON::encode(Settings_Vtiger_CompanyDetails_Model::getCompanies())|escape:'html'}'>
        <input type="hidden" id="organizationId" value="{$COMPANY_MODEL->getId()}">
        
        <form class="form-horizontal" id="updateCompanyDetailsForm" method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="Vtiger" />
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="action" value="CompanyDetailsSave" />
            <div class="form-group companydetailsedit">
                <label class="col-sm-2 fieldLabel control-label"> {vtranslate('LBL_COMPANY_LOGO',$QUALIFIED_MODULE)}</label>
                <div class="fieldValue col-sm-5" >
                    <div class="company-logo-content">
                        <img src="{$COMPANY_MODEL->getLogoPath()}" class="alignMiddle" style="max-width:700px;"/>
                        <br><hr>
                        <input type="file" name="logo" id="logoFile" />
                    </div>
                    <br>
                    <div class="alert alert-info" >
                        {vtranslate('LBL_LOGO_RECOMMENDED_MESSAGE',$QUALIFIED_MODULE)}
                    </div>
                </div>
            </div>

            {foreach from=$COMPANY_MODEL->getFields() item=FIELD_TYPE key=FIELD}
                {if $FIELD neq 'logoname' && $FIELD neq 'logo' }
                    <div class="form-group companydetailsedit">
                        <label class="col-sm-2 fieldLabel control-label ">
                            {vtranslate($FIELD,$QUALIFIED_MODULE)}{if $FIELD eq 'organizationname' || $FIELD eq 'company'}&nbsp;<span class="redColor">*</span>{/if}
                        </label>
                        <div class="fieldValue col-sm-5">
                            {if $FIELD eq 'company'}
                                {if $COMPANY_MODEL->getId() neq ''}
                                    <input type="hidden" name="{$FIELD}" value="{$COMPANY_MODEL->get($FIELD)}"/>
                                    <div class="marginTop5px">{vtranslate($COMPANY_MODEL->get($FIELD, $MODULE))}</div>
                                {else}
                                    <input type="text" data-rule-required="true" class="inputElement" name="{$FIELD}" value="{$COMPANY_MODEL->get($FIELD)}"/>
                                {/if}
                            {else if $FIELD eq 'address'}
                                <textarea class="form-control col-sm-6 resize-vertical" rows="2" name="{$FIELD}">{$COMPANY_MODEL->get($FIELD)}</textarea>
                            {else if $FIELD eq 'website'}
                                <input type="text" class="inputElement" data-rule-url="true" name="{$FIELD}" value="{$COMPANY_MODEL->get($FIELD)}"/>
                            {else}
                                <input type="text" {if $FIELD eq 'organizationname'} data-rule-required="true" {/if} class="inputElement" name="{$FIELD}" value="{$COMPANY_MODEL->get($FIELD)}"/>
                            {/if}
                        </div>
                    </div>
                {/if}
            {/foreach}

            <div class="modal-overlay-footer clearfix">
                <div class="row clearfix">
                    <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
                        <button type="submit" class="btn btn-success saveButton">{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class="cancelLink" data-dismiss="modal" onclick="window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {* SalesPlatform.ru end *}
{/strip}