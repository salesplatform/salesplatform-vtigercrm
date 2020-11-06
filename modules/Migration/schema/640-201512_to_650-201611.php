<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

//SalesPlatform.ru begin #3829
installVtlibModule('Search', 'packages/vtiger/optional/Search.zip');
//SalesPlatform.ru end
if(defined('VTIGER_UPGRADE')) {

//Start add new currency - 'CFP Franc or Pacific Franc' 
global $adb;

Vtiger_Utils::AddColumn('vtiger_portalinfo', 'cryptmode', 'varchar(20)');
$adb->pquery("ALTER TABLE vtiger_portalinfo MODIFY COLUMN user_password varchar(255)", array());

//Updating existing users password to thier md5 hash
$portalinfo_hasmore = true;
do {
	$result = $adb->pquery('SELECT id, user_password FROM vtiger_portalinfo WHERE cryptmode is null limit 1000', array());
	
	$portalinfo_hasmore = false; // assume we are done.
	while ($row = $adb->fetch_array($result)) {
		$portalinfo_hasmore = true; // we found at-least one so there could be more.
		
		$enc_password = Vtiger_Functions::generateEncryptedPassword(decode_html($row['user_password']));
		$adb->pquery('UPDATE vtiger_portalinfo SET user_password=?, cryptmode = ? WHERE id=?', array($enc_password, 'CRYPT', $row['id']));
	}
	
} while ($portalinfo_hasmore);

//Change column type of inventory line-item comment.
$adb->pquery("ALTER TABLE vtiger_inventoryproductrel MODIFY COLUMN comment TEXT", array());


// Initlize mailer_queue tables.
include_once 'vtlib/Vtiger/Mailer.php';
$mailer = new Vtiger_Mailer();
$mailer->__initializeQueue();

//set settings links, fixes translation issue on migrations from 5.x
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Users&parent=Settings&view=List' where name='LBL_USERS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Roles&parent=Settings&view=Index' where name='LBL_ROLES'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Profiles&parent=Settings&view=List' where name='LBL_PROFILES'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Groups&parent=Settings&view=List' where name='USERGROUPLIST'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=SharingAccess&parent=Settings&view=Index' where name='LBL_SHARING_ACCESS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=FieldAccess&parent=Settings&view=Index' where name='LBL_FIELDS_ACCESS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=LoginHistory&parent=Settings&view=List' where name='LBL_LOGIN_HISTORY_DETAILS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=ModuleManager&parent=Settings&view=List' where name='VTLIB_LBL_MODULE_MANAGER'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Picklist&view=Index' where name='LBL_PICKLIST_EDITOR'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=PickListDependency&view=List' where name='LBL_PICKLIST_DEPENDENCY_SETUP'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=MenuEditor&parent=Settings&view=Index' where name='LBL_MENU_EDITOR'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Settings&view=listnotificationschedulers&parenttab=Settings' where name='NOTIFICATIONSCHEDULERS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Settings&view=listinventorynotifications&parenttab=Settings' where name='INVENTORYNOTIFICATION'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Vtiger&view=CompanyDetails' where name='LBL_COMPANY_DETAILS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Vtiger&view=OutgoingServerDetail' where name='LBL_MAIL_SERVER_SETTINGS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Currency&view=List' where name='LBL_CURRENCY_SETTINGS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Vtiger&parent=Settings&view=TaxIndex' where name='LBL_TAX_SETTINGS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Settings&submodule=Server&view=ProxyConfig' where name='LBL_SYSTEM_INFO'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Vtiger&view=AnnouncementEdit' where name='LBL_ANNOUNCEMENT'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Settings&action=DefModuleView&parenttab=Settings' where name='LBL_DEFAULT_MODULE_VIEW'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=Vtiger&view=TermsAndConditionsEdit' where name='INVENTORYTERMSANDCONDITIONS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Vtiger&parent=Settings&view=CustomRecordNumbering' where name='LBL_CUSTOMIZE_MODENT_NUMBER'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?parent=Settings&module=MailConverter&view=List' where name='LBL_MAIL_SCANNER'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Workflows&parent=Settings&view=List' where name='LBL_LIST_WORKFLOWS'", array());
$adb->pquery("Update vtiger_settings_field set linkto='index.php?module=Vtiger&parent=Settings&view=ConfigEditorDetail' where name='LBL_CONFIG_EDITOR'", array());

// Extend description data-type (eg. allow large emails to be stored)
$adb->pquery("ALTER TABLE vtiger_crmentity MODIFY COLUMN description MEDIUMTEXT", array());

}


