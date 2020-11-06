<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_ListView_Model extends Settings_Vtiger_ListView_Model {
    
    public function getListViewEntries($pagingModel) {
        $listViewRecords = parent::getListViewEntries($pagingModel);
        foreach ($listViewRecords as $recordId => $recordInfo) {
            $recordModel = Settings_SPDynamicBlocks_Record_Model::getInstance($recordId);
            $moduleName = $recordModel->get('module_name');
            $selectedField = $recordModel->getSelectedField();
            $recordInfo->set('module_name', vtranslate($moduleName, $moduleName));
            $recordInfo->set(
                    'field_name', 
                    vtranslate($selectedField->get('label'), $moduleName));
            $recordInfo->set('values', implode(', ', $recordModel->getTranslatedArray($recordModel->getValues(),$moduleName)));
            $recordInfo->set('blocks', implode(', ', $recordModel->getTranslatedArray($recordModel->getBlocks(),$moduleName)));
        }
        return $listViewRecords;
    }        

}