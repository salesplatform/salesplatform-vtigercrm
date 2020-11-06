<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Vtiger_CustomRecordNumberingModule_Model extends Vtiger_Module_Model {

	/**
	 * Function to get focus of this object
	 * @return <type>
	 */
	public function getFocus() {
		if (!$this->focus) {
			$this->focus = CRMEntity::getInstance($this->getName());
		}
		return $this->focus;
	}

	/**
	 * Function to get Instance of this module
	 * @param <String> $moduleName
	 * @return <Settings_Vtiger_CustomRecordNumberingModule_Model> $moduleModel
	 */
	public static function getInstance($moduleName, $tabId = false) {
		$moduleModel = new self();
		$moduleModel->name = $moduleName;
		if ($tabId) {
			$moduleModel->id = $tabId;
		}
		return $moduleModel;
	}

	/**
	 * Function to ger Supported modules for Custom record numbering
	 * @return <Array> list of supported modules <Vtiger_Module_Model>
	 */
	public static function getSupportedModules() {
		$db = PearDatabase::getInstance();

		$sql = "SELECT tabid, name FROM vtiger_tab WHERE isentitytype = ? AND presence = ? AND tabid IN (SELECT DISTINCT tabid FROM vtiger_field WHERE uitype = ?)";
		$result = $db->pquery($sql, array(1, 0, 4));
		$numOfRows = $db->num_rows($result);

		for($i=0; $i<$numOfRows; $i++) {
			$tabId = $db->query_result($result, $i, 'tabid');
			$modulesModels[$tabId] = Settings_Vtiger_CustomRecordNumberingModule_Model::getInstance($db->query_result($result, $i, 'name'), $tabId);
		}

		return $modulesModels;
	}

	/**
	 * Function to get module custom numbering data
	 * @return <Array> data of custom numbering data
	 */
    // SalesPlatform.ru begin: Added separate numbering for self organizations
    public function getModuleCustomNumberingData($spCompany = '') {
        $moduleInfo = $this->getFocus()->getModuleSeqInfo($this->getName(), $spCompany);
	//public function getModuleCustomNumberingData() {
        //$moduleInfo = $this->getFocus()->getModuleSeqInfo($this->getName());
    // SalesPlatform.ru end
		return array(
				'prefix' => $moduleInfo[0],
				'sequenceNumber' => $moduleInfo[1]
		);
	}

	/**
	 * Function to set Module sequence
	 * @return <Array> result of success
	 */
    // SalesPlatform.ru begin: Added separate numbering for self organizations
    public function setModuleSequence($spCompany = '') {
    //public function setModuleSequence() {
    // SalesPlatform.ru end
		$moduleName = $this->getName();
		$prefix = $this->get('prefix');
		$sequenceNumber = $this->get('sequenceNumber');
                
        // SalesPlatform.ru begin: Added separate numbering for self organizations
        $status = $this->getFocus()->setModuleSeqNumber('configure', $moduleName, $prefix, $sequenceNumber, $spCompany);
		//$status = $this->getFocus()->setModuleSeqNumber('configure', $moduleName, $prefix, $sequenceNumber);
        // SalesPlatform.ru end

		$success = array('success' => $status);
		if (!$status) {
			$db = PearDatabase::getInstance();
                        // SalesPlatform.ru begin: Added separate numbering for self organizations
                        $result = $db->pquery("SELECT cur_id FROM vtiger_modentity_num WHERE semodule = ? AND prefix = ? and spcompany=?", array($moduleName, $prefix, $spCompany));
			//$result = $db->pquery("SELECT cur_id FROM vtiger_modentity_num WHERE semodule = ? AND prefix = ?", array($moduleName, $prefix));
                        // SalesPlatform.ru end
			$success['sequenceNumber'] = $db->query_result($result, 0, 'cur_id');
		}

		return $success;
	}

	/**
	 * Function to update record sequences which are under this module
	 * @return <Array> result of success
	 */
        // SalesPlatform.ru begin: Added separate numbering for self organizations
        public function updateRecordsWithSequence($spCompany = '') {
            return $this->getFocus()->updateMissingSeqNumber($this->getName(), $spCompany);
	//public function updateRecordsWithSequence() {
            //return $this->getFocus()->updateMissingSeqNumber($this->getName());
        //SalesPlatform.ru end    
	}

}