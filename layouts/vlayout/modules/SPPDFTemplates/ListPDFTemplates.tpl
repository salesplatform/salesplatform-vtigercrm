{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

<!-- If you want, you can place it in JS file -->
<script>
function massDelete()
{ldelim}
	if(typeof(document.massdelete.selected_id) == 'undefined')
		return false;
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {ldelim}

                if (document.massdelete.selected_id.checked)
               {ldelim}
                        document.massdelete.idlist.value=document.massdelete.selected_id.value+';';
			xx=1;
                {rdelim}
                else
                {ldelim}
                        alert("{vtranslate('SELECT_ATLEAST_ONE')}");
                        return false;
                {rdelim}
        {rdelim}
        else
        {ldelim}
                xx = 0;
                for(i = 0; i < x ; i++)
                {ldelim}
                        if(document.massdelete.selected_id[i].checked)
                        {ldelim}
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        {rdelim}
                {rdelim}
                if (xx != 0)
                {ldelim}
                        document.massdelete.idlist.value=idstring;
                {rdelim}
               else
                {ldelim}
                        alert("{vtranslate('SELECT_ATLEAST_ONE')}");
                        return false;
                {rdelim}
       {rdelim}
		if(confirm("{vtranslate('DELETE_CONFIRMATION')} "+xx+" {vtranslate('RECORDS')}?"))
		{ldelim}
	        	document.massdelete.action.value= "DeletePDFTemplate";
		{rdelim}
		else
		{ldelim}
			return false;
		{rdelim}

{rdelim}
</script>

{strip}
<div class="container-fluid">
     <div class="widget_header">
            <h3>{vtranslate('LBL_TEMPLATE_GENERATOR', $QUALIFIED_MODULE)}</h3>
            {vtranslate('LBL_TEMPLATE_GENERATOR_DESCRIPTION', $MODULE)}
    </div>
    <hr>
    <form  name="massdelete" action="index.php?module=SPPDFTemplates&action=DeletePDFTemplate" method="post">
        <input name="idlist" type="hidden">
        <input type="hidden" name="module" value="{$MODEL->getName()}">
        
        <input type="submit" class="btn btn-danger" onclick="return massDelete();"  value="{vtranslate('LBL_DELETE')}">
        <input type="button" class="btn addButton pull-right" onclick="location.href='{$MODEL->getCreateRecordUrl()}'" value="{vtranslate('LBL_ADD_TEMPLATE', $MODULE)}">
        <br><br>
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width=2% class="listViewHeaderValues">#</th>
                    <th width=3% class="listViewHeaderValues">{vtranslate('LBL_LIST_SELECT', $MODULE)}</th>
                    <th width=20% class="listViewHeaderValues">{vtranslate('LBL_TEMPLATE_NAME', $MODULE)}</th>
                    <th width=20% class="listViewHeaderValues">{vtranslate('LBL_MODULENAMES', $MODULE)}</th>
                    <th width=20% class="listViewHeaderValues">{vtranslate('LBL_COMPANY', 'Settings:Vtiger')}</th> 
                    <th width=10% class="listViewHeaderValues">{vtranslate('LBL_ACTION', $MODULE)}</th>
                </tr>
            </thead>

        {foreach item=template name=mailmerge from=$MODEL->getListTemplates()}
        <tr>
            <td class="listViewEntryValue" valign=top>{$smarty.foreach.mailmerge.iteration}</td>
            <td class="listViewEntryValue" valign=top><input type="checkbox" class=small name="selected_id" value="{$template->getId()}"></td>
            <td class="listViewEntryValue" valign=top>  <b><a href="{$template->getDetailViewUrl()}"> {$template->get('name')} </a></b></td>
            <td class="listViewEntryValue" valign=top>{vtranslate($template->get('module'))}</td>
            <td class="listViewEntryValue" valign=top><b>{vtranslate($template->get('spcompany'), 'Settings:Vtiger')}</b></a></td> 
            <td class="listViewEntryValue" valign=top nowrap>
                <a href="{$template->getEditViewUrl()}">{vtranslate('LBL_EDIT', $MODULE)}</a> | <a href="{$template->getDuplicateRecordUrl()}">{vtranslate('LBL_DUPLICATE', $MODULE)}</a>
            </td>
        </tr>
        {/foreach}
        </table>
    </form>
</div>
{/strip}