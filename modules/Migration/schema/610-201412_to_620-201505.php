<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
if (!defined('VTIGER_UPGRADE'))
    die('Invalid entry point');
chdir(dirname(__FILE__) . '/../../../');
include_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
include_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
include_once 'include/utils/utils.php';

if(defined('VTIGER_UPGRADE')) {
     updateVtlibModule('Google', 'packages/vtiger/optional/Google.zip');
}
if(defined('INSTALLATION_MODE')) {
		// Set of task to be taken care while specifically in installation mode.
}

// SalesPlatform.ru begin

//Unlink unwanted resources
$unWanted = array(
    "modules/ModComments/language/en_us.lang.php",
    "modules/SMSNotifier/providers/StreamSMSru.php",
    "modules/SMSNotifier/providers/StreamTelecomRuUr.php",
    "modules/SMSNotifier/providers/streamsms/SPSTREAMSMS.Class.v2.1.php",
    "modules/SMSNotifier/providers/streamsms/STREAMSMS.Class.v2.1.php",
    "modules/SMSNotifier/providers/streamsms/STREAMSMSOld.Class.php"
);

for($i = 0; $i <= count($unWanted); $i++){
    if(file_exists($unWanted[$i])){
        unlink($unWanted[$i]);
    }
}

global $adb;
$query = 'SELECT tabid FROM vtiger_tab where name = ?';
$result = $adb->pquery($query, array('Accounts'));
$tabId = $adb->query_result($result, 0 ,'tabid');
Migration_Index_View::ExecuteQuery('UPDATE vtiger_relatedlists SET name=? WHERE tabid=? AND label=?',
    array('get_dependents_list', $tabId, 'Act'));
Migration_Index_View::ExecuteQuery('UPDATE vtiger_relatedlists SET name=? WHERE tabid=? AND label=?',
    array('get_dependents_list', $tabId, 'Consignment'));

// Begin creating Workflow for Updating Inventory Stock for Invoice
$checkQuery = "SELECT * FROM com_vtiger_workflows WHERE module_name = ? AND summary = ?";
$result = $adb->pquery($checkQuery, array('Invoice', 'UpdateInventoryProducts On Every Save'));
if($adb->num_rows($result) < 1) {
    $vtWorkFlow = new VTWorkflowManager($adb);
    $invWorkFlow = $vtWorkFlow->newWorkFlow("Invoice");
    $invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
    $invWorkFlow->description = vtranslate('LBL_UPDATE_INVENTORY_PRODUCTS','Install');
    $invWorkFlow->defaultworkflow = 1;
    $vtWorkFlow->save($invWorkFlow);
}

// Begin
$query = 'SELECT blockid FROM vtiger_settings_blocks WHERE label = ?';
$result = $adb->pquery($query, array('LBL_INTEGRATION'));
$blockId = $adb->query_result($result, 0 ,'blockid');
Migration_Index_View::ExecuteQuery('UPDATE vtiger_settings_field SET blockid = ? WHERE name = ? OR name = ?',
    array($blockId, 'LBL_CML_SETTINGS', 'LBL_SOCIALCONNECTOR_SETTINGS'));

// Begin
$query = 'SELECT tabid FROM vtiger_tab where name = ?';
$result = $adb->pquery($query, array('PBXManager'));
$tabId = $adb->query_result($result, 0 ,'tabid');
Migration_Index_View::ExecuteQuery('UPDATE vtiger_field SET fieldname = ? WHERE tabid = ? AND fieldname = ?',
    array('createdtime', $tabId, 'CreatedTime'));
Migration_Index_View::ExecuteQuery('UPDATE vtiger_field SET fieldname = ? WHERE tabid = ? AND fieldname = ?',
    array('modifiedtime', $tabId, 'ModifiedTime'));

// Begin
$relModulesArray = array('Contacts', 'Leads','Accounts');

$query = 'SELECT tabid FROM vtiger_tab where name = ?';
$result = $adb->pquery($query, array('PBXManager'));
$tabId = $adb->query_result($result, 0 ,'tabid');

