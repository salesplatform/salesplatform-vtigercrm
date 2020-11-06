{strip}
   
<div class="modal-header contentsBackground">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    {assign var=STATUS_ID value=$RECORD_MODEL->getId()}
    {if empty($STATUS_ID)}
        <h3>{vtranslate('LBL_ADD_NEW_STATUS_ACCORD', $QUALIFIED_MODULE)}</h3>
    {else}
        <h3>{vtranslate('LBL_EDIT_STATUS_ACCORD', $QUALIFIED_MODULE)}</h3>
    {/if}
</div>
<form id="editStatus" class="form-horizontal">
    <input type="hidden" name="record" value="{$STATUS_ID}" />
    <div class="modal-body">
        <div class="row-fluid">
            
            <!-- Select crm status  -->
            <div class="control-group">
                <label class="muted control-label">
                    <span class="redColor">*</span>&nbsp;{vtranslate('Crm Status', $QUALIFIED_MODULE)}
                </label>
                <div class="controls row-fluid">
                    <select class="select2 span6" name="crmStatus">
                        {foreach item=CRM_STATUS from=$RECORD_MODEL->getCrmStatuses()}
                            <option value="{$CRM_STATUS}" {if $RECORD_MODEL->getCrmStatus() eq $CRM_STATUS} selected {/if}> {vtranslate($CRM_STATUS, $QUALIFIED_MODULE)} </option>
                        {/foreach}
                    </select>
                </div>
            </div>
                    
            <!-- Site status -->
            <div class="control-group">
                <label class="muted control-label"><span class="redColor">*</span>&nbsp;{vtranslate('Site Status', $QUALIFIED_MODULE)}</label>
                <div class="controls">
                    <input type="text" name="siteStatus" value="{$RECORD_MODEL->getSiteStatus()}"/>
                </div>
            </div>
        </div>
    </div>
    {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
</form>    
{/strip}