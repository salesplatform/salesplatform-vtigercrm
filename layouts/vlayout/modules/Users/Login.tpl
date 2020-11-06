{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
{* SalesPlatform.ru begin fixed display *}
{*<!DOCTYPE html>
<html>
	 <head>
		<title>SalesPlatform Vtiger CRM</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">*}
    {* SalesPlatform.ru end *}
		<!-- for Login page we are added -->
		<link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/jqueryBxslider.css" rel="stylesheet" />
		<script src="libraries/jquery/jquery.min.js"></script>
		<script src="libraries/jquery/boxslider/jqueryBxslider.js"></script>
		<script src="libraries/jquery/boxslider/respond.min.js"></script>
		<script>
			jQuery(document).ready(function(){
				scrollx = jQuery(window).outerWidth();
				window.scrollTo(scrollx,0);
				slider = jQuery('.bxslider').bxSlider({
				auto: true,
				pause: 4000,
				randomStart : true,
				autoHover: true
			});
			jQuery('.bx-prev, .bx-next, .bx-pager-item').live('click',function(){ slider.startAuto(); });
			}); 
		</script>
       {* SalesPlatform.ru begin fixed display *}         
	{*</head>
	<body>*}
        {* SalesPlatform.ru end *}  
		<div class="container-fluid login-container">
			<div class="row-fluid">
				<div class="span3">
					<div class="logo"><img src="layouts/vlayout/skins/images/logo.png">
					<br />
					<a target="_blank" href="http://{$COMPANY_DETAILSCOMPANY_DETAILS.website}">{$COMPANY_DETAILS.name}</a>
					</div>
				</div>
				<div class="span9">
					<div class="helpLinks">
                                                {* SalesPlatform.ru begin Localization fix*}
						<a href="http://community.salesplatform.ru/">{vtranslate('LBL_COMMUNITY','Users')}</a> | 
						<a href="http://community.salesplatform.ru/forums/">{vtranslate('LBL_FORUMS','Users')}</a> | 
						<a href="http://salesplatform.ru/wiki/">{vtranslate('LBL_WIKI','Users')}</a> | 
						<a href="http://community.salesplatform.ru/blogs/">{vtranslate('LBL_BLOGS','Users')}</a> | 
						<a href="http://salesplatform.ru/">SalesPlatform.ru</a>
                                                {* SalesPlatform.ru end Localization fix*}
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="content-wrapper">
						<div class="container-fluid">
							<div class="row-fluid">
                                                                {* SalesPlatform.ru begin Slider removed *}
                                                                {*
                                                                <!-- SalesPlatform.ru end -->
								<div class="span6">
									<div class="carousal-container">
										<div><h2> Get more out of Vtiger </h2></div>
										<ul class="bxslider">
											<li>
												<div id="slide01" class="slide">
													<img class="pull-left" src="{vimage_path('android_text.png')}">
													<img class="pull-right" src="{vimage_path('android.png')}"/>
												</div>
											</li>
											<li>
												<div id="slide02" class="slide">
													<img class="pull-left" src="{vimage_path('iphone_text.png')}"/>
													<img class="pull-right" src="{vimage_path('iphone.png')}"/>
												</div>
											</li>
											<li>
												<div id="slide03" class="slide">
													<img class="pull-left" src="{vimage_path('ipad_text.png')}"/>
													<img class="pull-right" src="{vimage_path('ipad.png')}"/>
												</div>
											</li>
											<li>
												<div id="slide04" class="slide">
													<img class="pull-left" src="{vimage_path('exchange_conn_text.png')}"/>
													<img class="pull-right" src="{vimage_path('exchange_conn.png')}"/>
												</div>
											</li>
											<li>
												<div id="slide05" class="slide">
													<img class="pull-left" src="{vimage_path('outlook_text.png')}"/>
													<img class="pull-right" src="{vimage_path('outlook.png')}"/>
												</div>
											</li>
										</ul>
									</div>
								</div>
                                                                <!-- SalesPlatform.ru begin Slider removed -->
                                                                *}
                                                                {* SalesPlatform.ru end *}
                                                                {* SalesPlatform.ru begin Center Login form *}
								<div class="span6" id="sp-login-span6">
									<div class="login-area" id="sp-login-area">
								<!-- <div class="span6"> -->
								<!--	<div class="login-area"> -->
                                                                <!-- SalesPlatform.ru end -->
										<div class="login-box" id="loginDiv">
											<div class="">
                                                                                        {* SalesPlatform.ru begin Localication fix*} 
												<h3 class="login-header">{vtranslate('LBL_LOGIN_IN_SYSTEM','Users')}</h3>
											</div>
											<form class="form-horizontal login-form" style="margin:0;" action="index.php?module=Users&action=Login" method="POST">
												{if isset($smarty.request.error)}
													<div class="alert alert-error">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<p>{vtranslate('LBL_INVALID_PASSWORD','Users')}</p>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												{/if}
												{if isset($smarty.request.fpError)}
													<div class="alert alert-error">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<p>{vtranslate('LBL_INVALID_EMAIL','Users')}</p>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												{/if}
												{if isset($smarty.request.status)}
													<div class="alert alert-success">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<p>{vtranslate('LBL_EMAIL_SEND_MESSAGE','Users')}</p>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												{/if}
												{if isset($smarty.request.statusError)}
													<div class="alert alert-error">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<p>{vtranslate('LBL_OUTGOING_SERVER_UNCONFIGURED','Users')}</p>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												{/if}
												<div class="control-group">
                                                                                                    {* SalesPlatform.ru begin Localization fix*} 
													<label class="control-label" for="username"><b>{vtranslate('LBL_USER_NAME','Users')}</b></label>
                                                                                                    {* SalesPlatform.ru end Localization fix*}
													<div class="controls">
														<input type="text" id="username" name="username" placeholder="Username">
													</div>
												</div>

												<div class="control-group">
                                                                                                    {* SalesPlatform.ru begin Localization fix*} 
													<label class="control-label" for="password"><b>{vtranslate('LBL_PASSWORD')}</b></label>
                                                                                                    {* SalesPlatform.ru end Localization fix*}
													<div class="controls">
														<input type="password" id="password" name="password" placeholder="Password">
													</div>
												</div>
												<div class="control-group signin-button">
													<div class="controls" id="forgotPassword">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<button type="submit" class="btn btn-primary sbutton">{vtranslate('LBL_SING_IN','Users')}</button>
														&nbsp;&nbsp;&nbsp;<a>{vtranslate('LBL_FOGGOT_PASSWORD','Users')}</a>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												</div>
												{* Retain this tracker to help us get usage details *}
												<img src='//stats.salesplatform.ru/stats.php?uid={$APPUNIQUEKEY}&v={$CURRENT_VERSION}&type=U' alt='' title='' border=0 width='1px' height='1px'>
											</form>
											<div class="login-subscript">
												<small> SalesPlatform Vtiger CRM {$CURRENT_VERSION}</small>
											</div>
										</div>
										
										<div class="login-box hide" id="forgotPasswordDiv">
											<form class="form-horizontal login-form" style="margin:0;" action="forgotPassword.php" method="POST">
												<div class="">
                                                                                                    {* SalesPlatform.ru begin Localization fix*} 
													<h3 class="login-header">{vtranslate('LBL_FOGGOT_PASSWORD_TITLE','Users')}</h3>
                                                                                                    {* SalesPlatform.ru end Localization fix*}
												</div>
												<div class="control-group">
                                                                                                    {* SalesPlatform.ru begin Localization fix*} 
													<label class="control-label" for="username"><b>{vtranslate('LBL_USER_NAME','Users')}</b></label>
                                                                                                    {* SalesPlatform.ru end Localization fix*}
													<div class="controls">
														<input type="text" id="username" name="username" placeholder="Username">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" for="email"><b>Email</b></label>
													<div class="controls">
														<input type="text" id="email" name="email"  placeholder="Email">
													</div>
												</div>
												<div class="control-group signin-button">
													<div class="controls" id="backButton">
                                                                                                            {* SalesPlatform.ru begin Localization fix*} 
														<input type="submit" class="btn btn-primary sbutton" value="{vtranslate('LBL_SUBMIT','Users')}" name="retrievePassword">
														&nbsp;&nbsp;&nbsp;<a>{vtranslate('LBL_BACK','Users')}</a>
                                                                                                            {* SalesPlatform.ru end Localization fix*}
													</div>
												</div>
											</form>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="navbar navbar-fixed-bottom">
			<div class="navbar-inner">
				<div class="container-fluid">
					<div class="row-fluid">
						<div class="span6 pull-left" >
							<div class="footer-content">
								<small>&#169 2004-{date('Y')}&nbsp;
									<a href="https://www.vtiger.com"> vtiger.com</a> | 
                                                                        {* SalesPlatform.ru begin *}
									&nbsp;&#169 2011-{date('Y')}&nbsp; <a href="http://salesplatform.ru/"> SalesPlatform.ru</a> 
                                                                        {*
									<a href="javascript:mypopup();">Read License</a> | 
									<a href="https://www.vtiger.com/products/crm/privacy_policy.html">Privacy Policy</a> </small>
                                                                        *}
                                                                        {* SalesPlatform.ru end *}
							</div>
						</div>
						<div class="span6 pull-right" >
							<div class="pull-right footer-icons">
								<small>{vtranslate('LBL_CONNECT_WITH_US', $MODULE)}&nbsp;</small>
                                                                {* SalesPlatform.ru begin *}
                                                                {*
								<a href="https://www.facebook.com/vtiger"><img src="layouts/vlayout/skins/images/facebook.png"></a>
                                                                *}
								<a href="http://community.salesplatform.ru/"><img src="layouts/vlayout/skins/images/forum.png"></a>
								&nbsp;<a href="https://twitter.com/salesplatformru"><img src="layouts/vlayout/skins/images/twitter.png"></a>
                                                                {*
								&nbsp;<a href="#"><img src="layouts/vlayout/skins/images/linkedin.png"></a>
								&nbsp;<a href="http://www.youtube.com/user/vtigercrm"><img src="layouts/vlayout/skins/images/youtube.png"></a> 
                                                                *}
                                                                {* SalesPlatform.ru end *}
							</div>
						</div>
					</div>   
				</div>    
			</div>   
		</div>
	</body>
	<script>
		jQuery(document).ready(function(){
			jQuery("#forgotPassword a").click(function() {
				jQuery("#loginDiv").hide();
				jQuery("#forgotPasswordDiv").show();
			});
			
			jQuery("#backButton a").click(function() {
				jQuery("#loginDiv").show();
				jQuery("#forgotPasswordDiv").hide();
			});
			
			jQuery("input[name='retrievePassword']").click(function (){
				var username = jQuery('#user_name').val();
				var email = jQuery('#emailId').val();
				
				var email1 = email.replace(/^\s+/,'').replace(/\s+$/,'');
				var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
				var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;
				
				if(username == ''){
					alert('Пожалуйста, введите корректное имя пользователя');
					return false;
				} else if(!emailFilter.test(email1) || email == ''){
					alert('Пожалуйста, введите корректный Email');
					return false;
				} else if(email.match(illegalChars)){
					alert( "Email содержит некорректные символы");
					return false;
				} else {
					return true;
				}
				
			});
		});
	</script>
{* SalesPlatform.ru begin fixed display *}        
{*</html>*}
{* SalesPlatform.ru end *}  
{/strip}
