{strip}
    <div class="container-fluid">
        <div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
            <div class="tab-pane active" id="providersTab">	
                <div id="pickListValuesTable">
                    <div class=" vt-default-callout vt-info-callout">
                        <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
                        <ul>
                            <li>{vtranslate('LBL_DIFFERENT_RULES_FOR_PROVIDERS', $QUALIFIED_MODULE)}</li>
                            <li>{vtranslate('LBL_AUTOCOMPLETE_FIELDS', $QUALIFIED_MODULE)}</li>
                        </ul>
                    </div>

                    <div class="controls fieldValue col-sm-6 marginTop10px">
                        <select id="existingProviders" class="select2" name="modulesList" style="min-width: 250px;">
                            {foreach item=PROVIDER from=$EXISTING_PROVIDERS}
                                <option value="{$PROVIDER->getId()}" {if $PROVIDER->getId() eq $SELECTED_PROVIDER->getId()} selected {/if}> 
                                    {vtranslate($PROVIDER->getName(), $MODULE_NAME)}
                                </option>
                            {/foreach}   
                        </select>
                        <button id="editProvider" type="button" class="btn btn-default marginLeft10px">
                            <strong>{vtranslate('LBL_EDIT_PROVIDER', $QUALIFIED_MODULE)}</strong>
                        </button>
                    </div>
                </div>
            </div>

            <div id="rulesContainer">
                {include file="ListRules.tpl"|@vtemplate_path:$QUALIFIED_MODULE}
            </div>	
        </div>
    </div>
{/strip}	
