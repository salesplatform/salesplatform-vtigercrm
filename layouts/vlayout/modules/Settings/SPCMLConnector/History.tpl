{strip}
    <div class="container-fluid">
        <div class="widget_header">
		<h3>{vtranslate('LBL_TRANSACTION_HISTORY', $QUALIFIED_MODULE)}</h3>
	</div>
        <hr>
        
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                    <tr class="listViewHeaders">

                            {foreach item=HEADER from=$MODEL->getHeaders()}
                            <th nowrap>
                                    {vtranslate($HEADER, $QUALIFIED_MODULE)}
                            </th>
                            {/foreach}
                    </tr>
            </thead>

            {foreach item=ENTRY from=$MODEL->getEntries()}
                <tr class="listViewEntries">
                    {foreach item=ENTRY_ELEMENT from=$ENTRY}
                    <td class="listViewEntryValue  nowrap">
                       {vtranslate($ENTRY_ELEMENT,$QUALIFIED_MODULE)}
                    </td>
                    {/foreach}
                </tr>
            {/foreach}
        </table>
    </div>
{/strip}