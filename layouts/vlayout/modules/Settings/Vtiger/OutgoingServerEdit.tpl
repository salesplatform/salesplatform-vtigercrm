{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{strip}
    <div class="container-fluid">
        <div class="contents row-fluid">
            {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
            <form id="OutgoingServerForm" class="form-horizontal" data-detail-url="{$MODEL->getDetailViewUrl()}" method="POST">
                <div class="widget_header row-fluid">
                    <div class="span8"><h3>{vtranslate('LBL_OUTGOING_SERVER', $QUALIFIED_MODULE)}</h3>&nbsp;{vtranslate('LBL_OUTGOING_SERVER_DESC', $QUALIFIED_MODULE)}</div>
                    <div class="span4 btn-toolbar"><div class="pull-right">
                            <button class="btn btn-success saveButton" type="submit" title="{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
                            <a type="reset" class="cancelLink" title="{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
                        </div></div>
                </div>
                <hr>

                <input type="hidden" name="default" value="false" />
                <input type="hidden" name="server_port" value="0" />
                <input type="hidden" name="server_type" value="email" />
                <input type="hidden" name="id" value="{$MODEL->get('id')}" />

                <div class="row-fluid hide errorMessage">
                    <div class="alert alert-error">
                        {vtranslate('LBL_TESTMAILSTATUS', $QUALIFIED_MODULE)}<strong>{vtranslate('LBL_MAILSENDERROR', $QUALIFIED_MODULE)}</strong>
                    </div>
                </div>
                <table class="table table-bordered table-condensed themeTableColor">
                    <thead>
                    <tr class="blockHeader"><th colspan="2" class="{$WIDTHTYPE}">{vtranslate('LBL_MAIL_SERVER_SMTP', $QUALIFIED_MODULE)}</th></tr>
                    </thead>
                    <tbody>
                    <tr><td width="25%" class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SENDMAIL', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="checkbox" name="use_sendmail" {if $MODEL->isUseSendMailEnabled() neq 'false'}checked{/if}/></td></tr>
                    {* SalesPlatform.ru begin *}
                    <tr><td width="25%" class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_USE_MAIL_ACCOUNT', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="checkbox" name="use_mail_account" {if $MODEL->isUseMailAccountEnabled() neq 'false'}checked{/if}/></td></tr>
                    {* SalesPlatform.ru end *}
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_SERVER_NAME', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="server" data-validation-engine='validate[required]' value="{$MODEL->get('server')}" /></td></tr>
                    {* SalesPlatform.ru begin optional fields *}
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SERVER_PORT', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="server_port" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_port')}"</td></tr>
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_USER_NAME', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="server_username" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_username')}"</td></tr>
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_PASSWORD', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="password" name="server_password" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_password')}"</td></tr>
                    {*<tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_USER_NAME', $QUALIFIED_MODULE)}</label></td>
-                                               <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="server_username" data-validation-engine='validate[required]' value="{$MODEL->get('server_username')}"</td></tr>
-                                       <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>{vtranslate('LBL_PASSWORD', $QUALIFIED_MODULE)}</label></td>
-                                               <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="password" name="server_password" data-validation-engine='validate[required]' value="{$MODEL->get('server_password')}"</td></tr>*}
                    {* SalesPlatform.ru end *}
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_FROM_EMAIL', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="from_email_field" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator='{Zend_Json::encode([['name' => 'Email']])}' value="{$MODEL->get('from_email_field')}"</td></tr>
                    {* SalesPlatform.ru begin *}
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_FROM_NAME', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="text" name="from_name" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('from_name')}"</td></tr>
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_REQUIRES_AUTHENTICATION', $QUALIFIED_MODULE)}</label></td>
                        {* SalesPlatform.ru end *}
                        {* SalesPlatform.ru begin display checkbox *}
                        <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="checkbox" name="smtp_auth" {if $MODEL->isSmtpAuthEnabled() neq 'false'}checked{/if}/></td></tr>
                    {* <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="checkbox" name="smtp_auth" {if $MODEL->isSmtpAuthEnabled()}checked{/if}/></td></tr> *}
                    {* SalesPlatform.ru end *}
                    {* SalesPlatform.ru begin *}
                    <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_CONNECTION_SECURITY', $QUALIFIED_MODULE)}</label></td>
                        <td class="{$WIDTHTYPE}" style="border-left: none;">
                            <input type="radio" name="server_tls" value="no" {if $MODEL->get('server_tls') eq 'no'}checked=true{elseif $MODEL->get('server_tls') neq 'tls' && $MODEL->get('server_tls') neq 'ssl'}checked=true{/if}>&nbsp; {vtranslate('LBL_NO_TLS', $QUALIFIED_MODULE)}&nbsp;
                            <input type="radio" name="server_tls" value="tls" {if $MODEL->get('server_tls') eq 'tls'}checked=true{/if}>&nbsp; {vtranslate('LBL_TLS', $QUALIFIED_MODULE)}&nbsp;
                            <input type="radio" name="server_tls" value="ssl" {if $MODEL->get('server_tls') eq 'ssl'}checked=true{/if}>&nbsp; {vtranslate('LBL_SSL', $QUALIFIED_MODULE)} </td>
                    </tr>
                    {* SalesPlatform.ru end *}
                    </tbody>
                </table>
                <br>
                <div class="alert alert-info">{vtranslate('LBL_OUTGOING_SERVER_NOTE', $QUALIFIED_MODULE)}</div>
                {* SalesPlatform.ru begin *}
                <div class="alert alert-info">{vtranslate('LBL_OUTGOING_SERVER_NOTE_2', $QUALIFIED_MODULE)}</div>
                {* SalesPlatform.ru end *}
            </form>
        </div>
    </div>
{/strip}