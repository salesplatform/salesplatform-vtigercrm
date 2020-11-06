{*/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/*}
{strip}
	<div class="editViewPageDiv " id="editViewContent">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
			<div class="contents">
				<form id="EditView" class="form-horizontal" method="POST">
                                        <input type="hidden" name="record" value="{$RECORD_ID}" id="record" />
                                        <input type="hidden" name="module" value="SPDynamicBlocks" />
                                        <input type="hidden" name="action" value="Save" />
                                        <input type="hidden" name="parent" value="Settings" />
					{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}                                        
					<div>
                                            <h4>{vtranslate('LBL_BLOCKS_CONFIGURATION_EDIT', $QUALIFIED_MODULE)}</h4>
					</div>
					<hr>
					<br>
                                        <div class="detailViewInfo">
                                            <div class="row form-group">
                                                <div class="col-lg-4 control-label fieldLabel">
                                                    <label>{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</label>
                                                    <span class="redColor">*</span>
                                                </div>
                                                <div class="col-lg-4 input-group">
                                                    <div class="input-group inputElement">
                                                        <select class="select2-container inputElement select2 col-lg-11" name="module_name" data-rule-required="true" >
                                                            <option></option>
                                                            {foreach item=LIST_MODULE_MODEL from=$MODULES_LIST}
                                                                {assign var=LIST_MODULE_NAME value=$LIST_MODULE_MODEL->getName()}
                                                                <option {if $LIST_MODULE_NAME eq $RECORD_MODEL->get('module_name')} selected {/if} value="{$LIST_MODULE_NAME}">{vtranslate($LIST_MODULE_NAME, $LIST_MODULE_NAME)}</option>                                                                    
                                                            {/foreach}
							</select>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-lg-4 control-label fieldLabel">
                                                    <label>{vtranslate('LBL_PICKLIST', $QUALIFIED_MODULE)}</label>
                                                    <span class="redColor">*</span>
                                                </div>
                                                <div class="col-lg-4 input-group">
                                                    <div class="input-group inputElement">
                                                        <select class="select2-container inputElement select2 col-lg-11" name="field_name" data-rule-required="true">
                                                            <option></option>
                                                            {foreach key=PICKLIST_NAME item=PICKLIST_LABEL from=$PICKLISTS}
                                                                <option {if $PICKLIST_NAME eq $RECORD_MODEL->get('field_name')} selected {/if} value="{$PICKLIST_NAME}">{vtranslate($PICKLIST_LABEL, $RECORD_MODEL->get('module_name'))}</option>                                                                    
                                                            {/foreach}
							</select>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="form-group">
                                                    <div class="col-lg-4 control-label fieldLabel">
                                                        <label>{vtranslate('LBL_VALUES', $QUALIFIED_MODULE)}</label>
                                                        <span class="redColor">*</span>
                                                    </div>
                                                    <div class="col-lg-4 input-group">
                                                        <div class="input-group inputElement">
                                                            <select class="select2-container inputElement select2 col-lg-11" multiple name="values[]" data-rule-required="true">
                                                                <option></option>
                                                                {foreach key=FIELD_VALUE item=VALUE_LABEL from=$VALUES}
                                                                    <option {if in_array($FIELD_VALUE, $RECORD_MODEL->getValues())} selected {/if} value="{$FIELD_VALUE}">{vtranslate($VALUE_LABEL, $RECORD_MODEL->get('module_name'))}</option>                                                                    
                                                                {/foreach}
                                                            </select>                                                        
                                                        </div>
                                                    </div>        
                                                </div>	
                                            </div>            
                                            <div class="row form-group">
                                                <div class="form-group">
                                                    <div class="col-lg-4 control-label fieldLabel">
                                                        <label>{vtranslate('LBL_BLOCKS', $QUALIFIED_MODULE)}</label>
                                                        <span class="redColor">*</span>
                                                    </div>
                                                    <div class="col-lg-4 input-group">
                                                        <div class="input-group inputElement">
                                                            <select class="select2-container inputElement select2 col-lg-11" multiple name="blocks[]" data-rule-required="true">
                                                                <option></option>
                                                                {foreach key=BLOCK_KEY item=BLOCK_LABEL from=$BLOCKS}
                                                                    <option {if in_array($BLOCK_KEY, $RECORD_MODEL->getBlocks())} selected {/if} value="{$BLOCK_KEY}">{vtranslate($BLOCK_LABEL, $RECORD_MODEL->get('module_name'))}</option>                                                                    
                                                                {/foreach}
                                                            </select>                                                        
                                                        </div>
                                                    </div>        
                                                </div>	
                                            </div> 
                                        </div>         
					<div class='modal-overlay-footer clearfix'>
						<div class=" row clearfix">
							<div class=' textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
								<button type='submit' class='btn btn-success saveButton'  >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
								<a class='cancelLink' href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{/strip}

