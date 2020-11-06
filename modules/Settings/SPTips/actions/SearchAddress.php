<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SearchAddress_Action extends Settings_Vtiger_Index_Action {
    
    public function process (Vtiger_Request $request) {
        $response = new Vtiger_Response();
        
        $currentModule = $request->get('currentModule');
        if (isset($currentModule)) {
            $currentProvider = SPTips_CurrentProvider_Model::getProviderInstance();
            if (!$currentProvider) {
                $response->setError('Can\'t load provider');
                $response->emit();
                return;
            }
            
            $searchParam = $request->get('searchParam');
            $providerId = Settings_SPTips_Provider_Model::getProviderIdByName($currentProvider->getName());
            $ruleModel = Settings_SPTips_Rule_Model::getInstanceByNameAndProvider($currentModule, $providerId);
            $targetFields = $ruleModel->get('targetFields');
            $currentVtigerField = $request->get('currentField');
            // add source field to array, which will be used for searching
            $targetFields[$currentVtigerField] = $currentVtigerField;
            $providerFields = Settings_SPTips_ListRules_View::getProviderFieldsForSelectedRule($ruleModel->get('ruleId'));
            $providerFields[$request->get('currentField')] = Settings_SPTips_Rule_Model::getProviderFieldForSourceField($currentModule, $providerId);
            
            $addressAggregator = new SPTips_AddressAggregator_Model($currentProvider, $searchParam, $targetFields, $providerFields);
            $resultArray = $addressAggregator->searchAddress();
            
            $response->setResult($resultArray);
        }
        else {
            $response->setError('No param with current module name');
        }
        
        $response->emit();
    }
}