//SalesPlatform.ru migration begin
if (defined('VTIGER_UPGRADE')) {
    
    /* Change 1 - set default spcomapany picklist value */
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_field` SET `defaultvalue`='Default' WHERE `fieldname`='spcompany' AND `columnname`='spcompany'", array());
    
    /* Change 2 - fix action for Payments dependent list */
    Migration_Index_View::ExecuteQuery("UPDATE `vtiger_relatedlists` SET `actions`='ADD' WHERE `related_tabid`=(SELECT DISTINCT `tabid` FROM `vtiger_tab` WHERE `name`='SPPayments') AND `name`='get_dependents_list'", array());

    // Begin Fix Webforms tables
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms COLLATE utf8_unicode_ci', array());
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms_field COLLATE utf8_unicode_ci', array());
    // End
    
    $result = $adb->pquery("SELECT tabid FROM vtiger_tab WHERE name IN(?,?)", array('SPUnits', 'SPPayments'));
    while($resultRow = $adb->fetchByAssoc($result)) {
        Migration_Index_View::ExecuteQuery('DELETE FROM vtiger_customerportal_tabs WHERE tabid=?;', array($resultRow['tabid']));
        Migration_Index_View::ExecuteQuery('DELETE FROM vtiger_customerportal_prefs WHERE tabid=?;', array($resultRow['tabid']));
    }

    /* Change type of template field in SPPDFTemplates module*/
    Migration_Index_View::ExecuteQuery('ALTER TABLE `sp_templates` MODIFY template mediumtext', array());
}
//SalesPlatform.ru migration end



/* Vtiger merge 01.11.16 migration */
if(defined('VTIGER_UPGRADE')) {
	global $adb, $current_user;

	// Migration for - #141 - Separating Create/Edit into 2 separate Role/Profile permissions
	$actionMappingResult = $adb->pquery('SELECT 1 FROM vtiger_actionmapping WHERE actionname=?', array('CreateView'));
	if (!$adb->num_rows($actionMappingResult)) {
		$adb->pquery('INSERT INTO vtiger_actionmapping VALUES(?, ?, ?)', array(7, 'CreateView', 0));
	}

	$createActionResult = $adb->pquery('SELECT * FROM vtiger_profile2standardpermissions WHERE operation=?', array(1));
	$query = 'INSERT INTO vtiger_profile2standardpermissions VALUES';
	while($rowData = $adb->fetch_array($createActionResult)) {
		$tabId			= $rowData['tabid'];
		$profileId		= $rowData['profileid'];
		$permissions	= $rowData['permissions'];
		$query .= "('$profileId', '$tabId', '7', '$permissions'),";
	}
	$adb->pquery(trim($query, ','), array());

	require_once 'modules/Users/CreateUserPrivilegeFile.php';
	$usersResult = $adb->pquery('SELECT id FROM vtiger_users', array());
	$numOfRows = $adb->num_rows($usersResult);
	$userIdsList = array();
	for($i=0; $i<$numOfRows; $i++) {
		$userId = $adb->query_result($usersResult, $i, 'id');
		createUserPrivilegesfile($userId);
	}

	echo '<br>#141 - Successfully updated create and edit permissions<br>';

	// Migration for - #117 - Convert lead field mapping NULL values and redundant rows
	$phoneFieldId = getFieldid(getTabid('Leads'), 'phone');
	$adb->pquery('UPDATE vtiger_convertleadmapping SET editable=? WHERE leadfid=?', array(1, $phoneFieldId));

	// Migration for #261 - vtiger_portalinfo doesn't update contact
	$result = $adb->pquery('SELECT workflow_id FROM com_vtiger_workflows WHERE test LIKE ? AND module_name=? AND defaultworkflow=?', array('%portal%', 'Contacts', 1));
	if ($adb->num_rows($result) == 1) {
		$workflowId = $adb->query_result($result, 0, 'workflow_id');
		$workflowModel = Settings_Workflows_Record_Model::getInstance($workflowId);
		$workflowModel->set('execution_condition', 3);
		$conditions = array(
			array(
				'fieldname' => 'portal',
				'operation' => 'is',
				'value' => '1',
				'valuetype' => 'rawtext',
				'joincondition' => 'and',
				'groupjoin' => 'and',
				'groupid' => '0'
			),
			array(
				'fieldname' => 'email',
				'operation' => 'has changed',
				'value' => '',
				'valuetype' => 'rawtext',
				'joincondition' => 'and',
				'groupjoin' => 'and',
				'groupid' => '0',
			),
			array(
				'fieldname' => 'email',
				'operation' => 'is not empty',
				'value' => '',
				'valuetype' => 'rawtext',
				'joincondition' => '',
				'groupjoin' => 'and',
				'groupid' => '0'
			)
		);
		$workflowModel->set('conditions', $conditions);
		$workflowModel->set('filtersavedinnew', 6);
		$workflowModel->save();
		echo '<b>"#261 - vtiger_portalinfo doesnt update contact"</b> fixed';
	}
}