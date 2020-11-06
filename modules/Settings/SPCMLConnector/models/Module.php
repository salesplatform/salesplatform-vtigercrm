<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_Module_Model extends Settings_Vtiger_Module_Model {
     
    var $baseTable = 'sp_cml_site_settings';
    var $listFields = array('key' => 'Crm Status', 'value' => 'Site Status');
    var $name = 'SPCMLConnector';
    
    /**
     * Function return method to create new status record
     * @return string
     */
    public function getCreateRecordUrl() {
        return "javascript:Settings_SPCMLConnector_List_Js.triggerAdd(event)";
    }
    
    /**
     * Returns login of website administrator from exchange control table.
     * @return String
     */
    public function getAdminLogin() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery(
                "select * from sp_cml_site_settings where `key`='adminLogin'",
                array());
        return $db->query_result($result,0,'value');
    }
    
    /**
     * Updates administratot login in exchange control table.
     * @param String $login
     */
    public function setAdminLogin($login) {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE `sp_cml_site_settings` SET
            `value`= '$login' where `key`='adminLogin'");
    }
    
    /**
     * Return website admin password.
     * @return String
     */
    public function getAdminPassword() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery(
                "select * from sp_cml_site_settings where `key`='adminPassword'",
                array());
        return $db->query_result($result,0,'value');
    }
    
    /**
     * Updates website admin password.
     * @param String $password
     */
    public function setAdminPassword($password) {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE `sp_cml_site_settings` SET
            `value`= '$password' where `key`='adminPassword'");
    }
    
    /**
     * Return website URI.
     * @return String
     */
    public function getSiteUrl() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select * from sp_cml_site_settings where `key`='siteURL'",
                array());
        return $db->query_result($result,0,'value');
    }
    
    /**
     * Updates website URI.
     * @param String $url
     */
    public function setSiteUrl($url) {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE `sp_cml_site_settings` SET
            `value`= '$url' where `key`='siteURL'");
    }
    
    /**
     * Return id of user, which will be assigned on new entity create.
     * @return String
     */
    public function getAssignedUser() {
        $db = PearDatabase::getInstance();
        $result = $db->query("select * from sp_cml_site_settings where `key`='assignedUser'");
        return $db->query_result($result,0,'value');
    }
    
    /**
     * Set user id, which will be assigned on new entity create.
     * @param type $userId
     */
    public function setAssignedUser($user) {
        $db = PearDatabase::getInstance();
        $db->query("UPDATE `sp_cml_site_settings` SET
            `value`= '$user' where `key`='assignedUser'");
    }
}
