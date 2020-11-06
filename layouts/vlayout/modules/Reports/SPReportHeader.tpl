{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
    <div id="toggleButton" class="toggleButton" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
        <i id="tButtonImage" class="{if $LEFTPANELHIDE neq '1'}icon-chevron-left{else}icon-chevron-right{/if}"></i>
    </div>
    <div class="container-fluid">
    <div class="row-fluid reportsDetailHeader">
        <input type="hidden" name="date_filters" data-value='{ZEND_JSON::encode($DATE_FILTERS)}' />
        <form id="detailView" onSubmit="return false;">
            <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
            <br>
            <div class="reportHeader row-fluid">
                <div class='span5 textAlignCenter'>
                    <h3>{vtranslate($REPORT_MODEL->getName(),$MODULE)}</h3>
                    <div id="noOfRecords">{vtranslate('LBL_NO_OF_RECORDS',$MODULE)} <span id="countValue">{$COUNT}</span>
                        {if $COUNT > 1000}
                            <span class="redColor" id="moreRecordsText"> ({vtranslate('LBL_MORE_RECORDS_TXT',$MODULE)})</span>
                        {else}
                            <span class="redColor hide" id="moreRecordsText"> ({vtranslate('LBL_MORE_RECORDS_TXT',$MODULE)})</span>
                        {/if}
                    </div>
                </div>
                <div class='span4'>
                    <span class="pull-right">
                        <div class="btn-toolbar">
                            {foreach item=DETAILVIEW_LINK from=$DETAILVIEW_LINKS}
                                {assign var=LINKNAME value=$DETAILVIEW_LINK->getLabel()}
                                <div class="btn-group">
                                    <button class="btn reportActions" name="{$LINKNAME}" data-href="{$DETAILVIEW_LINK->getUrl()}">
                                        <strong>{$LINKNAME}</strong>
                                    </button>
                                </div>
                            {/foreach}
                        </div>
                    </span>
                </div>
            </div>
            <br>
            <div class="row-fluid">
                <input type="hidden" id="recordId" value="{$RECORD_ID}" />
                {assign var=RECORD_STRUCTURE value=array()}
                {assign var=PRIMARY_MODULE_LABEL value=vtranslate($PRIMARY_MODULE, $PRIMARY_MODULE)}
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$PRIMARY_MODULE_RECORD_STRUCTURE}
                    {assign var=PRIMARY_MODULE_BLOCK_LABEL value=vtranslate($BLOCK_LABEL, $PRIMARY_MODULE)}
                    {assign var=key value="$PRIMARY_MODULE_LABEL $PRIMARY_MODULE_BLOCK_LABEL"}
                    {if $LINEITEM_FIELD_IN_CALCULATION eq false && $BLOCK_LABEL eq 'LBL_ITEM_DETAILS'}
                    {else}
                        {$RECORD_STRUCTURE[$key] = $BLOCK_FIELDS}
                    {/if}
                {/foreach}
                {include file='SPAdvanceFilter.tpl'|@vtemplate_path:$MODULE}
                <div class="row-fluid">
                    <div class="textAlignCenter">
                        <button class="btn generateReport" data-mode="generate" value="{vtranslate('LBL_GENERATE_NOW',$MODULE)}">
                            <strong>{vtranslate('LBL_GENERATE_NOW',$MODULE)}</strong>
                        </button>
                    </div>
                </div>
                <br>
            </div>
        </form>
    </div>
    <div id="reportContentsDiv">
{/strip}