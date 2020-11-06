{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<footer class="app-footer">
        {* SalesPlatform.ru begin *}
        <div class="pull-right footer-icons">
            <small>{vtranslate('LBL_CONNECT_WITH_US', $MODULE)}&nbsp;</small>
            <!-- SalesPlatform begin #5822 -->
            <!-- <a href="http://community.salesplatform.ru/"><img src="layouts/vlayout/skins/images/forum.png"></a>
            &nbsp;<a href="https://twitter.com/salesplatformru"><img src="layouts/vlayout/skins/images/twitter.png"></a> -->
            <a href="http://community.salesplatform.ru/" target="_blank" title="{vtranslate('Community', $MODULE)}"><i class="fa fa-comments"></i></a>
            <a href="https://twitter.com/salesplatformru" target="_blank" title="Twitter"><i class="fa fa-twitter"></i></a>
            <a href="https://vk.com/salesplatform" target="_blank" title="Vk"><i class="fa fa-vk"></i></a>
            <a href="https://youtube.com/salesplatform" target="_blank" title="YouTube"><i class="fa fa-youtube-play"></i></a>
            <!-- SalesPlatform end -->
        </div>
        {* SalesPlatform.ru end *}
	<p>
		{* SalesPlatform begin*}
                {*Powered by vtiger CRM - 7.0&nbsp;&nbsp;© 2004 - {date('Y')}&nbsp;&nbsp;*}
                {*<a href="//www.vtiger.com" target="_blank">Vtiger</a>&nbsp;|&nbsp;*}
                {*<a href="https://www.vtiger.com/privacy-policy" target="_blank">Privacy Policy</a>*}

            {vtranslate('POWEREDBY')} {$VTIGER_VERSION} &nbsp;
            &copy; 2004 - {date('Y')}&nbsp&nbsp;
            <a href="//www.vtiger.com" target="_blank">vtiger.com</a>
            &nbsp;|&nbsp;
            {* SalesPlatform.ru begin Doc links fixed *}
            &copy; 2011 - {date('Y')}&nbsp&nbsp;
            <a href="//salesplatform.ru/" target="_blank">SalesPlatform.ru</a>
            {*SalsePlatform end *}
	</p>
</footer>
</div>
<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement), 
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>
<div class="modal myModal fade"></div>
{include file='JSResources.tpl'|@vtemplate_path}
</body>

</html>