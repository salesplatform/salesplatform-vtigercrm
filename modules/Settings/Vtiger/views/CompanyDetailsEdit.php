<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_CompanyDetailsEdit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
        //SalesPlatform.ru begin
        $companyModel = Settings_Vtiger_CompanyDetails_Model::getCleanInstance();
        if($request->get('company') != null) {
            $companyModel = Settings_Vtiger_CompanyDetails_Model::getInstance(decode_html($request->get('company')));
        }
		//$moduleModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
        //SalesPlatform.ru end
        

		$viewer = $this->getViewer($request);
        //SalesPlatform.ru begin
		//$viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('COMPANY_MODEL', $companyModel);
        //SalesPlatform.ru end
		$viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
		$viewer->assign('ERROR_MESSAGE', $request->get('error'));

		$viewer->view('CompanyDetailsEdit.tpl', $qualifiedModuleName);//For Open Source
	}
		
	function getPageTitle(Vtiger_Request $request) {
		$qualifiedModuleName = $request->getModule(false);
		return vtranslate('LBL_CONFIG_EDITOR',$qualifiedModuleName);
	}
    
    
    //SalesPlatform.ru begin
    function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsScriptInstances = $this->checkAndConvertJsScripts(array(
			"modules.Settings.$moduleName.resources.CompanyDetailsEdit"
		));
        
		return array_merge(parent::getHeaderScripts($request), $jsScriptInstances);
	}
    //SalesPlatform.ru end
	
}