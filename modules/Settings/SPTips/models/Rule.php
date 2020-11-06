<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_Rule_Model extends Settings_Vtiger_Record_Model {
    
    const RULES_TABLE_NAME = 'sp_tips_module_rules';
    
    private $dependentsFieldsForSave = [];
    
    public function getName() {
        return '';
    }
    
    public function getId() {
        return $this->get('rule_id');
    }
    
    public function getModuleName() {
        return $this->get('module');
    }
    
    public function getTipFieldName() {
        return $this->get('field');
    }
    
    public function getType() {
        return $this->get('type');
    }
    
    /**
     * 
     * @return Settings_SPTips_Provider_Model
     */
    public function getProvider() {
        return Settings_SPTips_Provider_Model::getInstanceById($this->get('provider_id'));
    }
    
    /**
     * 
     * @return Vtiger_Field_Model
     */
    public function getTipFieldModel() {
        $moduleModel = Vtiger_Module_Model::getInstance($this->getModuleName());
        if($moduleModel) {
            return $moduleModel->getField($this->getTipFieldName());
        }
        
        return null;
    }
    
    /**
     * 
     * @param string $moduleName
     */
    public static function getAllForModule($moduleName) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery(
            "SELECT * FROM " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . " WHERE module=?",
            [$moduleName]
        );
        
        $rulesList = [];
        while($resultRow = $db->fetchByAssoc($result)) {
            $rulesList[] = new Settings_SPTips_Rule_Model($resultRow);
        }
        
        return $rulesList;
    }
    
    /**
     * 
     * @param type $ruleId
     * @return \Settings_SPTips_Rule_Model
     */
    public static function getInstanceById($ruleId) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . " WHERE rule_id=? LIMIT 1", [$ruleId]);
        $resultRow = $db->fetchByAssoc($result);
        $instance = null;
        if($resultRow != null) {
            $instance = new Settings_SPTips_Rule_Model($resultRow);
        }
        
        return $instance;
    }
    
    /**
     * 
     * @param type $providerId
     * @return \Settings_SPTips_Rule_Model[]
     */
    public static function getAllProviderRules($providerId) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . " WHERE provider_id=?", [$providerId]);
        $providers = [];
        while($resultRow = $db->fetchByAssoc($result)) {
            $providers[] = new Settings_SPTips_Rule_Model($resultRow);
        }
        
        return $providers;
    }
    
    /**
     * 
     * @return \Vtiger_Base_Model[]
     */
    public function getDependentFields() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM " . Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME . " WHERE rule_id=?", [$this->getId()]);
        
        $dependentFields = [];
        while($resultRow = $db->fetchByAssoc($result)) {
            $dependentFields[] = new Settings_SPTips_RuleDependentField_Model($resultRow);
        }
        
        return $dependentFields;
    }
    
    public function setDependentFieldsForSave($dependentFieldsList) {
        $this->dependentsFieldsForSave = $dependentFieldsList;
    }
    
    public function getAvailablePicklistModules() {
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT distinct vtiger_field.tabid, vtiger_tab.tablabel, vtiger_tab.name as tabname FROM vtiger_field ';
        $sql .= 'INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_field.tabid ';
        $sql .= 'AND vtiger_field.tabid !=' . getTabid('Emails');
        $sql .= ' AND vtiger_field.tabid !=' . getTabid('ModComments');
        $sql .= ' AND vtiger_field.tabid !=' . getTabid('PBXManager');
        $sql .= ' AND vtiger_tab.isentitytype = 1 ';
        $sql .= 'AND vtiger_field.displaytype = 1 ';
        $sql .= 'AND vtiger_field.presence in ("0","2") ';
        $sql .= 'AND vtiger_field.block != "NULL" ';
        $sql .= 'AND vtiger_tab.presence != 1 ';
        $sql .= 'GROUP BY vtiger_field.tabid HAVING count(*) > 1';
        
        $result = $adb->pquery($sql, array());
        while($row = $adb->fetch_array($result)) {
            $modules[$row['tablabel']] = $row['tabname'];
        }
        ksort($modules);
        
        $modulesModelsList = array();
        foreach($modules as $moduleLabel => $moduleName) {
            $instance = new Vtiger_Module_Model();
            $instance->name = $moduleName;
            $instance->label = $moduleLabel;
            $modulesModelsList[] = $instance;
        }
        return $modulesModelsList;
    }
    
    public function getAvailableModuleFields($moduleName) {
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT fieldname, fieldlabel FROM vtiger_field WHERE ';
        $sql .= '(tabid = (SELECT tabid FROM vtiger_tab WHERE name = ?)) ';
        $sql .= 'AND vtiger_field.uitype IN (1,2,7, 9,11,13,17,19,20,21,22,24,55,71,255) ';
        $sql .= 'AND vtiger_field.fieldname NOT IN ("tags", "source", "one_s_id") ';
        $sql .= 'AND vtiger_field.displaytype = 1 AND vtiger_field.readonly = 1 AND vtiger_field.presence != 1';
        $result = $adb->pquery($sql, array($moduleName));
        while($row = $adb->fetch_array($result)) {
            $fields[$row['fieldname']] = $row['fieldlabel'];
        }
        return $fields;
    }
    
    public static function getCleanInstance() {
        return new Settings_SPTips_Rule_Model();
    }
    
    public function save() {
        $db = PearDatabase::getInstance();
        if($this->getId() != null) {
            $db->pquery(
                "UPDATE " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . " SET module=?, field=?, type=?, provider_id=? WHERE rule_id=?", 
                [$this->getModuleName(), $this->getTipFieldName(), $this->getType(), $this->get('provider_id'), $this->getId()]
            );
        } else {
            $this->set('rule_id', $db->getUniqueID(Settings_SPTips_Rule_Model::RULES_TABLE_NAME));
            $db->pquery(
                "INSERT INTO " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . "(rule_id,module,field,type,provider_id) VALUES(?,?,?,?,?)",
                [$this->getId(), $this->getModuleName(), $this->getTipFieldName(), $this->getType(), $this->get('provider_id')]
            );
        }
        
        $db->pquery("DELETE FROM " . Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME . " WHERE rule_id=?", [$this->getId()]);
        foreach($this->dependentsFieldsForSave as $ruleDependentField) {
            $ruleDependentField->set('rule_id', $this->getId());
            $ruleDependentField->save();
        }
    }
    
    public function delete() {
        if($this->getId() != null) {
            $db = PearDatabase::getInstance();
            $db->pquery("DELETE FROM " . Settings_SPTips_Rule_Model::RULES_TABLE_NAME . " WHERE rule_id=?",[$this->getId()]);
            
            foreach($this->getDependentFields() as $dependentFieldModel) {
                $dependentFieldModel->delete();
            }
        }
    }
    
}