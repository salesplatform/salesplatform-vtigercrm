<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once('include/utils/utils.php');

function processUsageUnit() {
    global $adb;
    $sql = $adb->pquery("select * from sp_units", array());
    if ($sql) {
        $rows = $adb->num_rows($sql);
        if ($rows > 0) {
            $picklistid_result = $adb->pquery("select picklistid from vtiger_picklist ".
                                                "where name = 'usageunit'", array());
            if ($picklistid_result) {
                if ($adb->num_rows($picklistid_result) > 0) {
                    $picklistid = $adb->query_result($picklistid_result, 0, "picklistid");
                    
                    $data_products = getUsageUnitData('vtiger_usageunit', 'usageunit');
                    $data_service = getUsageUnitData('vtiger_service_usageunit', 'service_usageunit');
                    $valueid_result = $adb->pquery("select max(id)+1 as id from vtiger_picklistvalues_seq", array());
                     
                    $picklistvalueid = $adb->query_result($valueid_result, 0, "id");
                    
                    // In vtiger600 tables vtiger_usageunit and vtiger_service_usageunit have 5 field, so need new variable 
                    $sortorderid_result = $adb->pquery("select max(sortorderid)+1 as sortorderid from vtiger_usageunit", array());
                    $sortorderid = $adb->query_result($sortorderid_result, 0, "sortorderid");
   
                    $role_result = $adb->pquery("select roleid from vtiger_role", array());
                    if ($role_result) {
                        $numrow = $adb->num_rows($role_result);
                        if ($numrow > 0) {
                            $id_pr = $adb->getUniqueID('vtiger_usageunit');
                            $id_srv = $adb->getUniqueID('vtiger_service_usageunit');
                            for ($i=0; $i<$rows; $i++) {
                                $usageunit = $adb->query_result($sql, $i, "usageunit");
                                if (!in_array($usageunit, $data_products)) {
                                    $adb->pquery("insert into vtiger_usageunit values(?,?,?,?,?,?)",
                                                    array($id_pr, $usageunit, 1, $picklistvalueid, $sortorderid, NULL));
                                    $id_pr++;
                                }
                                if (!in_array($usageunit, $data_service)) {
                                    $adb->pquery("insert into vtiger_service_usageunit values(?,?,?,?,?,?)",
                                                array($id_srv, $usageunit, 1, $picklistvalueid, $sortorderid, NULL));
                                    $id_srv++;
                                }
                                if (!in_array($usageunit, $data_products) || !in_array($usageunit, $data_service)) {                  
                                    for($k=0; $k<$numrow; $k++) {
                                        $roleid = $adb->query_result($role_result, $k,'roleid');
                                        $sortid_result = $adb->pquery("select max(sortid)+1 as sortid from vtiger_role2picklist where picklistid = ? and roleid = ?", array($picklistid, $roleid));
                                        $sortid = $adb->query_result($sortid_result, 0, "sortid");
                                        $adb->pquery("insert into vtiger_role2picklist values(?,?,?,?)",
                                                        array($roleid, $picklistvalueid, $picklistid, $sortid));
                                    }
                                    $picklistvalueid++;
                                }
                            }
                            $adb->pquery("update vtiger_usageunit_seq SET id = ?", array(--$id_pr));
                            $adb->pquery("update vtiger_service_usageunit_seq SET id = ?", array(--$id_srv));
                            $adb->pquery("update vtiger_picklistvalues_seq SET id = ?", array(--$picklistvalueid));
                        } 
                    }
                }
            }
        }
    }
}

function getUsageUnitData($tablename, $column) {
    global $adb;
    $unit_data = array();
    $i = 0;
    $unit_result = $adb->pquery("select ".$column." from ".$tablename."", array());
    while ($resultrow = $adb->fetch_array($unit_result)) {
        $unit_data[$i] = $resultrow[$column];
        $i++;
    }
    return $unit_data;
}
?>

