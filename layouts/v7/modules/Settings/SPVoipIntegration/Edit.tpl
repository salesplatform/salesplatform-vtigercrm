{strip}
    <div class="container-fluid">
         <div class="widget_header row">
            <div class="col-sm-12">
                <h4>{vtranslate('LBL_SP_VOIP_SETTINGS', $QUALIFIED_MODULE)}</h4>
            </div>
        </div>
        <hr>
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTop15px">
            {assign var=DEFAULT_PROVIDER value=Settings_SPVoipIntegration_Record_Model::getDefaultProvider()}
            <form id="voipEditFrom" class="form-horizontal" data-detail-url="{$MODULE_MODEL->getDetailViewUrl()}">
                <input type="hidden" name="module" value="SPVoipIntegration"/>
                <input type="hidden" name="action" value="SaveAjax"/>
                <input type="hidden" name="parent" value="Settings"/>

                <div class="pull-left marginBottom10px" style="width: 100%;"> 
                    <label class="pull-left marginRight10px marginTop5px">{vtranslate('LBL_USE_CLICK_TO_CALL', $QUALIFIED_MODULE)}</label>
                    <input type="checkbox" name="use_click_to_call" {if Settings_SPVoipIntegration_Record_Model::isClickToCallEnabled()} checked {/if} />
                </div>
                
                <div class="pull-left" style="width: 100%;"> 
                    <label class="pull-left marginRight10px marginTop5px">{vtranslate('LBL_DEFAULT_PROVIDER', $QUALIFIED_MODULE)}</label>
                    <select name="default_provider" class="select2" style="min-width: 150px;">
                        {foreach item=PROVIDER_NAME from=$PROVIDER['existing_providers']}
                            <option value="{$PROVIDER_NAME}" {if $DEFAULT_PROVIDER eq $PROVIDER_NAME} selected{/if}>{ucfirst(vtranslate($PROVIDER_NAME, $QUALIFIED_MODULE))}</option>
                        {/foreach}
                    </select>
                </div>
                
                {foreach key=PROVIDER_NAME item=PROVIDER_FIELDS_INFO from=$FIELDS_INFO}
                    <div class="widget_header row-fluid providerData {if $PROVIDER_NAME neq $DEFAULT_PROVIDER} hide {/if}" data-provider="{$PROVIDER_NAME}">
                        <div class="pull-left width100per">
                            <h3>{vtranslate('LBL_VOIP_PROVIDER_SETTINGS', $QUALIFIED_MODULE)} {ucfirst(vtranslate($PROVIDER_NAME, $QUALIFIED_MODULE))}</h3>
                        </div>
                        <table class="table table-bordered table-condensed themeTableColor">
                            <tbody>
                                {foreach item=FIELD_INFO from=$PROVIDER_FIELDS_INFO}
                                    <tr>
                                        <td width="25%">
                                            <label class="muted pull-right marginRight10px">{vtranslate($FIELD_INFO['field_label'], $QUALIFIED_MODULE)}</label>
                                        </td>
                                        <td style="border-left: none;">
                                            <input class="inputElement" name="{$FIELD_INFO['field_name']}" value="{$FIELD_INFO['field_value']}" />
                                        </td>
                                    </tr>  
                                {/foreach}
                            </tbody>
                        </table>

                        {if $PROVIDER_NAME eq 'zebra'}
                            <div class="col-lg-12 marginBottom10px">
                                <button id='registerWebhooks' class="btn btn-default pull-right" type="button"><strong>{vtranslate('LBL_REGISTER_WEBHOOKS', $QUALIFIED_MODULE)}</strong></button>
                            </div>
                        {/if}    
                    </div>
                {/foreach}

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-toolbar">
                    <div class="pull-right">
                        <button class="btn btn-success saveButton" type="submit" title="{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}">
                            <strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong>
                        </button>
                        <a type="reset" class="cancelLink" title="{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}">
                            {vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}
                        </a>
                    </div>
                </div>
        </div>      

    </form>
</div>
{/strip}