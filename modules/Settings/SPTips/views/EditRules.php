<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 * ********************************************************************************** */

class Settings_SPTips_EditRules_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        $availableModules = Settings_SPTips_Rule_Model::getAvailablePicklistModules();
        $record = $request->get('record');
        $providerId = $request->get('providerId');
        $selectedModule = $request->get('sourceModule');
        $type = $request->get('type');
        $skipRecordDependentFields = false;
        
        
        /* Check provider */
        $viewer = $this->getViewer($request);
        $provider = Settings_SPTips_Provider_Model::getInstanceById($providerId);
        if ($provider == null) {
            $viewer->view('Error.tpl', $qualifiedModuleName);
            return;
        }
        $providerImplementation = $provider->getConcreteRealization();
        $tipTypes = $providerImplementation->getSupportedSearchTypes();
        
        
        /* Prepare for display */
        if (!empty($record)) {
            $recordModel = Settings_SPTips_Rule_Model::getInstanceById($record);
        } else {
            $recordModel = Settings_SPTips_Rule_Model::getCleanInstance();
            $recordModel->set('provider_id', $providerId);
            $firstModule = current($availableModules);
            $recordModel->set('module', $firstModule->name);
            $recordModel->set('type', current($tipTypes));
        }
        
        if(!empty($selectedModule) || !empty($type)) {
            $recordModel->set('field', null);
            $skipRecordDependentFields = true;
        }
        if(!empty($type)) {
            $recordModel->set('type', $type);
        }
        if(!empty($selectedModule)) {
            $recordModel->set('module', $selectedModule);
        }
        
        
        
        /* Prepare display */
        $availableFields = Settings_SPTips_Rule_Model::getAvailableModuleFields($recordModel->getModuleName());
        $viewer->assign('PROVIDER_PICKLIST_FIELDS', $providerImplementation->getProviderFields($recordModel->getType()));
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('AVAILABLE_MODULES', $availableModules);
        $viewer->assign('SUPPORTED_TYPES', $tipTypes);
        $viewer->assign('PICKLIST_FIELDS', $availableFields);
        $viewer->assign('PROVIDER', $provider);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('SKIP_DEPENDENT', $skipRecordDependentFields);
        $viewer->view('EditRules.tpl', $qualifiedModuleName);
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsScriptInstances = $this->checkAndConvertJsScripts(["modules.Settings.$moduleName.resources.EditRules"]);
            return array_merge(parent::getHeaderScripts($request), $jsScriptInstances);
    }

}
