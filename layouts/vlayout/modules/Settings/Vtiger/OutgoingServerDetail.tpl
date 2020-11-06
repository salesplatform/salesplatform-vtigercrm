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
    <div class="container-fluid" id="OutgoingServerDetails">
        <div class="widget_header row-fluid">
            <div class="span8"><h3>{vtranslate('LBL_OUTGOING_SERVER', $QUALIFIED_MODULE)}</h3></div>
            <div class="span4"><div class="pull-right"><button class="btn editButton" data-url='{$MODEL->getEditViewUrl()}' type="button" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</strong></button></div></div>
        </div>
        <hr>
        <div class="contents row-fluid">
            <table class="table table-bordered table-condensed themeTableColor">
                <thead>
                <tr class="blockHeader">
                    <th colspan="2" class="{$WIDTHTYPE}">
                        <span class="alignMiddle">{vtranslate('LBL_MAIL_SERVER_SMTP', $QUALIFIED_MODULE)}</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                {* SalesPlatform.ru begin *}
                <tr><td width="25%" class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SENDMAIL', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{if $MODEL->isUseSendMailEnabled() neq 'false'}{vtranslate('LBL_YES', $QUALIFIED_MODULE)} {else}{vtranslate('LBL_NO', $QUALIFIED_MODULE)}{/if}</span></td></tr>
                <tr><td width="25%" class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_USE_MAIL_ACCOUNT', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{if $MODEL->isUseMailAccountEnabled() neq 'false'}{vtranslate('LBL_YES', $QUALIFIED_MODULE)} {else}{vtranslate('LBL_NO', $QUALIFIED_MODULE)}{/if}</span></td></tr>
                {* SalesPlatform.ru end *}
                <tr><td class="{$WIDTHTYPE}" width="25%"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SERVER_NAME', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{$MODEL->get('server')}</span></td></tr>
                {* SalesPlatform.ru begin *}
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_SERVER_PORT', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{$MODEL->get('server_port')}</span></td></tr>
                {* SalesPlatform.ru end *}
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_USER_NAME', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{$MODEL->get('server_username')}</span></td></tr>
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_PASSWORD', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span class="password">{if $MODEL->get('server_password') neq ''}
                            ******
                            {/if}&nbsp;</span></td></tr>
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_FROM_EMAIL', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{$MODEL->get('from_email_field')}</span></td></tr>
                {* SalesPlatform.ru begin *}
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_FROM_NAME', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{$MODEL->get('from_name')}</span></td></tr>
                {* SalesPlatform.ru end *}
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_REQUIRES_AUTHENTICATION', $QUALIFIED_MODULE)}</label></td>
                    {* SalesPlatform.ru begin display checkbox *}
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{if $MODEL->isSmtpAuthEnabled() neq 'false'}{vtranslate('LBL_YES', $QUALIFIED_MODULE)} {else}{vtranslate('LBL_NO', $QUALIFIED_MODULE)}{/if}</span></td></tr>
                {* <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{if $MODEL->isSmtpAuthEnabled()}{vtranslate('LBL_YES', $QUALIFIED_MODULE)} {else}{vtranslate('LBL_NO', $QUALIFIED_MODULE)}{/if}</span></td></tr> *}
                {* SalesPlatform.ru end *}
                {* SalesPlatform.ru begin *}
                <tr><td class="{$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_CONNECTION_SECURITY', $QUALIFIED_MODULE)}</label></td>
                    <td class="{$WIDTHTYPE}" style="border-left: none;"><span>{if $MODEL->get('server_tls') eq 'tls'}{vtranslate('LBL_TLS', $QUALIFIED_MODULE)} {elseif $MODEL->get('server_tls') eq 'ssl'}{vtranslate('LBL_SSL', $QUALIFIED_MODULE)} {else} {vtranslate('LBL_NO_TLS', $QUALIFIED_MODULE)}{/if}</span></td></tr>
                {* SalesPlatform.ru end *}
                </tbody>
            </table>
        </div>
    </div>
{/strip}