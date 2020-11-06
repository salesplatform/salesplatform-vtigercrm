<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_Edit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
        $moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $modulesList = Settings_SPDynamicBlocks_Module_Model::getPicklistSupportedModules();                
        $recordModel = Settings_SPDynamicBlocks_Record_Model::getInstance($recordId);      
        $picklists = $recordModel->getPicklists();        
        
        $values = array();
        $blocks = array();
        if ($recordId) {
            $selectedModule = $recordModel->get('module_name');
            $selectedField = $recordModel->get('field_name');
            $values =  Settings_SPDynamicBlocks_Record_Model::getAllValuesForPicklist(
                    $selectedModule, 
                    $selectedField
                    );
            $moduleInstance = Vtiger_Module_Model::getInstance($selectedModule);
            $fieldModel = Vtiger_Field_Model::getInstance($selectedField, $moduleInstance);
            $blockModel = Vtiger_Block_Model::getInstance($fieldModel->getBlockId(), $moduleInstance);
            $blocks = Settings_SPDynamicBlocks_Record_Model::getAllBlocksForModule($selectedModule, array($blockModel->get('label')));
        }
        
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_USER_MODEL', $currentUserModel);
		$viewer->assign('MODULE', $moduleName);
        $viewer->assign('MODULES_LIST', $modulesList);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('RECORD_ID', $recordId);
        $viewer->assign('PICKLISTS', $picklists);
        $viewer->assign('VALUES', $values);
        $viewer->assign('BLOCKS', $blocks);
        $viewer->assign('PICKLIST_MAPPING', $this->getPicklistMapping($modulesList));
		$viewer->view('Edit.tpl', $qualifiedModuleName);
	}      
    
    private function getPicklistMapping($modulesList) {
        $picklistMapping = array();
        foreach ($modulesList as $moduleModel) {
            $moduleModel = Vtiger_Module_Model::getInstance($moduleModel->getName());
            $modulePicklistDep = Settings_PickListDependency_Record_Model::getInstance($moduleModel->getId(), null, null);
            $pickLists = $modulePicklistDep->getAllPickListFields();
            $picklistMapping[$moduleModel->getName()] = array_keys($pickLists);
        }
        return $picklistMapping;
    }
}