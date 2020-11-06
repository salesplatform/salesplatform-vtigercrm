<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

include_once 'config.php';
require_once 'include/logging.php';
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'user_privileges/default_module_view.php';
require_once 'modules/Vtiger/CRMEntity.php'; 

// Account is used to store vtiger_account information.
class Act extends Vtiger_CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_sp_act";
	var $table_index= 'actid';
	var $tab_name = Array('vtiger_crmentity','vtiger_sp_act','vtiger_sp_actbillads','vtiger_sp_actshipads','vtiger_sp_actcf', 'vtiger_inventoryproductrel');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_sp_act'=>'actid','vtiger_sp_actbillads'=>'actbilladdressid','vtiger_sp_actshipads'=>'actshipaddressid','vtiger_sp_actcf'=>'actid', 'vtiger_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_sp_actcf', 'actid');
	
	var $column_fields = Array();

	var $update_product_array = Array();	

	var $sortby_fields = Array('act_no','sp_actstatus','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				'Act No'=>Array('sp_act'=>'act_no'),
				'Sales Order'=>Array('sp_act'=>'salesorderid'),
				'Status'=>Array('sp_act'=>'sp_actstatus'),
				'Total'=>Array('sp_act'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);
	
	var $list_fields_name = Array(
				        'Act No'=>'act_no',
				        'Sales Order'=>'salesorder_id',
				        'Status'=>'sp_actstatus',
				        'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'act_no';

	var $search_fields = Array(
				'Act No'=>Array('sp_act'=>'act_no'),
				'Status'=>Array('sp_act'=>'sp_actstatus'),
				'Total'=>Array('sp_act'=>'total'),
				);
	
	var $search_fields_name = Array(
				        'Act No'=>'act_no',
				        'Status'=>'sp_actstatus',
				        'Total'=>'hdnGrandTotal',
				      );

        // For Popup window record selection
	var $popup_fields = Array('act_no');

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	var $mandatory_fields = Array('act_no','createdtime' ,'modifiedtime');
	var $_salesorderid;
	var $_recurring_mode;
	
	// For Alphabetical search
	var $def_basicsearch_col = 'act_no';
    
    //SalesPlatform.ru begin
    // For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;
    //SalesPlatform.ru end

	/**	Constructor which will set the column_fields in this object
	 */
	function Act() {
		$this->log =LoggerManager::getLogger('Act');
		$this->log->debug("Entering Act() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Act');
		$this->log->debug("Exiting Act method ...");
	}
    
    function insertIntoEntityTable($table_name, $module, $fileid = '')  {
		//Ignore relation table insertions while saving of the record
		if($table_name == 'vtiger_inventoryproductrel') {
			return;
		}
		parent::insertIntoEntityTable($table_name, $module, $fileid);
	}
    

	/** Function to handle the module specific save operations
	
	*/
	function save_module($module)
	{
		//in ajax save we should not call this function, because this will delete all the existing product values
		if(isset($_REQUEST)) {
            
            //SalesPlatform.ru begin
            if (isset($_REQUEST['REQUEST_FROM_WS']) && $_REQUEST['REQUEST_FROM_WS']) {
                unset($_REQUEST['totalProductCount']);
            }
            //SalesPlatform.ru end
            
            //SalesPlatform.ru begin
            if($_REQUEST['action'] != 'SaveAjax'&& $_REQUEST['action'] != 'ActAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
					&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'   
                    && $this->isLineItemUpdate != false && $_REQUEST['action'] != 'FROM_WS') {
            
			//if($_REQUEST['action'] != 'ActAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
			//		&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates')
			//{
            //SalesPlatform.ru end
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails($this, 'Act');
			}
		}
		

		// Update the currency id and the conversion rate for the act
		$update_query = "update vtiger_sp_act set currency_id=?, conversion_rate=? where actid=?";
		
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id); 
		$this->db->pquery($update_query, $update_params);

        // Update Invoice Act id field
        if(($_REQUEST['sourceModule'] == 'Invoice') && $_REQUEST['sourceRecord'] != null) {
            $this->db->pquery("update vtiger_invoice set sp_act_id = ? where invoiceid = ?", array($this->id, $_REQUEST['sourceRecord']));
        }
	}
        
	/**	function used to get the name of the current object
	 *	@return string $this->name - name of the current object
	 */
	function get_summary_text()
	{
		global $log;
		$log->debug("Entering get_summary_text() method ...");
		$log->debug("Exiting get_summary_text method ...");
		return $this->name;
	}


	/** Function to get activities associated with the Act
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedActivities() method
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.*,vtiger_seactivityrel.crmid as parent_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0 and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL and vtiger_activity.status !='Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype) {
		if ($columname == 'salesorderid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}
	
	/*
	 * Function to get the secondary query part of a report 
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
    //SalesPlatform.ru begin
    function generateReportsSecQuery($module,$secmodule,$queryplanner){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_sp_act","actid",$queryplanner);
	//function generateReportsSecQuery($module,$secmodule){
	//	$query = $this->getRelationQuery($module,$secmodule,"vtiger_sp_act","actid");
    //SalesPlatform.ru end    
		$query .= " left join vtiger_crmentity as vtiger_crmentityAct on vtiger_crmentityAct.crmid=vtiger_sp_act.actid and vtiger_crmentityAct.deleted=0
			left join vtiger_sp_actcf on vtiger_sp_act.actid = vtiger_sp_actcf.actid
			left join vtiger_salesorder as vtiger_salesorderAct on vtiger_salesorderAct.salesorderid=vtiger_sp_act.salesorderid
			left join vtiger_sp_actbillads on vtiger_sp_act.actid=vtiger_sp_actbillads.actbilladdressid
			left join vtiger_sp_actshipads on vtiger_sp_act.actid=vtiger_sp_actshipads.actshipaddressid
			left join vtiger_inventoryproductrel as vtiger_inventoryproductrelAct on vtiger_sp_act.actid = vtiger_inventoryproductrelAct.id
			left join vtiger_service as vtiger_serviceAct on vtiger_serviceAct.serviceid = vtiger_inventoryproductrelAct.productid
			left join vtiger_groups as vtiger_groupsAct on vtiger_groupsAct.groupid = vtiger_crmentityAct.smownerid
			left join vtiger_users as vtiger_usersAct on vtiger_usersAct.id = vtiger_crmentityAct.smownerid
			left join vtiger_contactdetails as vtiger_contactdetailsAct on vtiger_sp_act.contactid = vtiger_contactdetailsAct.contactid
			left join vtiger_account as vtiger_accountAct on vtiger_accountAct.accountid = vtiger_sp_act.accountid ";
        
        //SalesPlatform.ru begin
        $query .= $this->getReportsUiType10Query($secmodule, $queryplanner);
        //SalesPlatform.ru end
        
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules 
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_sp_act"=>"actid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_sp_act"=>"actid"),
			"Accounts" => array("vtiger_sp_act"=>array("actid","accountid")),
		);
		return $rel_tables[$secmodule];
	}
	
	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;
		
		if($return_module == 'Accounts' || $return_module == 'Contacts') {
			$this->trash('Act',$id);
		} elseif($return_module=='SalesOrder') {
			$relation_query = 'UPDATE vtiger_sp_act set salesorderid=0 where actid=?';
			$this->db->pquery($relation_query, array($id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}
        
        //SalesPlatform.ru begin global search for acts 
        function getListQuery($module, $usewhere='') {
                global $current_user;

                $query = "SELECT vtiger_crmentity.*,
			vtiger_sp_act.*,
			vtiger_sp_actbillads.*,
			vtiger_sp_actshipads.*,
			vtiger_salesorder.subject AS salessubject,
			vtiger_account.accountname,
			vtiger_currency_info.currency_name
			FROM vtiger_sp_act
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_sp_act.actid
			INNER JOIN vtiger_sp_actbillads
				ON vtiger_sp_act.actid = vtiger_sp_actbillads.actbilladdressid
			INNER JOIN vtiger_sp_actshipads
				ON vtiger_sp_act.actid = vtiger_sp_actshipads.actshipaddressid
			LEFT JOIN vtiger_currency_info
				ON vtiger_sp_act.currency_id = vtiger_currency_info.id
			LEFT OUTER JOIN vtiger_salesorder
				ON vtiger_salesorder.salesorderid = vtiger_sp_act.salesorderid
			LEFT OUTER JOIN vtiger_account
			        ON vtiger_account.accountid = vtiger_sp_act.accountid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_sp_act.contactid
			INNER JOIN vtiger_sp_actcf
				ON vtiger_sp_act.actid = vtiger_sp_actcf.actid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module,$current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ".$where;

                return $query;
        }
        //SalesPlatform.ru end
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		require_once('include/utils/utils.php');
		include_once('vtlib/Vtiger/Module.php');
		global $adb;

 		if($eventType == 'module.postinstall') {
			//Add Assets Module to Customer Portal

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$blockInstance = Vtiger_Block::getInstance('LBL_INVOICE_INFORMATION', $invoiceInstance);
			$fieldInstance = new Vtiger_Field();
			$fieldInstance->label = 'Act';
			$fieldInstance->name = 'sp_act_id';
			$fieldInstance->table = 'vtiger_invoice';
			$fieldInstance->column = 'sp_act_id';
			$fieldInstance->columntype = 'int';
			$fieldInstance->uitype = 10;
			$fieldInstance->typeofdata = 'I~O';
			$blockInstance->addField($fieldInstance);
			$fieldInstance->setRelatedModules(Array('Act'));

            // SalesPlatform.ru begin link Accounts
            $accountsInstance = Vtiger_Module::getInstance('Accounts');
            $actInstance = Vtiger_Module::getInstance('Act');
            $accountsInstance->addLink('DETAILVIEWBASIC', 'LBL_ACCOUNTS_ADD_ACT',
                    'index.php?module=Act&action=EditView&return_module=Accounts&return_action=DetailView&convertmode=accountstoact&createmode=link&return_id=$RECORD$&account_id=$RECORD$&parenttab=Sales',
                    'themes/images/actionGenerateInvoice.gif');
            // SalesPlatform.ru end
            
            //SalesPlatform.ru begin link Invoice
            $invoiceInstance->addLink('DETAILVIEWBASIC', 'LBL_INVOICE_ADD_ACT',
                    'index.php?module=Act&view=Edit&sourceModule=$MODULE$&sourceRecord=$RECORD$&invoice_id=$RECORD$&relationOperation=true',
                    'themes/images/actionGenerateInvoice.gif');
            //SalesPlatform.ru end 
            
            $accountsInstance->setRelatedlist($actInstance,'Act',array('ADD'),'get_dependents_list');
            
            // Salesplatform.ru begin Insert PDF after install
            $filename = "modules/Act/pdftemplates/act.htm";
            $handle = fopen($filename, "r");
            $body = fread($handle, filesize($filename));
            fclose($handle);

            $templatename = 'Акт';
            $header_size = 50;
            $footer_size = 50;
            $page_orientation = 'P';
            $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $moduleName));
            if($adb->num_rows($res) == 0) {
                $templateid = $adb->getUniqueID('sp_templates');
                $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                $adb->pquery($sql, $params);
            } else {
                $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $moduleName);
                $adb->pquery($sql, $params);
            }

            $filename = "modules/Act/pdftemplates/actwtax.htm";
            $handle = fopen($filename, "r");
            $body = fread($handle, filesize($filename));
            fclose($handle);

            $templatename = 'Акт с НДС';
            $header_size = 50;
            $footer_size = 50;
            $page_orientation = 'P';
            $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $moduleName));
            if($adb->num_rows($res) == 0) {
                $templateid = $adb->getUniqueID('sp_templates');
                $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                $adb->pquery($sql, $params);
            } else {
                $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $moduleName);
                $adb->pquery($sql, $params);
            }
            // Salesplatform.ru end
            
            $modFocus = CRMEntity::getInstance('Act');
            $modFocus->setModuleSeqNumber('configure', 'Act', '', '1');

		} else if($eventType == 'module.disabled') {
			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$fieldInstance = Vtiger_Field::getInstance('sp_act_id', $invoiceInstance);
			$fieldInstance->setPresence(1);
			$fieldInstance->save();
            // SalesPlatform.ru begin unlink Accounts
            $accountsInstance = Vtiger_Module::getInstance('Accounts');
            $actInstance = Vtiger_Module::getInstance('Act');
            $accountsInstance->deleteLink('DETAILVIEWBASIC', 'LBL_ACCOUNTS_ADD_ACT');
            // SalesPlatform.ru end
            //SalesPlatform.ru begin unlink Invoice
            $invoiceInstance->deleteLink('DETAILVIEWBASIC', 'LBL_INVOICE_ADD_ACT');
            //SalesPlatform.ru end

            $accountsInstance->unsetRelatedlist($actInstance,'Act','get_dependents_list');
                        
		} else if($eventType == 'module.enabled') {
			$invoiceInstance = Vtiger_Module::getInstance('Invoice');
			$fieldInstance = Vtiger_Field::getInstance('sp_act_id', $invoiceInstance);
			$fieldInstance->setPresence(2);
			$fieldInstance->save();
            // SalesPlatform.ru begin link Accounts
            $accountsInstance = Vtiger_Module::getInstance('Accounts');
            $actInstance = Vtiger_Module::getInstance('Act');

            // SalesPlatform.ru begin Add new icon
            $accountsInstance->addLink('DETAILVIEWBASIC', 'LBL_ACCOUNTS_ADD_ACT',
                    'index.php?module=Act&action=EditView&return_module=Accounts&return_action=DetailView&convertmode=accountstoact&createmode=link&return_id=$RECORD$&account_id=$RECORD$&parenttab=Sales',
                    'themes/images/actionGenerateQuote_new.gif');
            
            //SalesPlatform.ru begin link Invoice
            $invoiceInstance->addLink('DETAILVIEWBASIC', 'LBL_INVOICE_ADD_ACT',
                    'index.php?module=Act&view=Edit&sourceModule=$MODULE$&sourceRecord=$RECORD$&invoice_id=$RECORD$&relationOperation=true',
                    'themes/images/actionGenerateInvoice.gif');
            //SalesPlatform.ru end
            
            //$accountsInstance->addLink('DETAILVIEWBASIC', 'LBL_ACCOUNTS_ADD_ACT',
            //        'index.php?module=Act&action=EditView&return_module=Accounts&return_action=DetailView&convertmode=accountstoact&createmode=link&return_id=$RECORD$&parent_id=$RECORD$&parenttab=Sales',
            //        'themes/images/actionGenerateInvoice.gif');
            // SalesPlatform.ru end        

            // SalesPlatform.ru end

            $accountsInstance->setRelatedlist($actInstance,'Act',array(ADD),'get_dependents_list');
                        
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
            $filename = "modules/Act/pdftemplates/act.htm";
            $handle = fopen($filename, "r");
            $body = fread($handle, filesize($filename));
            fclose($handle);

            $templatename = 'Акт';
            $header_size = 50;
            $footer_size = 50;
            $page_orientation = 'P';
            $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $moduleName));
            if($adb->num_rows($res) == 0) {
                $templateid = $adb->getUniqueID('sp_templates');
                $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                $adb->pquery($sql, $params);
            } else {
                $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $moduleName);
                $adb->pquery($sql, $params);
            }

            $filename = "modules/Act/pdftemplates/actwtax.htm";
            $handle = fopen($filename, "r");
            $body = fread($handle, filesize($filename));
            fclose($handle);

            $templatename = 'Акт с НДС';
            $header_size = 50;
            $footer_size = 50;
            $page_orientation = 'P';
            $res = $adb->pquery('select * from sp_templates where name=? and module=?', array($templatename, $moduleName));
            if($adb->num_rows($res) == 0) {
                $templateid = $adb->getUniqueID('sp_templates');
                $sql = "insert into sp_templates (name,module,template,header_size,footer_size,page_orientation,templateid) values (?,?,?,?,?,?,?)";
                $params = array($templatename, $moduleName, $body, $header_size, $footer_size, $page_orientation, $templateid);
                $adb->pquery($sql, $params);
            } else {
                $sql = "update sp_templates set template=?, header_size=?, footer_size=?, page_orientation=? where name=? and module=?";
                $params = array($body, $header_size, $footer_size, $page_orientation, $templatename, $moduleName);
                $adb->pquery($sql, $params);
            }
            
            $accountsInstance = Vtiger_Module::getInstance('Accounts');
            $actInstance = Vtiger_Module::getInstance('Act');
            $accountsInstance->setRelatedlist($actInstance,'Act',array(ADD),'get_dependents_list');
            
		}
 	}
    
    //SalesPlatform.ru begin
    /*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		$createRecords = createRecords($obj);
		return $createRecords;
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		$entityInfo = importRecord($obj, $inventoryFieldData, $lineItemDetails);
		return $entityInfo;
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		$statusCount = getImportStatusCount($obj);
		return $statusCount;
	}
    
    /** 
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export PurchaseOrder Query.
	*/
	function create_export_query($where) {
		global $current_user;

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Act", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);

		$query = 
            "SELECT $fields_list FROM " . $this->entity_table . "
            INNER JOIN vtiger_sp_act ON vtiger_sp_act.actid = vtiger_crmentity.crmid
            INNER JOIN vtiger_sp_actcf ON vtiger_sp_actcf.actid = vtiger_sp_act.actid
            INNER JOIN vtiger_sp_actbillads ON vtiger_sp_actbillads.actbilladdressid = vtiger_sp_act.actid
            INNER JOIN vtiger_sp_actshipads ON vtiger_sp_actshipads.actshipaddressid = vtiger_sp_act.actid
            LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_sp_act.actid
            LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
            LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
            LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sp_act.contactid
            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_sp_act.accountid
            LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_sp_act.salesorderid
            LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_sp_act.currency_id
            LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Act', $current_user);
		$whereAuto = " vtiger_crmentity.deleted=0";

		if($where != "") {
			$query .= " where ($where) AND ".$whereAuto;
		} else {
			$query .= " where ".$whereAuto;
		}

		return $query;
	}
    //SalesPlatform.ru end
}

?>