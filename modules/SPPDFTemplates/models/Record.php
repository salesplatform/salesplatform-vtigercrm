<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_Record_Model extends Vtiger_Record_Model {
    
    function getId() {
        return $this->get('templateid');
    }
    
    function setId($value) {
        $this->set('templateid', $value);
    }
    
    function getName() {
        return $this->get('name');
    }
    /**
     * Clear id and name of record
     */
    public function toDuplicate() {
        $this->set('name',"");
        $this->set('templateid',"");
    }

    public function getDetailViewUrl() {
        return "index.php?module=SPPDFTemplates&view=Detail&templateid=".$this->getId();
    }
    
    public function getEditViewUrl() {
        return "index.php?module=SPPDFTemplates&view=Edit&templateid=".$this->getId();
    }
    
    public function getDuplicateRecordUrl() {
        return $this->getEditViewUrl()."&isDuplicate=true";
    }
    
    /**
     * Return record model by it id.
     * @param type $id
     * @return \self
     */
    public static function getInstanceById($id) {
        $instance = new SPPDFTemplates_Record_Model();
        
        if(!isset($id) || $id == '') {
            return $instance;
        }
        
        $db = PearDatabase::getInstance();
        
        $query = 'SELECT * FROM sp_templates WHERE templateid=?';
        $params = array($id);
        
        $result = $db->pquery($query,$params);
        if($db->num_rows($result) > 0) {
            $row = $db->query_result_rowdata($result,0);
            $instance->setData($row);
        }
        return $instance;
    }
    
    public static function deleteById($id) {
        $db = PearDatabase::getInstance();
        $sql = "delete from sp_templates where templateid=?";
	$db->pquery($sql, array($id));
    }
    
     /**
     * Create or update new record
     */
    public function save() {
        $db = PearDatabase::getInstance();
        
        $query = "update sp_templates set name=?, module=?, template=?, header_size=?, footer_size=?, page_orientation=?, spcompany=? where templateid=?";
        if($this->getId() == NULL) {
           $this->setId($db->getUniqueID('sp_templates'));
           $query = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,spcompany,templateid) values (?,?,?,?,?,?,?,?)";
        } 
        
        $params = array($this->get('name'), $this->get('module'), $this->get('template'), $this->get('header_size'), 
            $this->get('footer_size'), $this->get('page_orientation'), $this->get('spcompany'), $this->getId());
        $db->pquery($query, $params);
    }
}