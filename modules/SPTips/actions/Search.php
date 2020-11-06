<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_Search_Action extends Vtiger_Action_Controller {
    
    public function checkPermission(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
        $privileges = $currentUser->getPrivileges();
        if(!$privileges->hasModulePermission(getTabid('SPTips'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
	}
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $ruleModel = Settings_SPTips_Rule_Model::getInstanceById($request->get('ruleId'));
        if($ruleModel == null) {
            $response->setError(vtranslate('LBL_INVALID_RULE', $request->getModule()));
            $response->emit();
            return;
        }
        
        $provider = $ruleModel->getProvider();
        if ($provider == null) {
            $response->setError(vtranslate('LBL_NO_PROVIDER', $request->getModule()));
            $response->emit();
            return;
        }
        
        $realizationModel = $provider->getConcreteRealization();
        
        switch($ruleModel->getType()) {
            
            case SPTips_SearchType_Model::ADDRESS:
                $response->setResult(
                    $realizationModel->searchAddress(
                        $request->get('search'), 
                        $ruleModel->getDependentFields()
                    )
                );
                break;
            
            case SPTips_SearchType_Model::ORGANIZATION:
                $response->setResult(
                    $realizationModel->searchOrganization(
                        $request->get('search'), 
                        $ruleModel->getDependentFields()
                    )
                );
                break;
            
            default:
                $response->setError(vtranslate('LBL_INVALID_TYPE'));
                break;
        }
        
        
        $response->emit();
    }
}