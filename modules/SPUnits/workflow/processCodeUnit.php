<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');

function processCodeUnit($ws_entity) {
    global $adb;
    $data = array(
        "Products" => array("tablename" => "vtiger_products",
                            "id" => "productid",
                            "column" => "usageunit"),
        "Services" => array("tablename" => 'vtiger_service',
                            "id" => "serviceid",
                            "column" => "service_usageunit"),
        );
    $ws_id = $ws_entity->getId();
    $module = $ws_entity->getModuleName();
    if (empty($ws_id) || empty($module)) {
        return;
    }
    $crmid = vtws_getCRMEntityId($ws_id);
    if ($crmid <= 0) {
        return;
    }
    $entity = CRMEntity::getInstance($module);
    $entity->id = $crmid;
    $entity->retrieve_entity_info($crmid, $module);
    $units_res = $adb->pquery("select unit_code from sp_units ".
                                "where usageunit = ?",
                                array($entity->column_fields[$data[$module]['column']]));
    if ($units_res) {
        if ($adb->num_rows($units_res) > 0) {
            $unit_code = $adb->query_result($units_res, 0, "unit_code");
            $adb->pquery("update ".$data[$module]['tablename']." ".
                            "set unit_code = ? ".
                            "where ".$data[$module]['id']." = ?", 
            array($unit_code, $crmid));
        }
    }  
}

?>
