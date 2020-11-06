<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_Record_Model extends Settings_Vtiger_Record_Model{
    
    private $moduleModel = null;
    private static $restrictedBlocks = array('LBL_ITEM_DETAILS', 'LBL_RELATED_PRODUCTS');
    
    function  __construct($values=array()) {
		parent::__construct($values);
        $this->setModule();
	}
    
    public function getId() {
        return $this->get('sp_blocks_configuration_id');
    }
    
    public function setId($value) {
        $this->set('sp_blocks_configuration_id', $value);
    }
    
    public function getName() {
        return $this->get('module_name') . " | " . $this->get('field_name');
    }
    
    public static function getInstance($id = null) {
        $instance = new self();
        if (empty($id)) {
            return $instance;
        }
        
        $db = PearDatabase::getInstance();        
        $query = "SELECT * FROM vtiger_sp_blocks_configuration WHERE sp_blocks_configuration_id=?";
        
        $params = array($id);
        $result = $db->pquery($query,$params);
        if($result && $db->num_rows($result) > 0) {            
            $row = $db->query_result_rowdata($result,0);
            $instance->setData($row);
            $instance->set('values', self::getConfigurationValues($id));
            $instance->set('blocks', self::getConfigurationBlocks($id));
        }
        $instance->setId($id);        
        return $instance;
    }
    
    public static function getConfigurationValues($id) {
        $db = PearDatabase::getInstance();
        $values = array();
        $query = "SELECT field_value FROM vtiger_sp_blocks_configuration2values WHERE sp_blocks_configuration_id=?";
        $params = array($id);
        $result = $db->pquery($query, $params);
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $values[] = $resRow['field_value'];
            }
        }
        return $values;
    }
    
    public static function getConfigurationBlocks($id) {
        $db = PearDatabase::getInstance();
        $blocks = array();
        $query = "SELECT block_name FROM vtiger_sp_blocks_configuration2blocks WHERE sp_blocks_configuration_id=?";
        $params = array($id);
        $result = $db->pquery($query, $params);
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $blocks[] = $resRow['block_name'];
            }
        }
        return $blocks;
    }    
    
    public static function getAllValuesForPicklist($moduleName, $fieldName) {
        $fieldModel = Vtiger_Field_Model::getInstance($fieldName, Vtiger_Module_Model::getInstance($moduleName));
        $values = array();
        if (!empty($fieldModel)) {
            $values = $fieldModel->getPicklistValues();
        }
        return $values;
    }
    
    public static function getAllBlocksForModule($moduleName, $restrictedBlocks = array()) {     
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $blocksInfo = $moduleModel->getBlocks();
        $blocksLabels = array();
        $restrictedBlocks = array_merge(self::$restrictedBlocks, $restrictedBlocks);
        foreach (array_keys($blocksInfo) as $blockLabel) {
            if (!empty($blockLabel) && !in_array($blockLabel, $restrictedBlocks)) {
                $blocksLabels[$blockLabel] = vtranslate($blockLabel, $moduleName);
            }
        }
        
        if ($moduleName == 'Events') {
            $blocksLabels['LBL_INVITE_USER_BLOCK'] = vtranslate('LBL_INVITE_USER_BLOCK', $moduleName);
        }
        
        return $blocksLabels;
    }
    
    public static function getBlocksToHide($formFields) {
        $db = PearDatabase::getInstance();
        $editModule = $formFields['module'];
        $query = "SELECT vtiger_sp_blocks_configuration.module_name, vtiger_sp_blocks_configuration2blocks.block_name FROM vtiger_sp_blocks_configuration 
                    INNER JOIN vtiger_sp_blocks_configuration2values ON vtiger_sp_blocks_configuration.sp_blocks_configuration_id=vtiger_sp_blocks_configuration2values.sp_blocks_configuration_id
                    INNER JOIN vtiger_sp_blocks_configuration2blocks ON vtiger_sp_blocks_configuration.sp_blocks_configuration_id=vtiger_sp_blocks_configuration2blocks.sp_blocks_configuration_id 
                    WHERE vtiger_sp_blocks_configuration.module_name=? AND (";
        $params[] = $editModule;
        $queryArr = array();
        

        foreach ($formFields as $fieldName => $fieldValue) {
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, Vtiger_Module_Model::getInstance($editModule));
            if (empty($fieldModel)) {
                continue;
            }
            if ($fieldModel->getFieldDataType() == 'multipicklist') {
                $fieldValue = $formFields[$fieldName."[]"];
                foreach ($fieldValue as $singleMultipicklistValue) {
                    $queryArr[] = " (vtiger_sp_blocks_configuration.field_name=? AND vtiger_sp_blocks_configuration2values.field_value=?) ";
                    $params[] = $fieldName;
                    $params[] = $singleMultipicklistValue;
                }
                
            } else if ($fieldModel->getFieldDataType() == 'picklist') {
                $queryArr[] = " (vtiger_sp_blocks_configuration.field_name=? AND vtiger_sp_blocks_configuration2values.field_value=?) ";
                $params[] = $fieldName;
                $params[] = $fieldValue;
            }
        }
        
        $query .= implode(" OR ", $queryArr) . ")";
        
        $blocksToHide = array();
        $result = $db->pquery($query, $params);
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $moduleName = $resRow['module_name'];
                $blocksToHide[vtranslate($resRow['block_name'], $moduleName)] = vtranslate($resRow['block_name'], $moduleName);
                $blocksToHide[$resRow['block_name']] = $resRow['block_name'];
            }
        }
        return $blocksToHide;
    }    

    public function getPicklists() {       
        $picklists = array();
        $moduleName = $this->getSelectedModule();
        if (!empty($moduleName)) {
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $modulePicklistDep = Settings_PickListDependency_Record_Model::getInstance($moduleModel->getId(), null, null);
            $picklists = $modulePicklistDep->getAllPickListFields();     
        }
        
        $picklists = $this->filterExtraPicklists($picklists);
        
        return $this->getTranslatedArray($picklists, $moduleName);
    }
    
    public function save() {
        $db = PearDatabase::getInstance();        
        $recordId = $this->getId();        
        if (empty($recordId)) {
            $this->setId($db->getUniqueID($this->moduleModel->getBaseTable()));            
        } else {
            $this->clearTables();
        }           
        $this->saveBaseModel();
        $this->saveValues();
        $this->saveBlocks();
        
    }
    
    public function getSelectedModule() {
        return $this->get('module_name');
    }
    
    public function setSelectedModuleName($moduleName) {
        $this->set('module_name', $moduleName);
    }
    
    public function getSelectedField() {
        return Vtiger_Field_Model::getInstance(
                $this->get('field_name'), 
                Vtiger_Module_Model::getInstance($this->get('module_name')));        
    }
    
    public function getBlocks() {
        return $this->get('blocks');
    }
    
    public function getValues() {
        return $this->get('values');
    }
    
    public function getTranslatedArray($array, $moduleName) {
        array_walk($array, function(&$label) use ($moduleName) {
            $label = vtranslate($label, $moduleName);
        });
        return $array;
    }
    
    public function getDetailViewUrl() {
        return "index.php?parent=Settings&module=SPDynamicBlocks&view=Edit&record=" . $this->getId();
    }
    
    public function getRecordLinks() {

		$links = array();

		$recordLinks[] = array(
				'linklabel' => 'LBL_EDIT_RECORD',
				'linkurl' => $this->getDetailViewUrl(),
				'linkicon' => 'icon-pencil'
        );
        
        $recordLinks[] = array(
            'linkurl' => "javascript:Settings_SPDynamicBlocks_List_Js.triggerDelete('".$this->getId()."')",
            'linklabel' => 'LBL_DELETE',
            'linkicon' => 'icon-trash'
        );
		
		foreach($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}
    
    public function deleteRecord() {
        $this->clearTables();
    }
    
    private function filterExtraPicklists($picklists) {
        $moduleName = $this->get('module_name');
        if ($moduleName == 'Events') {
            unset($picklists['recurringtype']);
        }
        return $picklists;
    }
    
    private function clearTables() {
        $recordId = $this->getId();
        $db = PearDatabase::getInstance();
        $db->pquery("DELETE FROM {$this->moduleModel->getBaseTable()} WHERE {$this->moduleModel->getBaseIndex()}=?", array($recordId));
        $db->pquery("DELETE FROM {$this->moduleModel->getBlocksTable()} WHERE {$this->moduleModel->getBaseIndex()}=?", array($recordId));
        $db->pquery("DELETE FROM {$this->moduleModel->getValuesTable()} WHERE {$this->moduleModel->getBaseIndex()}=?", array($recordId));
    }
    
    private function saveBaseModel() {
        $db = PearDatabase::getInstance();
        $db->pquery("INSERT INTO {$this->moduleModel->getBaseTable()} VALUES(?,?,?)", 
                array($this->getId(), $this->getSelectedModule(), $this->getSelectedField()->getName()));
    }
    
    private function saveBlocks() {
        $db = PearDatabase::getInstance();
        foreach($this->getBlocks() as $blockLabel) {
            $db->pquery("INSERT INTO {$this->moduleModel->getBlocksTable()} VALUES(?,?)", array($this->getId(), $blockLabel));
        }
    }
    
    private function saveValues() {
        $db = PearDatabase::getInstance();
        foreach($this->getValues() as $value) {
            $db->pquery("INSERT INTO {$this->moduleModel->getValuesTable()} VALUES(?,?)", array($this->getId(), $value));
        }
    }
    
    private function setModule() {
        $this->moduleModel = Settings_SPDynamicBlocks_Module_Model::getInstance('Settings:SPDynamicBlocks');
    }

}