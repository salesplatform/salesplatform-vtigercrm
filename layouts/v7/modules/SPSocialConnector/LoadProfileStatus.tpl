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
    <div class="modelContainer">
        <div class="modal-header">
            {if count($UPDATED_FIELDS) == 0}
                <h3>{vtranslate('No changed fields', $MODULE)}</h3><br>
            {else}
                <h3>{vtranslate('The following fields have been changed', $MODULE)}</h3><br>
            {/if}
        </div>

        <div>
            <table class='table table-bordered table-condensed'>
                <thead class='listViewHeaders'>
                <th>
                    {vtranslate('Field', $MODULE)}
                </th>
                <th>
                    {vtranslate('New value', $MODULE)}
                </th>
                </thead>
                {foreach item=UPDATED_FIELD from=$UPDATED_FIELDS}
                    <tr>
                        <td>
                            {vtranslate($UPDATED_FIELD['index'], $MODULE)}
                        </td>
                        <td>
                            {$UPDATED_FIELD['value']}
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
{/strip}