$relatedToQuery = "SELECT fieldid FROM vtiger_field WHERE tabid=? AND fieldname=?";
$result = $adb->pquery($relatedToQuery, array($tabId, 'customer'));
$fieldId = $adb->query_result($result,0, 'fieldid');

$insertQuery = "INSERT INTO vtiger_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES(?,?,?,?,?)";
foreach ($relModulesArray as $relModule) {
    $checkQuery = "SELECT * FROM vtiger_fieldmodulerel WHERE module = ? AND relmodule = ?";
    $result = $adb->pquery($checkQuery, array('PBXManager', $relModule));
    if($adb->num_rows($result)) {
        continue;
    }
    Migration_Index_View::ExecuteQuery($insertQuery, array($fieldId, 'PBXManager', $relModule, NULL, NULL));
}

$query = 'SELECT tabid FROM vtiger_tab where name = ?';
$result = $adb->pquery($query, array('SPSocialConnector'));
$tabId = $adb->query_result($result, 0 ,'tabid');
$updateQuery = "UPDATE vtiger_relatedlists SET actions = ' ' WHERE related_tabid = ?";
Migration_Index_View::ExecuteQuery($updateQuery,array($tabId));

// Delete unnecessary lines
$file = 'config.inc.php';
$origContent = file_get_contents($file);
if(strpos($origContent, '// SalesPlatform.ru begin Additional phones')) {
    $origContent = str_replace('// SalesPlatform.ru begin Additional phones', '', $origContent);
    file_put_contents($file, $origContent);
}
if(strpos($origContent, '$crm_user_phones = array();')) {
    $origContent = str_replace('$crm_user_phones = array();', '', $origContent);
    file_put_contents($file, $origContent);
}
if(strpos($origContent, '// SalesPlatform.ru end')) {
    $origContent = str_replace('// SalesPlatform.ru end', '', $origContent);
    file_put_contents($file, $origContent);
}

// SalesPlatform.ru end

//Handle migration for http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7552--senotesrel
$seDeleteQuery="DELETE from vtiger_senotesrel WHERE crmid NOT IN(select crmid from vtiger_crmentity)";
Migration_Index_View::ExecuteQuery($seDeleteQuery,array());
$seNotesSql="ALTER TABLE vtiger_senotesrel ADD CONSTRAINT fk1_crmid FOREIGN KEY(crmid) REFERENCES vtiger_crmentity(crmid) ON DELETE CASCADE";
Migration_Index_View::ExecuteQuery($seNotesSql,array());

//Update uitype of created_user_id field of vtiger_field from 53 to 52
$updateQuery = "UPDATE vtiger_field SET uitype = 52 WHERE fieldname = 'created_user_id'";
Migration_Index_View::ExecuteQuery($updateQuery,array());

/*141*/
//registering handlers for Google sync 
require_once 'includes/main/WebUI.php';
require_once 'modules/WSAPP/Utils.php'; 
require_once 'modules/Google/connectors/Config.php';
wsapp_RegisterHandler('Google_vtigerHandler', 'Google_Vtiger_Handler', 'modules/Google/handlers/Vtiger.php'); 
wsapp_RegisterHandler('Google_vtigerSyncHandler', 'Google_VtigerSync_Handler', 'modules/Google/handlers/VtigerSync.php'); 

//updating Google Sync Handler names 
$db = PearDatabase::getInstance();
$names = array('Vtiger_GoogleContacts', 'Vtiger_GoogleCalendar'); 
$result = $db->pquery("SELECT stateencodedvalues FROM vtiger_wsapp_sync_state WHERE name IN (".  generateQuestionMarks($names).")", array($names)); 
$resultRows = $db->num_rows($result); 
$appKey = array(); 
for($i=0; $i<$resultRows; $i++) { 
        $stateValuesJson = $db->query_result($result, $i, 'stateencodedvalues'); 
        $stateValues = Zend_Json::decode(decode_html($stateValuesJson)); 
        $appKey[] = $stateValues['synctrackerid']; 
}

