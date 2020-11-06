<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_GetBindingFields_Action extends Vtiger_Action_Controller {
    
    public function checkPermission(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
        $privileges = $currentUser->getPrivileges();
        if(!$privileges->hasModulePermission(getTabid('SPTips'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
	}
    
    public function process (Vtiger_Request $request) {
        $moduleName = $request->get('sourceModule');
        
        $response = new Vtiger_Response();
        $rulesList = [];
        $moduleRules = Settings_SPTips_Rule_Model::getAllForModule($moduleName);
        foreach($moduleRules as $ruleModel) {
            $rulesList[] = $this->getRuleForResponse($ruleModel);
        }
        
        $response->setResult($rulesList);
        $response->emit();
    }
    
    /**
     * 
     * @param Settings_SPTips_Rule_Model $ruleModel
     */
    private function getRuleForResponse($ruleModel) {
        return [
            'ruleId' => $ruleModel->getId(),
            'autocomplete' => $ruleModel->getTipFieldName()
        ];
    }
    
}