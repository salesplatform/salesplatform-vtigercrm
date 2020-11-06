{strip}    
    <div class="container-fluid">
        <div class="widget_header row">
            <div class="col-sm-8">
                <h4>{vtranslate('LBL_SP_VOIP_SETTINGS', $QUALIFIED_MODULE)}</h4>
            </div>
            <div class="col-sm-4">
                <div class="btn-group pull-right">
                    <button class="btn btn-default editButton" data-url='{$MODULE_MODEL->getEditViewUrl()}&mode=showpopup' type="button" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}">
                        <strong>{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</strong>
                    </button>
                </div>
            </div>
        </div>
        <hr>

        {assign var=PROVIDER_NAME value=Settings_SPVoipIntegration_Record_Model::getDefaultProvider()}
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTop15px"> 
            <table class="table table-bordered table-condensed themeTableColor">
                <tbody>
                    <tr>
                        <td width="25%">
                            <label class="muted pull-right marginRight10px text-right">{vtranslate('LBL_USE_CLICK_TO_CALL', $QUALIFIED_MODULE)}</label>
                        </td>
                        <td style="border-left: none;">
                            <span>
                                {if Settings_SPVoipIntegration_Record_Model::isClickToCallEnabled()}  
                                    {vtranslate('LBL_YES', $QUALIFIED_MODULE)}
                                {else} 
                                    {vtranslate('LBL_NO', $QUALIFIED_MODULE)}
                                {/if}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <label class="muted pull-right marginRight10px ">{vtranslate('LBL_DEFAULT_PROVIDER', $QUALIFIED_MODULE)}</label>
                        </td>
                        <td style="border-left: none;">
                            <span>{vtranslate($PROVIDER_NAME, $QUALIFIED_MODULE)}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {assign var=PROVIDER_FIELDS_INFO value=$FIELDS_INFO[$PROVIDER_NAME]}
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="{$PROVIDER_NAME}settings">                    
            <div class="widget_header row-fluid clearfix">
                <div class="pull-left" style="width: 100%;">
                    <h4>
                        {vtranslate('LBL_VOIP_PROVIDER_PARAMETERS', $QUALIFIED_MODULE)} {ucfirst(vtranslate($PROVIDER_NAME, $QUALIFIED_MODULE))}
                    </h4>
                </div>        
                <table class="table table-bordered table-condensed themeTableColor">
                    <tbody>
                        {foreach item=FIELD_INFO from=$PROVIDER_FIELDS_INFO}
                            <tr>
                                <td width="25%">
                                    <label class="muted pull-right marginRight10px ">{vtranslate($FIELD_INFO['field_label'], $QUALIFIED_MODULE)}</label>
                                </td>
                                <td style="border-left: none;">
                                    <span>{$FIELD_INFO['field_value']}</span>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {if $PROVIDER_NAME eq 'zebra'}
                <button id='registerWebhooks' class="btn btn-default pull-right" type="button"><strong>{vtranslate('LBL_REGISTER_WEBHOOKS', $QUALIFIED_MODULE)}</strong></button>
                <br>
                <br>
            {/if}    
        </div>     
    </div>             
{/strip}	