if(!empty($appKey)) { 
    $sql = 'UPDATE vtiger_wsapp SET name = ? WHERE appkey IN ('.  generateQuestionMarks($appKey).')'; 
    $res = Migration_Index_View::ExecuteQuery($sql, array('Google_vtigerSyncHandler', $appKey)); 
}
        
//Ends 141

//Google Calendar sync changes
/**
 * Please refer this trac (http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/8354#comment:3)
 * for configuration of vtiger to Google OAuth2
 */
global $adb;

if(!Vtiger_Utils::CheckTable('vtiger_google_oauth2')) {
    Vtiger_Utils::CreateTable('vtiger_google_oauth2',
            '(service varchar(20),access_token varchar(500),refresh_token varchar(500),userid int(19))',true);
    echo '<br> vtiger_google_oauth2 table created <br>';
}

//(start)Migrating GoogleCalendar ClientIds in wsapp_recordmapping to support v3
            
$syncTrackerIds = array();

if(Vtiger_Utils::CheckTable('vtiger_wsapp_sync_state')) {

    $sql = 'SELECT stateencodedvalues from vtiger_wsapp_sync_state WHERE name = ?';
    $result = $db->pquery($sql,array('Vtiger_GoogleCalendar'));
    $num_of_rows = $adb->num_rows($result);

    for($i=0;$i<$num_of_rows;$i++) {
        $stateEncodedValues = $adb->query_result($result,$i,'stateencodedvalues');
        $htmlDecodedStateEncodedValue = decode_html($stateEncodedValues);
        $stateDecodedValues = json_decode($htmlDecodedStateEncodedValue,true);
        if(is_array($stateDecodedValues) && isset($stateDecodedValues['synctrackerid'])) {
            $syncTrackerIds[] = $stateDecodedValues['synctrackerid'];
        }
    }

}

//$syncTrackerIds - list of all Calendar sync trackerIds

$appIds = array();

if(count($syncTrackerIds)) {

    $sql = 'SELECT appid FROM vtiger_wsapp WHERE appkey IN (' . generateQuestionMarks($syncTrackerIds) . ')';
    $result = Migration_Index_View::ExecuteQuery($sql,$syncTrackerIds);

    $num_of_rows = $adb->num_rows($result);

    for($i=0;$i<$num_of_rows;$i++) {
        $appId = $adb->query_result($result,$i,'appid');
        if($appId) $appIds[] = $appId;
    }

}

//$appIds - list of all Calendarsync appids

if(count($appIds)) {

    $sql = 'SELECT id,clientid FROM vtiger_wsapp_recordmapping WHERE appid IN (' . generateQuestionMarks($appIds) . ')';
    $result = Migration_Index_View::ExecuteQuery($sql,$appIds);

    $num_of_rows = $adb->num_rows($result);

    for($i=0;$i<$num_of_rows;$i++) {

        $id = $adb->query_result($result,$i,'id');
        $clientid = $adb->query_result($result,$i,'clientid');

        $parts = explode('/', $clientid);
        $newClientId = end($parts);

        Migration_Index_View::ExecuteQuery('UPDATE vtiger_wsapp_recordmapping SET clientid = ? WHERE id = ?',array($newClientId,$id));

    }

    echo '<br> vtiger_wsapp_recordmapping clientid migration completed for CalendarSync';

}
//(end)
            
//Google Calendar sync changes ends here

//Google migration : Create Sync setting table
$sql = 'CREATE TABLE vtiger_google_sync_settings (user int(11) DEFAULT NULL, 
    module varchar(50) DEFAULT NULL , clientgroup varchar(255) DEFAULT NULL, 
    direction varchar(50) DEFAULT NULL)';
$db->pquery($sql,array());
$sql = 'CREATE TABLE vtiger_google_sync_fieldmapping ( vtiger_field varchar(255) DEFAULT NULL,
        google_field varchar(255) DEFAULT NULL, google_field_type varchar(255) DEFAULT NULL,
        google_custom_label varchar(255) DEFAULT NULL, user int(11) DEFAULT NULL)';
$db->pquery($sql,array());
echo '<br>Google sync setting and mapping table added</br>';