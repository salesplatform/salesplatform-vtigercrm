{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<div>
    {* SalesPlatform.ru begin *}
	<table width="100%" cellpadding="3" cellspacing="1" border="0" class="table table-bordered detailview-table">
    {* <table width="100%" cellpadding="3" cellspacing="1" border="0" class="lvt small"> *}
    {* SalesPlatform.ru end *}
        {* SalesPlatform.ru begin *}
        <tr>
            <th>{vtranslate('LBL_NUMBER', $MODULE)}</th>
            <th>{vtranslate('LBL_STATUS', $MODULE)}</th>
            <th>{vtranslate('LBL_STATUS_MESSAGE', $MODULE)}</th>
        </tr> 
        {if $RECORDS|@count ne 0}
            {foreach item=RECORD from=$RECORDS}
            <tr bgcolor="{SMSNotifier_Record_Model::getBackgroundColorForStatus($RECORD['status'])}">
                <td nowrap="nowrap" width="33%">{$RECORD['tonumber']}</td>
                <td nowrap="nowrap" width="33%">{vtranslate($RECORD['status'], $MODULE)}</td>
                <td nowrap="nowrap" width="33%">{$RECORD['statusmessage']}</td>
                {*<td nowrap="nowrap" bgcolor="{$RECORD->get('statuscolor')}" width="25%">{$RECORD->get('tonumber')}</td>*}
            </tr>
            {/foreach}
        {else}
            <tr>
                <td nowrap="nowrap" width="100%" colspan="3">{vtranslate('LBL_NO_NUMBERS_TO_SEND_SMS', $MODULE)}</td>
            </tr>
        {/if}
        {* SalesPlatform.ru end *}
	</table>
</div>