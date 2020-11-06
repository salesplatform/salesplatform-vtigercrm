{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
<div id="sendSmsContainer" class='modal-dialog modal-lg'>
	<div class="modal-content">
            <form class="form-horizontal" id="massSave" method="post" action="index.php">
                {assign var=HEADER_TITLE value={vtranslate('Compose message', $MODULE)}|cat:" "|cat:{vtranslate($SINGLE_MODULE, $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <div class="modal-body">        

		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
		<input type="hidden" name="action" value="MassSaveAjax" />
		<input type="hidden" name="viewname" value="{$VIEWNAME}" />
		<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
		<input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
        <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
        <input type="hidden" name="operator" value="{$OPERATOR}" />
        <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
		<div class="modal-body tabbable">
			<div>
				<span><strong>{vtranslate('LBL_STEP_1',$MODULE)}</strong></span>
				&nbsp;:&nbsp;
				{vtranslate('Please select the URL to send the message',$MODULE)}
			</div>
                <select name="fields" data-placeholder="{vtranslate('LBL_ADD_MORE_FIELDS',$MODULE)}" multiple class="select2 select2-offscreen" style="width: 100%">
					{foreach item=URL_FIELD from=$URL_FIELDS}
						{assign var=URL_FIELD_NAME value=$URL_FIELD->get('name')}
						<option value="{$SINGLE_RECORD->get($URL_FIELD_NAME)}">
							{if !empty($SINGLE_RECORD)}
								{assign var=FIELD_VALUE value=$SINGLE_RECORD->get($URL_FIELD_NAME)}
							{/if}
							{vtranslate($URL_FIELD->get('label'), $SOURCE_MODULE)}{if !empty($FIELD_VALUE)} ({$FIELD_VALUE}){/if}
						</option>
					{/foreach}
			</select>
			<hr>
			<div>
				<span><strong>{vtranslate('LBL_STEP_2',$MODULE)}</strong></span>
				&nbsp;:&nbsp;
				{vtranslate('Message',$MODULE)}
			</div>
			<textarea class="inputElement form-control" name="message" id="message" placeholder="{vtranslate('LBL_WRITE_YOUR_MESSAGE_HERE', $MODULE)}"></textarea>
		</div>
                </div>
		<div class="modal-footer">
                       {if $BUTTON_NAME neq null}
                            {assign var=BUTTON_LABEL value=$BUTTON_NAME}
                        {else}
                            {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE)}}
                        {/if}
                        <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-success" type="submit" name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
                        <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</div>
            </form>
        </div>
</div>
{/strip}