<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Migration_Module_Model extends Vtiger_Module_Model {
	
	public function getDBVersion(){
		$db = PearDatabase::getInstance();
		
		$result = $db->pquery('SELECT current_version FROM vtiger_version', array());
		if($db->num_rows($result) > 0){
			$currentVersion = $db->query_result($result, 0, 'current_version');
		}
		return $currentVersion;
	}
	
	public static function getInstance() {
		return new self();
	}
	
	public function getAllowedMigrationVersions(){
            // SalesPlatform.ru begin
            $versions = array(
                array('540-201310' => '5.4.0-201310'),
                array('600-201403' => '6.0.0-201403'),
                array('610-201410' => '6.1.0-201410'),
                array('610-201412' => '6.1.0-201412'),
                array('620-201505' => '6.2.0-201505'),
                array('630-201507' => '6.3.0-201507'),
                array('640-201512' => '6.4.0-201512'),
                array('650-201611' => '6.5.0-201611'),
                array('650-201707' => '6.5.0-201707'),
                array('701-201707' => '7.0.1-201707'),
                array('701-201711' => '7.0.1-201711'),
                array('710-201803' => '7.1.0-201803'),
            );
            // SalesPlatform.ru end
            return $versions;
	}
	
	public function getLatestSourceVersion(){
		return vglobal('vtiger_current_version');
	}
	
	/**
	 * Function to update the latest vtiger version in db
	 * @return type
	 */
	public function updateVtigerVersion(){
		$db = PearDatabase::getInstance();
		$db->pquery('UPDATE vtiger_version SET current_version=?,old_version=?', array($this->getLatestSourceVersion(), $this->getDBVersion()));
		return true;
	}
	
	/**
	 * Function to rename the migration file and folder
	 * Writing tab data in flat file
	 */
	public function postMigrateActivities(){
		//Writing tab data in flat file
		perform_post_migration_activities();
		
		//rename the migration file and folder
		$renamefile = uniqid(rand(), true);
				
		if(!@rename("migrate/", $renamefile."migrate/")) {
			if (@copy ("migrate/", $renamefile."migrate/")) {
				@unlink("migrate/");
			} 
		}
	}
}
