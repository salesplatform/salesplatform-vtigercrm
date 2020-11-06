{*
/*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: SalesPlatform Ltd
* The Initial Developer of the Original Code is SalesPlatform Ltd.
* All Rights Reserved.
* If you have any questions or comments, please email: devel@salesplatform.ru
************************************************************************************/
*}
{strip}
    <table class="table table-bordered rulesTable">
        <thead>
            <tr class="listViewContentHeader">
                <th class="listViewEntryValue"></th>
                <th class="listViewEntryValue">{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</th>
                <th class="listViewEntryValue">{vtranslate('LBL_TIP_TYPE', $QUALIFIED_MODULE)}</th>
                <th class="listViewEntryValue">{vtranslate('LBL_SELECTED_AUTOCFOMPLETE_FIELD', $QUALIFIED_MODULE)}</th>
                <th class="listViewEntryValue">{vtranslate('LBL_FILL_IN_FIELDS', $QUALIFIED_MODULE)}</th>
            </tr>
        </thead>
        <tbody>
            {foreach key=key item=ITEM from=$EXISTING_RULES}
                {assign var=TIP_FIELD value=$ITEM->getTipFieldModel()}

                <tr class="listViewEntries">
                    <td width="5%">
                        <div class="table-actions text-center">
                            <a href="index.php?module=SPTips&view=EditRules&parent=Settings&record={$ITEM->getId()}&providerId={$ITEM->get('provider_id')}">
                                <i class="fa fa-pencil"></i>
                            </a>
                            &nbsp;&nbsp;
                            <a href="#" class="deleteRule" data-rule-id="{$ITEM->getId()}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </td>
                    <td class="listViewEntryValue">{vtranslate($ITEM->getModuleName(), $ITEM->getModuleName())}</td>
                    <td class="listViewEntryValue">{vtranslate($ITEM->getType(), $QUALIFIED_MODULE)}</td>
                    <td class="listViewEntryValue">
                        {if $TIP_FIELD}
                            {vtranslate($TIP_FIELD->get('label'), $ITEM->getModuleName())}
                        {else}
                            {vtranslate($ITEM->getTipFieldName(), $ITEM->getModuleName())}
                        {/if}
                    </td>
                    <td class="listViewEntryValue">
                        <ul class="lists-menu">
                            {foreach item=DEPENDENT_FIELD_MODEL from=$ITEM->getDependentFields()}
                                {assign var=VTIGER_FIELD value=$DEPENDENT_FIELD_MODEL->getVtigerField()}
                                <li style="font-size:12px;" class="listViewFilter" >
                                    {if $VTIGER_FIELD}
                                        {vtranslate($VTIGER_FIELD->get('label'), $ITEM->getModuleName())} &nbsp;&nbsp; <i class="fa fa-arrow-left"> &nbsp;&nbsp; </i> {vtranslate($DEPENDENT_FIELD_MODEL->getProviderFieldName(), $QUALIFIED_MODULE)}
                                    {else}
                                        {vtranslate($DEPENDENT_FIELD_MODEL->getVtigerFieldName(), $ITEM->getModuleName())}&nbsp;&nbsp;<i class="fa fa-arrow-left">&nbsp;&nbsp;</i> {vtranslate($DEPENDENT_FIELD_MODEL->getProviderFieldName(), $QUALIFIED_MODULE)}
                                    {/if}
                                </li>
                            {/foreach}
                        </ul>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/strip}