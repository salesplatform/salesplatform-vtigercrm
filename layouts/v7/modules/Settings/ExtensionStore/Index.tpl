{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    {* SalesPlatform.ru begin *}
    {include file="modules/Vtiger/partials/Topbar.tpl"}

    <div class="container-fluid app-nav">
            <div class="row">
                {include file="partials/SidebarHeader.tpl"|vtemplate_path:$QUALIFIED_MODULE}
                {include file="ModuleHeader.tpl"|vtemplate_path:$QUALIFIED_MODULE}
            </div>
    </div>
    </nav>    

    <div class="main-container clearfix">
	<div class=" ExtensionscontentsDiv contentsDiv">
    {* SalesPlatform.ru end *}
            <div class="col-sm-12 col-xs-12 content-area" id="importModules">
		<div class="row">
			<div class="col-sm-4 col-xs-4">
				<div class="row">
					<div class="col-sm-8 col-xs-8">
                                                {* SalesPlatform.ru begin localization fix *}
						{*<input type="text" id="searchExtension" class="extensionSearch form-control" placeholder="{vtranslate('Search for an extension..', $QUALIFIED_MODULE)}"/>*}
						<input type="text" id="searchExtension" class="extensionSearch form-control" placeholder="{vtranslate('LBL_SEARCH_FOR_AN_EXTENSION', $QUALIFIED_MODULE)}"/>
                                                {* SalesPlatform.ru end localization fix *}
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="contents row">
			<div class="col-sm-12 col-xs-12" id="extensionContainer">
				{include file='ExtensionModules.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
			</div>
		</div>

		{include file="CardSetupModals.tpl"|@vtemplate_path:$QUALIFIED_MODULE}
	</div>
{/strip}