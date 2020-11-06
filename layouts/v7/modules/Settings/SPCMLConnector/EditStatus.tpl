{strip}
<div class="taxModalContainer modal-dialog modal-xs">
    <div class="modal-content">
        <form id="editStatus" class="form-horizontal">
            {assign var=STATUS_ID value=$RECORD_MODEL->getId()}
            {if empty($STATUS_ID)}
                {assign var=TITLE value={vtranslate('LBL_ADD_NEW_STATUS_ACCORD', $QUALIFIED_MODULE)}}
            {else}
                {assign var=TITLE value={vtranslate('LBL_EDIT_STATUS_ACCORD', $QUALIFIED_MODULE)}}
            {/if}
            {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
            <input type="hidden" name="record" value="{$STATUS_ID}" />
            <div class="modal-body">
                <div class="blockData">
                     <!-- Select crm status  -->
                    <div class="row form-group">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-3">
                            <label>{vtranslate('Crm Status', $QUALIFIED_MODULE)}&nbsp;<span class="redColor">*</span></label>
                        </div>
                        <div class=" col-lg-4 col-md-4 col-sm-4">
                            <select class="select2 inputElement " name="crmStatus"> 
                                {foreach item=CRM_STATUS from=$RECORD_MODEL->getCrmStatuses()}
                                    <option value="{$CRM_STATUS}" {if $RECORD_MODEL->getCrmStatus() eq $CRM_STATUS} selected {/if}> {vtranslate($CRM_STATUS, $QUALIFIED_MODULE)} </option>
                                {/foreach}
                            </select> 
                        </div>
                        <div class="col-lg-3"></div>
                    </div>                    
                    <!-- Site status -->
                    <div class="row form-group">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-3">
                            <label>{vtranslate('Site Status', $QUALIFIED_MODULE)}&nbsp;<span class="redColor">*</span></label>
                        </div>
                        <div class="">
							<div class=" col-lg-4 col-md-4 col-sm-4">
								<input type="text" class="inputElement" name="siteStatus" value="{$RECORD_MODEL->getSiteStatus()}" data-rule-required="true"/>
							</div>
						</div>                        
                    </div>
                </div>
            </div>
            {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
        </form>    
    </div>
</div>
{/strip}