{strip}
    
    <div class="container-fluid">
        <div class="widget_header">
		<h3>{vtranslate('LBL_CML_SETTINGS', $QUALIFIED_MODULE)}</h3>
	</div>
        <hr>
        
            <form id="cmlSettingsForm">

            
                <table class="table-nobordered table">
                    <tr>
                        <td class="textAlignRight"> <label class="muted control-label">{vtranslate('LBL_ADMIN_LOGIN', $QUALIFIED_MODULE)}</label> </td>
                        <td> <input class="input-xxlarge" type="text" name="adminLogin" id="adminLogin" value="{$MODEL->getAdminLogin()}"> </td>
                    </tr>
                    <tr>
                        <td class="textAlignRight"> <label class="muted control-label">{vtranslate('LBL_ADMIN_PASSWORD', $QUALIFIED_MODULE)}</label></td>
                        <td> <input class="input-xxlarge" type="text" name="adminPassword" id="adminPassword" value="{$MODEL->getAdminPassword()}"></td>
                    </tr>
                    <tr>
                        <td class="textAlignRight"> <label class="muted control-label">{vtranslate('LBL_WEBSITE_URL', $QUALIFIED_MODULE)}</label></td>
                        <td> <input class="input-xxlarge" type="text" name="websiteURL" id="websiteURL" value="{$MODEL->getSiteUrl()}"></td>
                    </tr>
                    <tr>
                        <td class="textAlignRight"> <label class="muted control-label">{vtranslate('LBL_ASSIGNED_USER', $QUALIFIED_MODULE)}</label></td>
                        <td>
                            <select name="assignedUser" id="assignedUser" class="small">
                                {foreach item=USER_MODEL from=$USERS}
                                        {assign var=USER_NAME value=$USER_MODEL->get('user_name')}
                                        <option value="{$USER_NAME}" {if $MODEL->getAssignedUser() eq $USER_NAME} selected {/if}>{vtranslate($USER_MODEL->getName(), $QUALIFIED_MODULE)}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                </table>       
                <div class="row-fluid">
                        <div class="span6 padding1per">
                                <button class="btn addButton pull-right" type="submit" name="saveCmlSettings"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>      
                        </div>
                        <div class="span6">&nbsp;</div>
                </div>            
                      
            </form> 
                        
            <div class="widget_header">
		<h3>{vtranslate('LBL_STATUSES_SETTINGS', $QUALIFIED_MODULE)}</h3>
            </div>
            <hr>
            
            <button id="editStatus" class="btn addButton"  onclick="location.href='index.php?module=SPCMLConnector&view=List&parent=Settings'"><strong>{vtranslate('LBL_EDIT_STATUSES_SETTINGS', $QUALIFIED_MODULE)}</strong></button>
            <button id="history" class="btn addButton pull-right"  onclick="location.href='index.php?module=SPCMLConnector&view=History&parent=Settings'"><strong>{vtranslate('LBL_TRANSACTION_HISTORY', $QUALIFIED_MODULE)}</strong></button>
            <br>
            <br>
            
            <table class="table table-bordered listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">

				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if} class="{$WIDTHTYPE}">
					<a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('column')}">{vtranslate($LISTVIEW_HEADER->get('label'), $QUALIFIED_MODULE)}
						&nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
				</th>
				{/foreach}
			</tr>
		</thead>
                
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
                    <tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}'>
                        {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                        <td class="listViewEntryValue  nowrap">
                           {vtranslate($LISTVIEW_ENTRY->get($LISTVIEW_HEADER->get('name')),$QUALIFIED_MODULE)}
                        </td>
                        {/foreach}
                    </tr>
                {/foreach}
            </table>
    </div>
{/strip}	
