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

class SPPayments extends Vtiger_CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'sp_payments';
	var $table_index= 'payid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('sp_paymentscf', 'payid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'sp_payments', 'sp_paymentscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'sp_payments'   => 'payid',
	    'sp_paymentscf' => 'payid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
                'Pay No'=>Array('sp_payments'=>'pay_no'),
                'Pay type'=>Array('sp_payments'=>'pay_type'),
                'Payer'=>Array('sp_payments'=>'payer'),
                'Amount'=>Array('sp_payments'=>'amount'),
                'Assigned To'=>Array('crmentity'=>'smownerid')
                
	);
	var $list_fields_name = Array(
                'Pay No'=>'pay_no',
                'Pay type'=>'pay_type',
                'Payer'=>'payer',
                'Amount'=>'amount',
                'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'pay_no';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
                'Pay No'=>Array('sp_payments'=>'pay_no'),
                'Pay type'=>Array('sp_payments'=>'pay_type'),
                'Payer'=>Array('sp_payments'=>'payer'),
	);
	var $search_fields_name = Array(
                'Pay No'=>'pay_no',
                'Pay type'=>'pay_type',
                'Payer'=>'payer',
	);

	// For Popup window record selection
	var $popup_fields = Array('pay_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'pay_no';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'pay_no';

	// Required Information for enabling Import feature
	var $required_fields = Array('pay_no'=>1);

	var $default_order_by = 'pay_no';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'pay_no');
	
    var $related_tables = Array('sp_paymentscf' => Array('payid')); 
    
	function __construct() {
		global $log;
                $this->column_fields = getColumnFields('SPPayments');
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
		if($event_type == 'module.postinstall') {		
			$projectmilestoneTabid = getTabid($modulename);
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));
			
			$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
			$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
			$nextSequence = $maxSequence+1;
			
			$adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($projectmilestoneTabid,1,$nextSequence)");
			$adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($projectmilestoneTabid,'showrelatedinfo',1)");
          
                        $this->process_Payments($modulename);
                        
                        // Added pdf-template
                        $filename = "modules/SPPayments/pdftemplates/cashorder.htm";
                        $handle = fopen($filename, "r");
                        $body = fread($handle, filesize($filename));
                        fclose($handle);
                        $templatename = 'Приходный кассовый ордер';
                        $header_size = 0;
                        $footer_size = 50;
                        $page_orientation = 'P';
                        $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $modulename));
                        if($adb->num_rows($res) == 0) {
                            $templateid = $adb->getUniqueID('sp_templates');
                            $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                            $params = array($templatename, $modulename, $body, $header_size, $footer_size, $page_orientation, $templateid);
                            $adb->pquery($sql, $params);
                        } else {
                            $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                            $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $modulename);
                            $adb->pquery($sql, $params);
                        }
                        
            $modFocus = CRMEntity::getInstance('SPPayments');
            $modFocus->setModuleSeqNumber('configure', 'SPPayments', '', '1');

		} else if($event_type == 'module.disabled') {
                        $this->unlinkDetails($modulename);
			// TODO Handle actions when this module is disabled. 
		} else if($event_type == 'module.enabled') {
                        $this->process_Payments($modulename);
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
                        $this->unlinkDetails($modulename);
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
                        // Added pdf-template
                        $filename = "modules/SPPayments/pdftemplates/cashorder.htm";
                        $handle = fopen($filename, "r");
                        $body = fread($handle, filesize($filename));
                        fclose($handle);
                        $templatename = 'Приходный кассовый ордер';
                        $header_size = 0;
                        $footer_size = 50;
                        $page_orientation = 'P';
                        $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $modulename));
                        if($adb->num_rows($res) == 0) {
                            $templateid = $adb->getUniqueID('sp_templates');
                            $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                            $params = array($templatename, $modulename, $body, $header_size, $footer_size, $page_orientation, $templateid);
                            $adb->pquery($sql, $params);
                        } else {
                            $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                            $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $modulename);
                            $adb->pquery($sql, $params);
                        }
		}
	}
        
        function process_Payments($modulename) {
                global $adb;
                $rel_modules = array('Accounts', 'Contacts', 'Vendors', 'Invoice', 'SalesOrder', 'PurchaseOrder');           
                $query_reltab_id = $adb->pquery("SELECT tabid FROM vtiger_tab WHERE name = ?", array($modulename));
                if ($query_reltab_id) {
                    if ($adb->num_rows($query_reltab_id) > 0) {
                        $reltab_id = $adb->query_result($query_reltab_id, 0, 'tabid');
                        for ($i=0; $i<count($rel_modules); $i++) {
                            $query_tab_id = $adb->pquery("SELECT tabid FROM vtiger_tab where name = ?", array($rel_modules[$i]));
                            if ($query_tab_id) {
                                if ($adb->num_rows($query_tab_id) > 0) {
                                    $tab_id = $adb->query_result($query_tab_id, 0, 'tabid');
                                    $query_rel_seq = $adb->pquery("SELECT max(sequence) AS seq FROM vtiger_relatedlists WHERE tabid = ?", array($tab_id));
                                    $rel_seq = $adb->query_result($query_rel_seq, 0, 'seq');
                                    if (!is_numeric($rel_seq)) {
                                       $rel_seq = 0;
                                    }
                                    $rel_id = $adb->getUniqueId('vtiger_relatedlists');
                                    $sql = "INSERT INTO vtiger_relatedlists ".
                                    "(relation_id, tabid, related_tabid, name, sequence, label, presence, actions) ".
                                    "VALUES (?,?,?,?,?,?,?,?)";
                                    $params = array($rel_id, $tab_id, $reltab_id, 'get_dependents_list',
                                        ++$rel_seq, $modulename, 0, 'ADD,SELECT');
                                    $adb->pquery($sql, $params);

                                    $query_link_seq = $adb->pquery("SELECT max(sequence) AS seq FROM vtiger_links WHERE tabid = ?", array($tab_id));
                                    $link_seq = $adb->query_result($query_link_seq, 0, 'seq');
                                    if (!is_numeric($link_seq)) {
                                       $link_seq = 0;
                                    }
                                    $link_id = $adb->getUniqueId('vtiger_links');
                                    $sql_links = "INSERT INTO vtiger_links ".
                                    "(linkid, tabid, linktype, linklabel, linkurl, linkicon, sequence, handler_path, handler_class, handler) ".
                                    "VALUES (?,?,?,?,?,?,?,?,?,?)";
                                    // SalesPlatform.ru begin Add new icon
                                    $params_links = array($link_id, $tab_id, 'DETAILVIEWBASIC', 'LBL_SP_ADD_SPPAYMENTS',
                                        'index.php?module=SPPayments&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
                                        'themes/images/actionGenerateInvoice_new.gif', ++$link_seq, 'NULL', 'NULL', 'NULL');
                                    //$params_links = array($link_id, $tab_id, 'DETAILVIEWBASIC', 'LBL_SP_ADD_SPPAYMENTS',
                                    //    'index.php?module=SPPayments&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
                                    //    'themes/images/actionGenerateInvoice.gif', ++$link_seq, 'NULL', 'NULL', 'NULL');
                                    // SalesPlatform.ru end
                                    $adb->pquery($sql_links, $params_links);
                                }
                            }
                        }
                    }
                }
            }

            function unlinkDetails($modulename) {
                global $adb;
                $query_tab_id = $adb->pquery("SELECT tabid FROM vtiger_tab WHERE name = ?", array($modulename));
                if ($query_tab_id) {
                    if ($adb->num_rows($query_tab_id) > 0) {
                        $tab_id = $adb->query_result($query_tab_id, 0, 'tabid');
                        $adb->pquery("DELETE FROM vtiger_relatedlists WHERE related_tabid = ?", array($tab_id));
                        $adb->pquery("DELETE FROM vtiger_links WHERE linktype = 'DETAILVIEWBASIC' and linklabel = 'LBL_SP_ADD_SPPAYMENTS'", array());
                        $query_relid = $adb->pquery("SELECT max(relation_id) AS id FROM vtiger_relatedlists", array());
                        $query_linkid = $adb->pquery("SELECT max(linkid) AS id FROM vtiger_links", array());
                        if ($query_relid) {
                            if ($adb->num_rows($query_relid) > 0) {
                                $relation_id = $adb->query_result($query_relid, 0, 'id');
                                $adb->pquery("UPDATE vtiger_relatedlists_seq SET id = ?", array($relation_id));
                            }
                        }
                        if ($query_linkid) {
                            if ($adb->num_rows($query_linkid) > 0) {
                                $link_id = $adb->query_result($query_linkid, 0, 'id');
                                $adb->pquery("UPDATE vtiger_links_seq SET id = ?", array($link_id));
                            }
                        }
                    }
                }
            }
}
?>