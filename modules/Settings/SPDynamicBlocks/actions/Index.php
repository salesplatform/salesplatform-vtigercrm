<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_Index_Action extends Settings_Vtiger_Basic_Action {
    
    public function checkPermission(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $privileges = $currentUser->getPrivileges();
        if(!$privileges->hasModulePermission(getTabid('SPDynamicBlocks'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
    }
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getPicklists');
        $this->exposeMethod('getValues');
        $this->exposeMethod('getBlocks');
        $this->exposeMethod('getBlocksToHide');
        $this->exposeMethod('deleteConfiguration');
        $this->exposeMethod('getBlocksToHideOnLoadDetail');
    }
   
    public function getPicklists(Vtiger_Request $request) {
        $configurationModel = Settings_SPDynamicBlocks_Record_Model::getInstance();
        $configurationModel->setSelectedModuleName($request->get('module_name'));
        $picklists = $configurationModel->getPicklists();
        
        $response = new Vtiger_Response();        
        $response->setResult(array('success' => true, 'fieldPicklists'=> $picklists));
        $response->emit();
    }
    
    public function getValues(Vtiger_Request $request) {   
        $selectedModule = $request->get('module_name');
        $selectedField = $request->get('field_name');
        $values = Settings_SPDynamicBlocks_Record_Model::getAllValuesForPicklist($selectedModule, $selectedField);    
        $response = new Vtiger_Response();        
        $response->setResult(array('success' => true, 'values'=> $values));
        $response->emit();
    }
    
    public function getBlocks (Vtiger_Request $request) {        
        $selectedModule = $request->get('module_name');
        $fieldName = $request->get('field_name');
        $moduleInstance = Vtiger_Module_Model::getInstance($selectedModule);
        $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
        $blockModel = Vtiger_Block_Model::getInstance($fieldModel->getBlockId(), $moduleInstance);
        $blocksLabels = Settings_SPDynamicBlocks_Record_Model::getAllBlocksForModule($selectedModule, array($blockModel->get('label')));        
        $response = new Vtiger_Response();        
        $response->setResult(array('success' => true, 'values'=> $blocksLabels));
        $response->emit();
    }
    
    public function getBlocksToHide(Vtiger_Request $request) {        
        $fieldsData = (array) json_decode(urldecode($request->get('formFields')));        
        $blocks = Settings_SPDynamicBlocks_Record_Model::getBlocksToHide($fieldsData);
        $response = new Vtiger_Response();        
        $response->setResult(array('success' => true, 'blocks'=> $blocks));
        $response->emit();
    }
    
    public function deleteConfiguration(Vtiger_Request $request) {
        $recordId = $request->get('record');        
        $recordModel = Settings_SPDynamicBlocks_Record_Model::getInstance($recordId);
        $recordModel->deleteRecord();
        $response = new Vtiger_Response();        
        $response->setResult(array('success' => true));
        $response->emit();
    }
    
    public function getBlocksToHideOnLoadDetail(Vtiger_Request $request) {
        $blocksToHide = array();       
        $recordId = $request->get('record');        
        if (isRecordExists($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $fieldsData = $recordModel->getData();
            $fieldsData['module'] = $recordModel->getModuleName();
            if ($fieldsData['module'] == 'Calendar') {
                $fieldsData['module'] = $recordModel->getType();
            }
            $blocksToHide = Settings_SPDynamicBlocks_Record_Model::getBlocksToHide($fieldsData);            
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true, 'blocks' => $blocksToHide));
        $response->emit();
    }

}