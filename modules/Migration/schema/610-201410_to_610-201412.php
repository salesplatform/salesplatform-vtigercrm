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
include_once 'include/utils/utils.php';

if(defined('VTIGER_UPGRADE')) {
    // New module
    installVtlibModule('SPKladr', 'packages/vtiger/optional/SPKladr.zip');

    // Begin Add new config value
    $file = 'config.inc.php';
    // Delete end php file symbol
    $origContent = file_get_contents($file);
    if(strpos($origContent, 'crm_user_phones') === false) {
        $origContent = str_replace('?>', '', $origContent);
        file_put_contents($file, $origContent);
        // Add new config
        $newContent = "// SalesPlatform.ru begin Additional phones\n\$crm_user_phones = array();\n// SalesPlatform.ru end";
        file_put_contents($file, $newContent, FILE_APPEND | LOCK_EX);
        // End
    }
}

global $adb;

//Unlinking unwanted resources when migrating
$unWanted = array(
    "layouts/vlayout/modules/SPSocialConnector/EnterURL.tpl",
    "modules/SPSocialConnector/actions/AuthWindow.php",
    "modules/SPSocialConnector/hybridauth/Hybrid/Providers/MySpace.php",
    "modules/SPSocialConnector/SPSocialConnectorStatusWidget.php"
);

for($i=0;$i<=count($unWanted);$i++){
    if(file_exists($unWanted[$i])){
        unlink($unWanted[$i]);
    }
}

// Begin S002_fix_socialconnector.sql
$query = 'SELECT tabid FROM vtiger_tab where name = ?';
$result = $adb->pquery($query, array('SPSocialConnector'));
$tabid = $adb->query_result($result, 0 ,'tabid');
Migration_Index_View::ExecuteQuery('UPDATE vtiger_relatedlists SET actions=? WHERE tabid=? AND label=?',
    array('', $tabid, 'Accounts'));
Migration_Index_View::ExecuteQuery('UPDATE vtiger_relatedlists SET actions=? WHERE tabid=? AND label=?',
    array('', $tabid, 'Contacts'));
Migration_Index_View::ExecuteQuery('UPDATE vtiger_relatedlists SET actions=? WHERE tabid=? AND label=?',
    array('', $tabid, 'Leads'));
// End
