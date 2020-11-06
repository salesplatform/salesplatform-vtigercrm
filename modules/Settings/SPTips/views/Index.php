<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 * ********************************************************************************** */

class Settings_SPTips_Index_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        $providersList = Settings_SPTips_Provider_Model::getAll();
        $selectedProider = current($providersList);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('EXISTING_PROVIDERS', Settings_SPTips_Provider_Model::getAll());
        $viewer->assign('EXISTING_RULES', Settings_SPTips_Rule_Model::getAllProviderRules($selectedProider->getId()));
        $viewer->assign('SELECTED_PROVIDER', $selectedProider);

        $viewer->view('Index.tpl', $qualifiedModuleName);
    }

}
