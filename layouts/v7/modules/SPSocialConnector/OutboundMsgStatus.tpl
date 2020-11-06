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
<div class="modal-dialog modal-lg">
    <div class="modal-content">
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right " >
                        <button type="button" class="close" onClick="self.close()" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class='fa fa-close'></span>
                        </button>
                    </div>
                    <h4 class="pull-left">
                        {vtranslate('Status', $MODULE)}
                    </h4>
                </div>
            </div>
        <div class="modal-body">
            <table class='table table-bordered table-condensed'>
                <thead class='listViewHeaders'>
                    <th>
                        {vtranslate('Social net', $MODULE)}
                    </th>
                    <th>
                        {vtranslate('Comment', $MODULE)}
                    </th>
                </thead>
                {foreach item=ITEM key=KEY from=$SOCIAL_NET_DOMEN}
                    <tr>
                        <td>
                            {$SOCIAL_NET_MAPPING[$ITEM->domen]}
                        </td>
                        <td>
                            {if $STATUS[$KEY] !== -1}
                                {vtranslate('Sent', $MODULE)}
                            {else}
                                {vtranslate('Not sent', $MODULE)}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>

        <div class="modal-footer">
            <div class=" pull-left cancelLinkContainer">
                <button class="btn btn-success" onClick="self.close()"><strong>{vtranslate('LBL_CLOSE', $MODULE)}</strong></button>
            </div>
        </div>
    </div>
</div>
{/strip}