<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_RuleDependentField_Model extends Settings_Vtiger_Record_Model {
    
    const DEPENDENT_FIELDS_TABLE_NAME = 'sp_tips_dependent_fields';
    
    public function getId() {
        return $this->get('field_id');
    }

    public function getName() {
        return "";
    }
    
    public function getModuleName() {
        $ruleModel = $this->getRule();
        if($ruleModel != null) {
            return $ruleModel->getModuleName();
        }
        
        return null;
    }
    
    public function getProviderFieldName() {
        return $this->get('provider_fieldname');
    }
    
    public function getVtigerFieldName() {
        return $this->get('vtiger_fieldname');
    }

    public function getVtigerField() {
        $ruleModel = $this->getRule();
        if($ruleModel != null) {
            $moduleModel = Vtiger_Module_Model::getInstance($ruleModel->getModuleName());
            if($moduleModel) {
                return $moduleModel->getField($this->getVtigerFieldName());
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @return Settings_SPTips_Rule_Model
     */
    public function getRule() {
        return Settings_SPTips_Rule_Model::getInstanceById($this->get('rule_id'));
    }
    
    
    public function save() {
        $db = PearDatabase::getInstance();
        if($this->getId() != null) {
            $db->pquery(
                "UPDATE " . Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME . 
                " SET vtiger_fieldname=?, provider_fieldname=?, rule_id=? WHERE field_id=?", 
                [$this->getVtigerFieldName(), $this->getProviderFieldName(), $this->get('rule_id'), $this->getId()]
            );
        } else {
           $this->set('field_id', $db->getUniqueID(Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME));
           $db->pquery(
               "INSERT INTO " . Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME . 
               " VALUES(?,?,?,?)",
               [$this->getId(), $this->getVtigerFieldName(), $this->getProviderFieldName(), $this->get('rule_id')]
            );
        }
    }
    
    public function delete() {
        if($this->getId() != null) {
            $db = PearDatabase::getInstance();
            $db->pquery("DELETE FROM " . Settings_SPTips_RuleDependentField_Model::DEPENDENT_FIELDS_TABLE_NAME . " WHERE field_id=?", [$this->getId()]);
        }
    }
}
