<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_Save_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {        
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordModel->save();        
		
		header("Location: " . Settings_SPDynamicBlocks_Module_Model::getlistViewURL());
        
    }
    
    private function getRecordModelFromRequest(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $selectedModule = $request->get('module_name');
        $field = $request->get('field_name');
        $values = $request->get('values');
        $blocks = $request->get('blocks');
        $recordModel = Settings_SPDynamicBlocks_Record_Model::getInstance($recordId);
        $recordModel->set('module_name', $selectedModule);
        $recordModel->set('field_name', $field);
        $recordModel->set('values', $values);
        $recordModel->set('blocks', $blocks);
        return $recordModel;
    }
    
}