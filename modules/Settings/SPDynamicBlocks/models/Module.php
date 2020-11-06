<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_Module_Model extends Settings_Vtiger_Module_Model{
    
    private $tableName = 'vtiger_sp_blocks_configuration';
    private $config2valuesTable = 'vtiger_sp_blocks_configuration2values';
    private $config2blocksTable = 'vtiger_sp_blocks_configuration2blocks';
    
    var $listFields = array('module_name' => 'LBL_MODULE', 'field_name' => 'LBL_PICKLIST', 'values' => 'LBL_VALUES', 'blocks' => 'LBL_BLOCKS');
	var $baseIndex = 'sp_blocks_configuration_id';
    var $name = 'SPDynamicBlocks';
    
    private static $restrictedModules = array('Emails');
    
    public function getCreateRecordUrl() {
        return "index.php?parent=Settings&module=SPDynamicBlocks&view=Edit";
    }
    
    public function getBaseTable() {
		return $this->tableName;
	}
    
    public function getValuesTable() {
        return $this->config2valuesTable;
    }
    
    public function getBlocksTable() {
        return $this->config2blocksTable;
    }
    
    public static function getlistViewURL() {
        $db = PearDatabase::getInstance();
        $returnURL = "index.php";
        $result = $db->pquery("SELECT linkto FROM vtiger_settings_field WHERE name=?", array('DynamicBlocks'));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $returnURL = $resRow['linkto'];
        }        
        return html_entity_decode($returnURL);
    }
    
    public static function getPicklistSupportedModules($restrictedModules = array()) {		
		$modules = self::getPickListModules();
		$modulesModelsList = array();
        $restrictedModules = array_merge(self::$restrictedModules, $restrictedModules);
		foreach($modules as $moduleLabel => $moduleName) {
            if (in_array($moduleName, $restrictedModules)) {
                continue;
            }
			$instance = new self();
			$instance->name = $moduleName;
			$instance->label = $moduleLabel;
			$modulesModelsList[] = $instance;
		}
		return $modulesModelsList;
	}
    
    private static function getPickListModules(){
        $db = PearDatabase::getInstance();        
        $query = 'select distinct vtiger_field.fieldname,vtiger_field.tabid,vtiger_tab.tablabel, vtiger_tab.name as tabname,uitype from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where uitype IN (15,16,33) and vtiger_field.tabid != 29 and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2) order by vtiger_field.tabid ASC';        
        $result = $db->pquery($query, array());
        while($result && $row = $db->fetch_array($result)){
            $modules[$row['tablabel']] = $row['tabname'];
        }
        return $modules;
    }
}