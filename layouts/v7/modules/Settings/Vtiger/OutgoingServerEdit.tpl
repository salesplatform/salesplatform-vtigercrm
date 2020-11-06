{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Settings/Vtiger/views/OutgoingServerEdit.php *}

{strip}
	<div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div>
				<h3 style="margin-top: 0px;">{vtranslate('LBL_OUTGOING_SERVER', $QUALIFIED_MODULE)}</h3>&nbsp;{vtranslate('LBL_OUTGOING_SERVER_DESC', $QUALIFIED_MODULE)}
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="OutgoingServerForm" data-detail-url="{$MODEL->getDetailViewUrl()}" method="POST">
				<input type="hidden" name="default" value="false" />
				<input type="hidden" name="server_port" value="0" />
				<input type="hidden" name="server_type" value="email"/>
				<input type="hidden" name="id" value="{$MODEL->get('id')}"/>
				<div class="blockData">
					<br>
					<div class="hide errorMessage">
						<div class="alert alert-danger">
							{vtranslate('LBL_TESTMAILSTATUS', $QUALIFIED_MODULE)}<strong>{vtranslate('LBL_MAILSENDERROR', $QUALIFIED_MODULE)}</strong>
						</div>
					</div>
					<div class="block">
						<div>
							<div class="btn-group pull-right">
								<button class="btn t-btn resetButton" type="button" title="{vtranslate('LBL_RESET_TO_DEFAULT', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_RESET_TO_DEFAULT', $QUALIFIED_MODULE)}</strong></button>
							</div>
							<h4>{vtranslate('LBL_MAIL_SERVER_SMTP', $QUALIFIED_MODULE)}</h4>
						</div>
						<hr>
						<table class="table editview-table no-border">
							<tbody>
								<tr><td class="{$WIDTHTYPE} fieldLabel" style="width: 25%;"><label>{vtranslate('LBL_SENDMAIL', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input type="checkbox" name="use_sendmail" {if $MODEL->isUseSendMailEnabled() neq 'false'}checked{/if}/>
								        </div>
								    </td>
								</tr>
								{* SalesPlatform.ru begin *}
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_USE_MAIL_ACCOUNT', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input type="checkbox" name="use_mail_account" {if $MODEL->isUseMailAccountEnabled() neq 'false'}checked{/if}/>
								        </div>
								    </td>
								</tr>
								{* SalesPlatform.ru end *}
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label><span class="redColor">*</span>{vtranslate('LBL_SERVER_NAME', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="text" name="server" data-validation-engine='validate[required]' value="{$MODEL->get('server')}" />
								        </div>
								    </td>
								</tr>
								{* SalesPlatform.ru begin optional fields *}
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_SERVER_PORT', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="text" name="server_port" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_port')}"
								        </div>
								    </td>
								</tr>
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_USER_NAME', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="text" name="server_username" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_username')}"                                  
								        </div>
								    </td>
								</tr>
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_PASSWORD', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="password" name="server_password" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('server_password')}"
								        </div>
								    </td>
								</tr>
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_FROM_EMAIL', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="text" name="from_email_field" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator='{Zend_Json::encode([['name' => 'Email']])}' value="{$MODEL->get('from_email_field')}"
								        </div>
								    </td>
								</tr>
								{* SalesPlatform.ru begin *}
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_FROM_NAME', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input class="inputElement" type="text" name="from_name" data-validation-engine='validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]' value="{$MODEL->get('from_name')}"
								        </div>
								    </td>
								</tr>
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_REQUIRES_AUTHENTICATION', $QUALIFIED_MODULE)}</label></td>
								    {* SalesPlatform.ru end *}
								    {* SalesPlatform.ru begin display checkbox *}
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input type="checkbox" name="smtp_auth" {if $MODEL->isSmtpAuthEnabled() neq 'false'}checked{/if}/>
								        </div>
								    </td>
								</tr>
								{* <td class="{$WIDTHTYPE}" style="border-left: none;"><input type="checkbox" name="smtp_auth" {if $MODEL->isSmtpAuthEnabled()}checked{/if}/></td></tr> *}
								{* SalesPlatform.ru end *}
								{* SalesPlatform.ru begin *}
								<tr><td class="{$WIDTHTYPE} fieldLabel"><label>{vtranslate('LBL_CONNECTION_SECURITY', $QUALIFIED_MODULE)}</label></td>
								    <td class="{$WIDTHTYPE} fieldValue">
								        <div class=" col-lg-6 col-md-6 col-sm-12">
								            <input type="radio" name="server_tls" value="no" {if $MODEL->get('server_tls') eq 'no'}checked=true{elseif $MODEL->get('server_tls') neq 'tls' && $MODEL->get('server_tls') neq 'ssl'}checked=true{/if}>&nbsp; {vtranslate('LBL_NO_TLS', $QUALIFIED_MODULE)}&nbsp;
								            <input type="radio" name="server_tls" value="tls" {if $MODEL->get('server_tls') eq 'tls'}checked=true{/if}>&nbsp; {vtranslate('LBL_TLS', $QUALIFIED_MODULE)}&nbsp;
								            <input type="radio" name="server_tls" value="ssl" {if $MODEL->get('server_tls') eq 'ssl'}checked=true{/if}>&nbsp; {vtranslate('LBL_SSL', $QUALIFIED_MODULE)} 
								        </div>
								    </td>
								</tr>
								{* SalesPlatform.ru end *}
								</tbody>
							    </table>
								<br>
							    <div class="alert alert-info">{vtranslate('LBL_OUTGOING_SERVER_NOTE', $QUALIFIED_MODULE)}</div>
							    {* SalesPlatform.ru begin *}
							    <div class="alert alert-info">{vtranslate('LBL_OUTGOING_SERVER_NOTE_2', $QUALIFIED_MODULE)}</div>
							    {* SalesPlatform.ru end *}
					</div>
					<br>	
					<div class='modal-overlay-footer clearfix'>
						<div class="row clearfix">
							<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
								<button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
								<a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}
