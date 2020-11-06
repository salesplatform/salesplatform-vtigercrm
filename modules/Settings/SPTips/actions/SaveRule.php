<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SaveRule_Action extends Settings_Vtiger_Index_Action {
    
    const rulesTable = 'sp_tips_module_rules';
    const dependentFieldsTable = 'sp_tips_dependent_fields';
    
    public function process (Vtiger_Request $request) {
        $record = $request->get('record');
        $ruleModel = Settings_SPTips_Rule_Model::getInstanceById($record);
        if($ruleModel == null) {
            $ruleModel = Settings_SPTips_Rule_Model::getCleanInstance();
        }
        $providerModel = Settings_SPTips_Provider_Model::getInstanceById($request->get('providerId'));
        if($providerModel == null) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        
        $ruleModel->set('module', $request->get('sourceModule'));
        $ruleModel->set('field', $request->get('sourceField'));
        $ruleModel->set('provider_id', $providerModel->getId());
        $ruleModel->set('type', $request->get('type'));
        
        /* Prepare auto-fill fields */
        $dependentFields = $request->get('dependentFields');
        $providerFields = $request->get('providerFields');
        $saveMapping = [];
        if(!empty($dependentFields)) {
            foreach($dependentFields as $position => $vtigerFieldName) {
                $providerFieldName = $providerFields[$position];
                $ruleDependentFieldModel = new Settings_SPTips_RuleDependentField_Model();
                $ruleDependentFieldModel->set('vtiger_fieldname', $vtigerFieldName);
                $ruleDependentFieldModel->set('provider_fieldname', $providerFieldName);
                $saveMapping[] = $ruleDependentFieldModel;
            }
        }
        
        $ruleModel->setDependentFieldsForSave($saveMapping);
        $ruleModel->save();
        
        header("Location: index.php?module=SPTips&view=Index&parent=Settings");
    }
}