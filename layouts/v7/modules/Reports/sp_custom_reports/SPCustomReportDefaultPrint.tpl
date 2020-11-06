<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->
{strip}
    <tr>
    {assign var=COLUMNS_COUNT value=count($COLUMN_NAMES)}
    {assign var=COUNTER value=0}
    {foreach item=COLUMN_NAME from=$COLUMN_NAMES}
        {assign var=COUNTER value=$COUNTER + 1}
        {if $COUNTER eq $COLUMNS_COUNT && $CUSTOM_REPORT->hasLastLinkColumn()}
            {continue}
        {/if}
        <th>{$COLUMN_NAME}</th>
    {/foreach}
    </tr>
    {foreach item=REPORT_ROW from=$DATA}
        <tr>
        {assign var=COUNTER value=0}
        {foreach item=ROW_COLUMN from=$REPORT_ROW}
            {assign var=COUNTER value=$COUNTER + 1}
            {if $COUNTER eq $COLUMNS_COUNT && $CUSTOM_REPORT->hasLastLinkColumn()}
                {continue}
            {/if}
            <td>{$ROW_COLUMN}</td>
        {/foreach}
        </tr>
    {/foreach}
{/strip}