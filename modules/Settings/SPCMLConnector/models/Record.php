<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_Record_Model extends Settings_Vtiger_Record_Model {
    
    const statusesTable = "sp_cml_site_settings";
    
    private function getUniqueId() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPCMLConnector_Record_Model::statusesTable;
        $result = $db->query('select max(id) from '.$tableName);
        return $db->query_result($result, 'max(id)') + 1;
    }
    
    public function getId() {
        return $this->get('id');
    }
    
    public function getName() {
        return $this->get('id');
    }
    
    /**
     * Return record status in CRM
     */
    public function getCrmStatus() {
        return $this->get('key');
    }
    
    /**
     * Return record status in site.
     */
    public function getSiteStatus() {
        return $this->get('value');
    }
    
    
    /**
     * Return all SalesOrder statuses, avalible in CRM. 
     * Not sure that it will be in this model.
     * @return null
     */
    public function getCrmStatuses() {
        $db = PearDatabase::getInstance();
        $result = $db->query("select sostatus from vtiger_sostatus");
        
        $sostatuses = array();
        while( $record =  $db->fetchByAssoc($result)) {
            $sostatus = $record['sostatus'];
            array_push($sostatuses, $sostatus);
        }
        return $sostatuses;
    }
    
    /**
     * Return record model by it id.
     * @param type $id
     * @return \self
     */
    public static function getInstance($id) {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPCMLConnector_Record_Model::statusesTable;
        
        $query = 'SELECT * FROM '.$tableName.' WHERE id=?';
        $params = array($id);
        
        $instance = new Settings_SPCMLConnector_Record_Model();
        
        $result = $db->pquery($query,$params);
        if($db->num_rows($result) > 0) {
            $row = $db->query_result_rowdata($result,0);
            $instance->setData($row);
        }
        return $instance;
    }
    
    /**
     * JavaScript links on create and edit events
     * @return array
     */
    public function getRecordLinks() {
        $editLink = array(
            'linkurl' => "javascript:Settings_SPCMLConnector_List_Js.triggerEdit(event, '".$this->getId()."')",
            'linklabel' => 'LBL_EDIT',
            'linkicon' => 'icon-pencil'
        );
        $editLinkInstance = Vtiger_Link_Model::getInstanceFromValues($editLink);
        
        $deleteLink = array(
            'linkurl' => "javascript:Settings_SPCMLConnector_List_Js.triggerDelete(event,'".$this->getId()."')",
            'linklabel' => 'LBL_DELETE',
            'linkicon' => 'icon-trash'
        );
        $deleteLinkInstance = Vtiger_Link_Model::getInstanceFromValues($deleteLink);
        return array($editLinkInstance,$deleteLinkInstance);
    }
    
    /**
     * Saves setted parameters
     */
    public function save() {
        $db = PearDatabase::getInstance();
        $tableName = Settings_SPCMLConnector_Record_Model::statusesTable;
        $id = $this->getId();

        /* If edit - rewrite. Else create new. */
        if(empty($id)) {

            $id = $this->getUniqueId($tableName);
            $query = 'INSERT INTO '.$tableName.' VALUES(?,?,?,?)' ;
            $params = array($id,'statusParam', $this->getCrmStatus(), $this->getSiteStatus());
        } else {
            $query = 'UPDATE '.$tableName.' SET `key`=?, `value`=? WHERE `id`=?' ;
            $params = array($this->getCrmStatus(), $this->getSiteStatus(), $id);
        }
        $db->pquery($query,$params);
    }
    
    /**
     * Delete record by it id.
     * @param type $id
     */
    public function delete() {
        $tableName = Settings_SPCMLConnector_Record_Model::statusesTable;
        $query = 'DELETE FROM '.$tableName.' WHERE `id`=?' ;
        $params = array($this->getId());
        
        $db = PearDatabase::getInstance();
        $db->pquery($query, $params);
    }
    
}