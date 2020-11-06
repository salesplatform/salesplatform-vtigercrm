{***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************}
{strip}
    <div class="container-fluid">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="socialSettings">
	<div class="widget_header row-fluid clearfix">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><h3 style="margin-top: 0px;">{vtranslate('LBL_SPSOCIALCONNECTOR_SETTINGS', $QUALIFIED_MODULE)}</h3></div>
        {assign var=MODULE_MODEL value=Settings_SPSocialConnector_Module_Model::getCleanInstance()}
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><div class="btn-group pull-right"><button class="btn btn-default editButton" data-url='{$MODULE_MODEL->getEditViewUrl()}&mode=showpopup' type="button" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</strong></button></div></div>
	</div>
        </div>
        <br>
        <div class="contents row-fluid">
		<table class="table table-bordered table-condensed themeTableColor">
			<thead>
				<tr class="blockHeader">
					<th colspan="2" class="mediumWidthType">
						<span class="alignMiddle">{vtranslate('LBL_SPSOCIALCONNECTOR_CONFIG', $QUALIFIED_MODULE)}</span>
					</th>
				</tr>
			</thead>
			<tbody>
                {assign var=FIELDS value=Settings_SPSocialConnector_Module_Model::getSettingsParameters()}
                {foreach item=FIELD_TYPE key=FIELD_NAME from=$FIELDS}
                        <tr><td width="25%"><label class="muted pull-right marginRight10px ">{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</label></td>
                        <td style="border-left: none;"><span>{$RECORD_MODEL->get($FIELD_NAME)}</span></td></tr>
                {/foreach}
                <input type="hidden" name="module" value="SPSocialConnector"/>
                <input type="hidden" name="action" value="SaveAjax"/>
                <input type="hidden" name="parent" value="Settings"/>
			</tbody>
		</table>
                <br>
<div class="span4 alert alert-info container-fluid">
{vtranslate('LBL_NOTE', $QUALIFIED_MODULE)} <a target="blank" href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640_%D0%98%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F_%D1%81%D0%BE_%D1%81%D1%82%D0%BE%D1%80%D0%BE%D0%BD%D0%BD%D0%B8%D0%BC%D0%B8_%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0%D0%BC%D0%B8#.D0.98.D0.BD.D1.82.D0.B5.D0.B3.D1.80.D0.B0.D1.86.D0.B8.D1.8F_.D1.81_.D1.81.D0.BE.D1.86.D0.B8.D0.B0.D0.BB.D1.8C.D0.BD.D1.8B.D0.BC.D0.B8_.D1.81.D0.B5.D1.82.D1.8F.D0.BC.D0.B8">{vtranslate('LBL_DOCS', $QUALIFIED_MODULE)}</a>
</div>	
	</div>
</div>

</div>
{/strip}
