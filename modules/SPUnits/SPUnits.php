<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';
require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Utils.php';
require_once 'modules/Vtiger/CRMEntity.php'; 

class SPUnits extends Vtiger_CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'sp_units';
	var $table_index= 'unitid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('sp_unitscf', 'unitid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'sp_units', 'sp_unitscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'sp_units'   => 'unitid',
	    'sp_unitscf' => 'unitid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
                'Unit No'=>Array('sp_units'=>'unit_no'),
                'Unit Name'=>Array('sp_units'=>'unitname'),
                'Usage Unit'=>Array('sp_units'=>'usageunit'),
                'Unit Code'=>Array('sp_units'=>'unit_code'),
                'Assigned To'=>Array('crmentity'=>'smownerid'),
	);
	var $list_fields_name = Array(
                'Unit No'=>'unit_no',
                'Unit Name'=>'unitname',
                'Usage Unit'=>'usageunit',
                'Unit Code'=>'unit_code',
                'Assigned To'=>'assigned_user_id',
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'unitname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
                'Unit No'=>Array('sp_units'=>'unit_no'),
                'Unit Name'=>Array('sp_units'=>'unitname'),
                'Usage Unit'=>Array('sp_units'=>'usageunit'),
                'Unit Code'=>Array('sp_units'=>'unit_code'),
	);
	var $search_fields_name = Array(
                'Unit No'=>'unit_no',
                'Unit Name'=>'unitname',
                'Usage Unit'=>'usageunit',
                'Unit Code'=>'unit_code',
	);

	// For Popup window record selection
	var $popup_fields = Array('unitname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'unitname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'unitname';

	// Required Information for enabling Import feature
	var $required_fields = Array('unitname'=>1);

	var $default_order_by = 'unitname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'unitname');
	
	function __construct() {
		global $log;
                $this->column_fields = getColumnFields('SPUnits');
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function getSortOrder() {
		global $currentModule;

		$sortorder = $this->default_sort_order;
		if($_REQUEST['sorder']) $sortorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else if($_SESSION[$currentModule.'_Sort_Order']) 
			$sortorder = $_SESSION[$currentModule.'_Sort_Order'];

		return $sortorder;
	}

	function getOrderBy() {
		global $currentModule;
		
		$use_default_order_by = '';		
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		
		$orderby = $use_default_order_by;
		if($_REQUEST['order_by']) $orderby = $this->db->sql_escape_string($_REQUEST['order_by']);
		else if($_SESSION[$currentModule.'_Order_By'])
			$orderby = $_SESSION[$currentModule.'_Order_By'];
		return $orderby;
	}

	function save_module($module) {
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $where='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";
		
		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';
		
		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0]; 
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';
		
		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);
		
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');
			
			$other =  CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);
			
			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role 
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					) 
					OR vtiger_crmentity.smownerid IN 
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					) 
					OR 
						(";
		
					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN 
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;
		$thismodule = $_REQUEST['module'];
		
		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");
		
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";
		
		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');
			
			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);
			
			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}
	
	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}
	
	/** 
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		
		$where_clause = "	WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);
					
		if (isset($select_cols) && trim($select_cols) != '') {
                        $sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";	
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}	
		
		
		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";
					
		return $query;		
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		require_once('include/utils/utils.php');
		include_once('vtlib/Vtiger/Module.php');
                global $adb;
                $wf_task = array(vtranslate('LBL_WF1_TASK_SUMMARY', 'SPUnits'), vtranslate('LBL_WF2_TASK_SUMMARY', 'SPUnits'),
                    vtranslate('LBL_WF3_TASK_SUMMARY', 'SPUnits'));
		if($event_type == 'module.postinstall') {		
            $projectmilestoneTabid = getTabid($modulename);
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));
			
			$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
			$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
			$nextSequence = $maxSequence+1;
			
			$adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($projectmilestoneTabid,1,$nextSequence)");
			$adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($projectmilestoneTabid,'showrelatedinfo',1)");
            
            $this->create_new_fields();
            $this->process_Workflows();
            $this->setDisplaytype(2);
		} else if($event_type == 'module.disabled') {
            $this->saveActivityTask($wf_task, false);
            $this->setDisplaytype(1);
        // TODO Handle actions when this module is disabled. 
		} else if($event_type == 'module.enabled') {
            $this->saveActivityTask($wf_task, true);
            $this->setDisplaytype(2);
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}
        
    function process_Workflows() {
        $workflow_data = array(
            "wf1" => array("summary" => vtranslate('LBL_WF1_SUMMARY','SPUnits'),
                        "func_name" => 'processUsageUnit', "path" => "modules/SPUnits/workflow/processUsageUnit.php",
                        "task_summary" => vtranslate('LBL_WF1_TASK_SUMMARY','SPUnits'),
                        "modulename" => "SPUnits",
                        "execution_condition" => 3,
                        "type" => "basic",
                        "task_object" => 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";i:#workflowId#;s:7:"summary";'. serialize(vtranslate('LBL_WF1_TASK_SUMMARY', 'SPUnits')) .'s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"processUsageUnit";s:2:"id";i:#taskId#;}'),
            "wf2" => array("summary" => vtranslate('LBL_WF2_SUMMARY','SPUnits'),
                        "func_name" => 'processCodeUnit', "path" => "modules/SPUnits/workflow/processCodeUnit.php",
                        "task_summary" => vtranslate('LBL_WF2_TASK_SUMMARY','SPUnits'),
                        "modulename" => "Products",
                        "execution_condition" => 3,
                        "type" => "basic",
                        "task_object" => 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";i:#workflowId#;s:7:"summary";'. serialize(vtranslate('LBL_WF2_TASK_SUMMARY', 'SPUnits')) .'s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:15:"processCodeUnit";s:2:"id";i:#taskId#;}'),
            "wf3" => array("summary" => vtranslate('LBL_WF3_SUMMARY','SPUnits'),
                        "func_name" => 'processCodeUnit', "path" => "modules/SPUnits/workflow/processCodeUnit.php",
                        "task_summary" => vtranslate('LBL_WF3_TASK_SUMMARY','SPUnits'),
                        "modulename" => "Services",
                        "execution_condition" => 3,
                        "type" => "basic",
                        "task_object" => 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";i:#workflowId#;s:7:"summary";'. serialize(vtranslate('LBL_WF3_TASK_SUMMARY', 'SPUnits')) .'s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:15:"processCodeUnit";s:2:"id";i:#taskId#;}'));
        foreach ($workflow_data as $data) {
                $this->create_Workflow($data);             
        }
    }

    function create_Workflow($data) {
        global $adb;
        $wf_id = $adb->getUniqueID('com_vtiger_workflows');
        $sql = "INSERT INTO com_vtiger_workflows ".
                "(workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type) ".
                "VALUES (?,?,?,?,?,?,?)";
        $params = array($wf_id, $data["modulename"], 
                 $data["summary"], NULL, $data["execution_condition"], NULL, $data["type"]);
        $adb->pquery($sql, $params);

        $method_id = $adb->getUniqueID('com_vtiger_workflowtasks_entitymethod');
        $sql = "INSERT INTO com_vtiger_workflowtasks_entitymethod ".
                "(workflowtasks_entitymethod_id, module_name, method_name, function_path, function_name) ".
                "VALUES (?,?,?,?,?)";
        $params = array($method_id, $data["modulename"], $data["func_name"], $data["path"], $data["func_name"]);
        $adb->pquery($sql, $params);

        $task_id = $adb->getUniqueID('com_vtiger_workflowtasks');
        $task_object = str_replace("#workflowId#", $wf_id, $data["task_object"]);
        $task_object = str_replace("#taskId#", $task_id, $task_object);

        $sql = "INSERT INTO com_vtiger_workflowtasks ".
                "(task_id, workflow_id, summary, task) ".
                "VALUES (?,?,?,?)";
        $params = array($task_id, $wf_id, $data["task_summary"], $task_object);
        $adb->pquery($sql, $params);
    }

    function saveActivityTask($wf_task, $activity) {
        global $adb;
        require_once("modules/com_vtiger_workflow/VTTaskManager.inc");             
        $tm = new VTTaskManager($adb);
        for ($i=0; $i<count($wf_task); $i++) {
                $task_res = $adb->pquery("select task_id from com_vtiger_workflowtasks ".
                                            "where summary = ?", array($wf_task[$i]));
                if ($task_res) {
                        if ($adb->num_rows($task_res) > 0) {
                                $task_id = $adb->query_result($task_res, 0, 'task_id');
                                $task = $tm->retrieveTask($task_id);
                                $task->active = $activity;
                                $tm->saveTask($task);
                        }
                }
        }
    }

    function setDisplaytype($type) {
        global $adb;
        $adb->pquery("update vtiger_field SET displaytype = ? ".
                "where tablename = 'vtiger_products' and fieldname = 'unit_code'", array($type));
        $adb->pquery("update vtiger_field SET displaytype = ? ".
                "where tablename = 'vtiger_service' and fieldname = 'unit_code'", array($type));
    }

    function create_new_fields() {
        $productInstance = Vtiger_Module::getInstance('Products');
        $productBlockInstance = Vtiger_Block::getInstance('LBL_STOCK_INFORMATION', $productInstance);            
        $fieldInstance1 = new Vtiger_Field();
        $fieldInstance1->label = 'Unit Code';
        $fieldInstance1->name = 'unit_code';
        $fieldInstance1->table = 'vtiger_products';
        $fieldInstance1->column = 'unit_code';
        $fieldInstance1->columntype = 'VARCHAR(100)';
        $fieldInstance1->uitype = 1;
        $fieldInstance1->typeofdata = 'V~O';
        $productBlockInstance->addField($fieldInstance1);

        $serviceInstance = Vtiger_Module::getInstance('Services');
        $serviceBlockInstance = Vtiger_Block::getInstance('LBL_SERVICE_INFORMATION', $serviceInstance);            
        $fieldInstance2 = new Vtiger_Field();
        $fieldInstance2->label = 'Unit Code';
        $fieldInstance2->name = 'unit_code';
        $fieldInstance2->table = 'vtiger_service';
        $fieldInstance2->column = 'unit_code';
        $fieldInstance2->columntype = 'VARCHAR(100)';
        $fieldInstance2->uitype = 1;
        $fieldInstance2->typeofdata = 'V~O';
        $serviceBlockInstance->addField($fieldInstance2);
    }
}


?>