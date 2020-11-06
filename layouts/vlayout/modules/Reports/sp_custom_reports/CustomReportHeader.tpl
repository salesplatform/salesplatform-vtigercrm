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
        <input type="hidden" name="customJsController" id="customJsController" value="{$CUSTOM_REPORT->getCustomUIControllerName()}" />
        <form id="detailView" onSubmit="return false;">
            {assign var=SELECTED_VIEW value=$CUSTOM_REPORT->getViewTypeName()}
            
            <input type="hidden" name="custom_report_data" value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($CUSTOM_REPORT_DATA))}'/>
            <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
            <input type="hidden" name="chartViews" id="chartViews" value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($CUSTOM_REPORT->getChartsViewControlData()))}'>
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
                {include file='sp_custom_reports/CustomReportAdvanceFilter.tpl'|@vtemplate_path:$MODULE RECORD_STRUCTURE=$RECORD_STRUCTURE ADVANCE_CRITERIA=$SELECTED_ADVANCED_FILTER_FIELDS COLUMNNAME_API=getReportFilterColumnName}
                <div class="additionalControls" style="margin-bottom: 15px">
                    {foreach item=FIELD_MODEL from=$CUSTOM_REPORT->getCustomControlFields()}
                        <div>
                            <span style="margin-right: 10px;">{$FIELD_MODEL->get('label')}</span>
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(), 'Vtiger') MODULE='Vtiger'}
                        </div>
                        
                    {/foreach}
                </div>
                
                <div class="row-fluid" id="viewTypesContainer">
                    {assign var=DISPLAY_TYPES value=$CUSTOM_REPORT->getChartsViewControlData()}
                    <span style="margin-right: 10px;">{vtranslate('LBL_VIEWTYPE', 'Reports')}:</span>
                    <select id="displayType" class="chzn-select">
                        {foreach item=TYPE from=array_keys($CUSTOM_REPORT->getChartsViewControlData())}
                            <option value="{$TYPE}" {if $TYPE eq $SELECTED_VIEW} selected {/if}>{vtranslate($TYPE, 'Reports')}</option>
                        {/foreach}
                    </select>
                    
                    
                    {* Add chart views grouping options *}
                    <span id="agregationContainer" class="{if $SELECTED_VIEW eq Reports_CustomReportTypes_Model::TABLE} hide {/if} ">
                        {assign var=DISPLAY_TYPE_DATA value=$DISPLAY_TYPES[$SELECTED_VIEW]}
                        <span style="margin-right: 10px; margin-left: 75px">{vtranslate('LBL_GROUP_BY', 'Reports')}:</span>
                        <select id="groupingSelect" class="chzn-select">
                            {foreach key=GROUP_TYPE item=TRANSLATION from=$DISPLAY_TYPE_DATA['group']}
                                <option value="{$GROUP_TYPE}">{vtranslate($TRANSLATION, 'Reports')}</option>
                            {/foreach}
                        </select>

                        <span style="margin-right: 10px; margin-left: 75px">{vtranslate('LBL_AGREGATE_BY', 'Reports')}:</span>
                        {if $SELECTED_VIEW neq Reports_CustomReportTypes_Model::PIE}
                            <select id="agregateSelect" multiple class="chzn-select">
                                {foreach key=AGGREGATE_TYPE item=TRANSLATION from=$DISPLAY_TYPE_DATA['agregate']}
                                    <option value="{$AGGREGATE_TYPE}" selected>{vtranslate($TRANSLATION, 'Reports')}</option>
                                {/foreach}
                            </select>
                        {else}
                            <select id="agregateSelect" class="chzn-select">
                                {foreach key=AGGREGATE_TYPE item=TRANSLATION from=$DISPLAY_TYPE_DATA['agregate']}
                                    <option value="{$AGGREGATE_TYPE}" {if $CUSTOM_REPORT->getViewTypeDetails()->getAgregateFields() eq $AGGREGATE_TYPE}selected{/if}>{vtranslate($TRANSLATION, 'Reports')}</option>
                                {/foreach}
                            </select>
                        {/if}
                    </span>
                </div>
                <br><br>
                <div class="row-fluid">
                    <div class="textAlignCenter">
                        <button class="btn generateCustomReport" data-mode="generate" value="{vtranslate('LBL_GENERATE_NOW',$MODULE)}">
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