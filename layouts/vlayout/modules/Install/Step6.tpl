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
<form class="form-horizontal" name="step6" method="post" action="index.php">
	<input type=hidden name="module" value="Install" />
	<input type=hidden name="view" value="Index" />
	<input type=hidden name="mode" value="Step7" />
	<input type=hidden name="auth_key" value="{$AUTH_KEY}" />

	<div class="row-fluid main-container">
		<div class="inner-container">
			<div class="row-fluid">
				<div class="span10">
					<h4>{vtranslate('LBL_ONE_LAST_THING','Install')}</h4>
				</div>
				<div class="span2">
					{* SalesPlatform.ru begin Link to SP wiki *}
                    <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640" target="_blank" class="pull-right">
                    {*<a href="https://wiki.vtiger.com/vtiger6/" target="_blank" class="pull-right">*}
                    {* SalesPlatform.ru end *}
						<img src="{'help.png'|vimage_path}" alt="Help-Icon"/>
					</a>
				</div>
			</div>
		    <hr>
			<div class="offset2 row-fluid">
				<div class="span8">
					<table class="config-table input-table">
						<tbody>
							<tr>
								<td>    {*SalesPlatform.ru begin*}
									<strong>{vtranslate('LBL_YOUR_INDUSTRY','Install')}</strong> <span class="no">*</span>
                                                                        {*vtiger commented code
                                                                        <strong>Please let us know your Industry</strong> <span class="no">*</span>
                                                                        *}
                                                                        {*SalesPlatform.ru end*}
								</td>
								<td>
                                                                        {*SalesPlatform.ru begin*}
                                					<select name="industry" class="select2" required="true" style="width:250px;" placeholder={vtranslate('LBL_CHOOSE_ONE','Install')}>
										<option value=""></option> 
										<option>{vtranslate('LBL_ACCOUNTING','Install')}</option> 
										<option>{vtranslate('LBL_ADVERTISING','Install')}</option>
										<option>{vtranslate('LBL_AGRICULTURE','Install')}</option>
										<option>{vtranslate('LBL_APPAREL_ACCESSORIES','Install')}</option>
										<option>{vtranslate('LBL_AUTOMOTIVE','Install')}</option>
										<option>{vtranslate('LBL_BANKING_FINANCIAL_SERVICES','Install')}</option>
										<option>{vtranslate('LBL_BIOTECHNOLOGY','Install')}</option>
										<option>{vtranslate('LBL_CALL_CENTERS','Install')}</option>
										<option>{vtranslate('LBL_CAREERS_EMPLOYMENT','Install')}</option>
										<option>{vtranslate('LBL_CHEMICAL','Install')}</option>
										<option>{vtranslate('LBL_COMPUTER_HARDWARE','Install')}</option>
										<option>{vtranslate('LBL_COMPUTER_SOFTWARE','Install')}</option>
										<option>{vtranslate('LBL_CONSULTING','Install')}</option>
										<option>{vtranslate('LBL_CONSTRUCTION','Install')}</option>
										<option>{vtranslate('LBL_EDUCATION','Install')}</option>
										<option>{vtranslate('LBL_ENERGY_SERVICES','Install')}</option>
										<option>{vtranslate('LBL_ENGINEERING','Install')}</option>
										<option>{vtranslate('LBL_ENTERTAINMENT','Install')}</option>
										<option>{vtranslate('LBL_FINANCIAL','Install')}</option>
										<option>{vtranslate('LBL_FOOD','Install')}</option>
										<option>{vtranslate('LBL_GOVERNMENT','Install')}</option>
										<option>{vtranslate('LBL_HEALTH_CARE','Install')}</option>
										<option>{vtranslate('LBL_INSURANCE','Install')}</option>
										<option>{vtranslate('LBL_LEGAL','Install')}</option>
										<option>{vtranslate('LBL_LOGISTICS','Install')}</option>
										<option>{vtranslate('LBL_MANUFACTURING','Install')}</option>
										<option>{vtranslate('LBL_MEDIA_PRODUCTION','Install')}</option>
										<option>{vtranslate('LBL_NON_PROFIT','Install')}</option>
										<option>{vtranslate('LBL_PHARMACEUTICAL','Install')}</option>
										<option>{vtranslate('LBL_REAL_ESTATE','Install')}</option>
										<option>{vtranslate('LBL_RENTAL','Install')}</option>
										<option>{vtranslate('LBL_RETAIL_WHOLESALE','Install')}</option>
										<option>{vtranslate('LBL_SECURITY','Install')}</option>
										<option>{vtranslate('LBL_SERVICE','Install')}</option>
										<option>{vtranslate('LBL_SPORTS','Install')}</option>
										<option>{vtranslate('LBL_TELECOMMUNICATIONS','Install')}</option>
										<option>{vtranslate('LBL_TRANSPORTATION','Install')}</option>
										<option>{vtranslate('LBL_TRAVEL_TOURISM','Install')}</option>
										<option>{vtranslate('LBL_UTILITIES','Install')}</option>
										<option>{vtranslate('LBL_OTHER','Install')}</option>
									</select>
                                                                        {*vtiger commented code
									<select name="industry" class="select2" required="true" style="width:250px;" placeholder="Choose one...">
										<option value=""></option> 
										<option>Accounting</option> 
										<option>Advertising</option>
										<option>Agriculture</option>
										<option>Apparel &amp; Accessories</option>
										<option>Automotive</option>
										<option>Banking &amp; Financial Services</option>
										<option>Biotechnology</option>
										<option>Call Centers</option>
										<option>Careers/Employment</option>
										<option>Chemical</option>
										<option>Computer Hardware</option>
										<option>Computer Software</option>
										<option>Consulting</option>
										<option>Construction</option>
										<option>Education</option>
										<option>Energy Services</option>
										<option>Engineering</option>
										<option>Entertainment</option>
										<option>Financial</option>
										<option>Food &amp; Food Service</option>
										<option>Government</option>
										<option>Health care</option>
										<option>Insurance</option>
										<option>Legal</option>
										<option>Logistics</option>
										<option>Manufacturing</option>
										<option>Media &amp; Production</option>
										<option>Non-profit</option>
										<option>Pharmaceutical</option>
										<option>Real Estate</option>
										<option>Rental</option>
										<option>Retail &amp; Wholesale</option>
										<option>Security</option>
										<option>Service</option>
										<option>Sports</option>
										<option>Telecommunications</option>
										<option>Transportation</option>
										<option>Travel &amp; Tourism</option>
										<option>Utilities</option>
										<option>Other</option>
									</select>
                                                                        *}
                                                                        {*SalesPlatform.ru end*}
								</td>
							</tr>
							<tr>
								<td colspan="2">
                                                                        {*SalesPlatform.ru begin*}
                                                                        {vtranslate('LBL_WE_COLLECT_INFORMATION','Install')}
                                                                        {*vtiger commented code
									We collect anonymous information (Country, OS) 
									to help us improve future versions of Vtiger. 
									Data about how CRM is used and where it is being used helps 
									us identify the areas in the product that need to be enhanced. 
									We use this data to improve your experience with Vtiger. 
									None of the data collected here can be linked back to an individual.
                                                                        *}
                                                                        {*SalesPlatform.ru end*}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row-fluid offset2">
				<div class="span8">
					<div class="button-container">
						<input type="button" class="btn btn-large btn-primary" value="{vtranslate('LBL_NEXT','Install')}" name="step7"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div id="progressIndicator" class="row-fluid main-container hide">
	<div class="inner-container">
		<div class="inner-container">
			<div class="row-fluid">
				<div class="span12 welcome-div alignCenter">
					<h3>{vtranslate('LBL_INSTALLATION_IN_PROGRESS','Install')}...</h3><br>
					<img src="{'install_loading.gif'|vimage_path}"/>
					<h6>{vtranslate('LBL_PLEASE_WAIT','Install')}.... </h6>
				</div>
			</div>
		</div>
	</div>
</div>
</div>