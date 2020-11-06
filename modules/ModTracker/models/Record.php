<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModTracker_Record_Model extends Vtiger_Record_Model {

	const UPDATE = 0;
	const DELETE = 1;
	const CREATE = 2;
	const RESTORE = 3;
	const LINK = 4;
	const UNLINK = 5;

	/**
	 * Function to get the history of updates on a record
	 * @param <type> $record - Record model
	 * @param <type> $limit - number of latest changes that need to retrieved
	 * @return <array> - list of  ModTracker_Record_Model
	 */
	public static function getUpdates($parentRecordId, $pagingModel,$moduleName) {
		if($moduleName == 'Calendar') {
			if(getActivityType($parentRecordId) != 'Task') {
				$moduleName = 'Events';
			}
		}
		$db = PearDatabase::getInstance();
		$recordInstances = array();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$listQuery = "SELECT * FROM vtiger_modtracker_basic WHERE crmid = ? AND module = ? ".
						" ORDER BY changedon DESC LIMIT $startIndex, $pageLimit";

		$result = $db->pquery($listQuery, array($parentRecordId, $moduleName));
		$rows = $db->num_rows($result);

		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstance = new self();
			$recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
			$recordInstances[] = $recordInstance;
		}
		return $recordInstances;
	}

	function setParent($id, $moduleName) {
		if(!Vtiger_Util_Helper::checkRecordExistance($id)) {
			$this->parent = Vtiger_Record_Model::getInstanceById($id, $moduleName);
		} else {
			$this->parent = Vtiger_Record_Model::getCleanInstance($moduleName);
			$this->parent->id = $id;
			$this->parent->setId($id);
		}
	}

	function getParent() {
		return $this->parent;
	}

	function checkStatus($callerStatus) {
		$status = $this->get('status');
		if ($status == $callerStatus) {
			return true;
		}
		return false;
	}

	function isCreate() {
		return $this->checkStatus(self::CREATE);
	}

	function isUpdate() {
		return $this->checkStatus(self::UPDATE);
	}

	function isDelete() {
		return $this->checkStatus(self::DELETE);
	}

	function isRestore() {
		return $this->checkStatus(self::RESTORE);
	}

	function isRelationLink() {
		return $this->checkStatus(self::LINK);
	}

	function isRelationUnLink() {
		return $this->checkStatus(self::UNLINK);
	}

	function getModifiedBy() {
		$changeUserId = $this->get('whodid');
		return Users_Record_Model::getInstanceById($changeUserId, 'Users');
	}

	function getActivityTime() {
		return $this->get('changedon');
	}

	function getFieldInstances() {
		$id = $this->get('id');
		$db = PearDatabase::getInstance();

		$fieldInstances = array();
		if($this->isCreate() || $this->isUpdate()) {
			$result = $db->pquery('SELECT * FROM vtiger_modtracker_detail WHERE id = ?', array($id));
			$rows = $db->num_rows($result);
			for($i=0; $i<$rows; $i++) {
				$data = $db->query_result_rowdata($result, $i);
				$row = array_map('decode_html', $data);

				if($row['fieldname'] == 'record_id' || $row['fieldname'] == 'record_module') continue;

				$fieldModel = Vtiger_Field_Model::getInstance($row['fieldname'], $this->getParent()->getModule());
	                //SalesPlatform.ru begin History of products changes
	                if(!$fieldModel && in_array($this->getParent()->getModuleName(), getInventoryModules()) &&  
	                    strpos($row['fieldname'], 'productid') === 0) { 
	                     
	                    $fieldModel = new InventoryModTracker_Field_Model($row, $this->getParent()->getModule()); 
	                     
	                    $fieldInstance = new ExtendentModTracker_Field_Model(); 
	                    $fieldInstance->setData($row)->setParent($this)->setFieldInstance($fieldModel); 
	                    $fieldInstances[] = $fieldInstance; 
	                    continue; 
	                } 
	                //SalesPlatform.ru end History of products changes
				if(!$fieldModel) continue;
				
				$fieldInstance = new ModTracker_Field_Model();
				$fieldInstance->setData($row)->setParent($this)->setFieldInstance($fieldModel);
				$fieldInstances[] = $fieldInstance;
			}
		}
		return $fieldInstances;
	}

	function getRelationInstance() {
		$id = $this->get('id');
		$db = PearDatabase::getInstance();

		if($this->isRelationLink() || $this->isRelationUnLink()) {
			$result = $db->pquery('SELECT * FROM vtiger_modtracker_relations WHERE id = ?', array($id));
			$row = $db->query_result_rowdata($result, 0);
			$relationInstance = new ModTracker_Relation_Model();
			$relationInstance->setData($row)->setParent($this);
		}
		return $relationInstance;
	}
        
	public function getTotalRecordCount($recordId) {
    	$db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT COUNT(*) AS count FROM vtiger_modtracker_basic WHERE crmid = ?", array($recordId));
        return $db->query_result($result, 0, 'count');
	}
}

	 
	//SalesPlatform.ru begin History of products changes
	class ExtendentModTracker_Field_Model extends ModTracker_Field_Model { 
	     
	    public function get($key) { 
	        if($key === 'prevalue') { 
	            return $this->getFieldInstance()->get($key); 
	        } 
	         
	        if($key === 'postvalue') { 
	            return $this->getFieldInstance()->get($key); 
	        } 
	         
	        return parent::get($key); 
	    } 
	     
	} 
	 
	class InventoryModTracker_Field_Model extends Vtiger_Field_Model { 
	     
	    private $productId; 
	    private $fieldType; 
	    private $moduleModel; 
	     
	    private $postValue; 
	    private $preValue; 
	     
	    public function __construct($row, $moduleModel) { 
	        parent::__construct(); 
	         
	        list($this->fieldType, $this->productId) = explode("_", $row['fieldname'], 2); 
	        $this->moduleModel = $moduleModel; 
	        $this->preValue = $row['prevalue']; 
	        $this->postValue = $row['postvalue']; 
	        $this->prepareDisplayCompare(); 
	    } 
	     
	    public function isViewable() { 
	        return true; 
	    } 
	     
	    public function getDisplayType() { 
	        return '1'; 
	    } 
	     
	    public function get($propertyName) { 
	        if($propertyName == 'label') { 
	            return $this->getName(); 
	        } 
	         
	        if($propertyName === 'prevalue') { 
	            return $this->getPreValue(); 
	        } 
	         
	        if($propertyName === 'postvalue') { 
	            return $this->getPostValue(); 
	        } 
	         
	        return parent::get($propertyName); 
	    } 
	     
	    public function getName() { 
	        $db = PearDatabase::getInstance(); 
	         
	        /* Get type */ 
	        $result = $db->pquery("SELECT label FROM vtiger_crmentity WHERE crmid=?", array($this->productId)); 
	        if($result && $resultRow = $db->fetchByAssoc($result)) { 
	            return $resultRow['label']; 
	        } 
	         
	        return ''; 
	    } 
	     
	    private function prepareDisplayCompare() { 
	        if($this->preValue != null && $this->postValue != null) { 
	            $this->initDiffDisplayContent( 
	                ModTrackerInventory::fromTracker($this->preValue, $this->moduleModel),  
	                ModTrackerInventory::fromTracker($this->postValue, $this->moduleModel) 
	            ); 
	            return; 
	        } 
	         
	        if($this->preValue != null) { 
	            $this->preValue = $this->getDispayContent( 
	                ModTrackerInventory::fromTracker($this->preValue, $this->moduleModel) 
	            ); 
	        } 
	         
	        if($this->postValue != null) { 
	            $this->postValue = $this->getDispayContent( 
	                ModTrackerInventory::fromTracker($this->postValue, $this->moduleModel) 
	            ); 
	        } 
	    } 
	     
	    private function initDiffDisplayContent($beforeInventory, $afterInventory) { 
	        $beforeDisplayFields = $beforeInventory->getDisplayFields(); 
	        $afterDisplayFields = $afterInventory->getDisplayFields(); 
	         
	        /* Get only diff fields names */ 
	        $fieldsNames = array(); 
	        foreach($beforeDisplayFields as $fieldName => $beforeFieldModel) { 
	            if(array_key_exists($fieldName, $afterDisplayFields)) { 
	                $afterFieldModel = $afterDisplayFields[$fieldName]; 
	                 
	                if($afterFieldModel->get('value') != $beforeFieldModel->get('value')) { 
	                    $fieldsNames[] = $fieldName; 
	                } 
	            } 
	        } 
	         
	        $this->preValue = $this->getDispayContent($beforeInventory, $fieldsNames); 
	        $this->postValue = $this->getDispayContent($afterInventory, $fieldsNames); 
	    } 
	     
	    private function getDispayContent($inventory, $onlyFieldsNames = array()) { 
	        $displayParts = array(); 
	        foreach($inventory->getDisplayFields() as $fieldName => $fieldModel) { 
	            if(!empty($onlyFieldsNames) && !in_array($fieldName, $onlyFieldsNames)) { 
	                continue; 
	            } 
	             
	            $fieldValue = $fieldModel->getDisplayValue($fieldModel->get('value')); 
	            if($fieldValue != null) { 
	               $displayParts[] = vtranslate($fieldModel->get('label'), $this->moduleModel->getName())  .  
	                    " : «" .  $fieldValue . "»";  
	            } 
	             
	             
	        } 
	         
	        return join(", ", $displayParts); 
	    } 
	     
	    private function getPreValue() { 
	        return $this->preValue; 
	    } 
	     
	    private function getPostValue() { 
	        return $this->postValue; 
	    } 
	} 
	 
	 
	class ModTrackerInventory { 
	     
	    private $fields; 
	     
	    private $displayFieldsNames = array( 
	        'quantity', 'listprice', 'discount_percent', 'discount_amount', 
	        'comment', 'description', 'tax1' 
	    ); 
	     
	    protected function __construct($serialized, $moduleModel) { 
	        $this->fields = array(); 
	        $jsonFields = json_decode(decode_html($serialized)); 
	         
	        foreach($this->displayFieldsNames as $fieldName) { 
	            if(property_exists($jsonFields, $fieldName)) { 
	                $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleModel); 
	                if(!$fieldModel) { 
	                    $fieldModel = new Vtiger_Field_Model(); 
	                    $fieldModel->set('label', $fieldName); 
	                    $fieldModel->set('name', $fieldName); 
	                } 
	                $fieldModel->set('value', (string) $jsonFields->{$fieldName}); 
	                 
	                $this->fields[$fieldModel->getName()] = $fieldModel; 
	            } 
	        } 
	    } 
	     
	    /** 
	     *  
	     * @param type $serialized 
	     * @return ModTrackerInventory Description 
	     */ 
	    public static function fromTracker($serialized, $moduleModel) { 
	        return new ModTrackerInventory($serialized, $moduleModel); 
	    } 
	     
	    public function getDisplayFields() { 
	        return $this->fields; 
	    } 
	} 
	//SalesPlatform.ru end History of products changes