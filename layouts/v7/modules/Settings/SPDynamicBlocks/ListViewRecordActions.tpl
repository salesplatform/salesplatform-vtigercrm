{*/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/*}
{strip}
    <div class="table-actions">      
            {foreach item=RECORD_LINK from=$LISTVIEW_ENTRY->getRecordLinks()}
                <span>
                {assign var="RECORD_LINK_URL" value=$RECORD_LINK->getUrl()}
                
                {if $RECORD_LINK->getIcon() eq 'icon-pencil' }
                      <a {if stripos($RECORD_LINK_URL, 'javascript:')===0} title='{vtranslate('LBL_EDIT', $MODULE)}' onclick="{$RECORD_LINK_URL|substr:strlen("javascript:")};if(event.stopPropagation){ldelim}event.stopPropagation();{rdelim}else{ldelim}event.cancelBubble=true;{rdelim}" {else} href='{$RECORD_LINK_URL}' {/if}>
                      <i class="fa fa-pencil" ></i>
                      </a>
                {/if}
                {if  $RECORD_LINK->getIcon() eq 'icon-trash'}
                    <a {if stripos($RECORD_LINK_URL, 'javascript:')===0} title="{vtranslate('LBL_DELETE', $MODULE)}" onclick="{$RECORD_LINK_URL|substr:strlen("javascript:")};if(event.stopPropagation){ldelim}event.stopPropagation();{rdelim}else{ldelim}event.cancelBubble=true;{rdelim}" {else} href='{$RECORD_LINK_URL}' {/if}>
                    <i class="fa fa-trash" ></i>
                    </a>
                {/if}
                {if !$RECORD_LINK@lastui-sortable}
                    &nbsp;&nbsp;
                {/if}
                </span>
            {/foreach}
    </div>
{/strip}