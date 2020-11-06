<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

/**
 * Describes settings of website exchange.
 */
class SiteExchangeSettings {
    
    private $connector;
    
    public function __construct() {
        global $adb;
        $this->connector = $adb;
    }
    
    private function translate($string) {
        global $mod_strings;
        return $mod_strings["$string"];
    }
    
    /**
     * Returns login of website administrator from exchange control table.
     * @return String
     */
    public function getAdminLogin() {
        $result = $this->connector->pquery(
                "select * from sp_cml_site_settings where `key`='adminLogin'",
                array());
        $login = $this->connector->query_result($result,0,'value');
        return $login;
    }
    
    /**
     * Updates administratot login in exchange control table.
     * @param String $login
     */
    public function setAdminLogin($login) {
        $this->connector->pquery("UPDATE `sp_cml_site_settings` SET
            `value`= '$login' where `key`='adminLogin'", array());
    }
    
    /**
     * Return website admin password.
     * @return String
     */
    public function getAdminPassword() {
        $result = $this->connector->pquery(
                "select * from sp_cml_site_settings where `key`='adminPassword'",
                array());
        $password = $this->connector->query_result($result,0,'value');
        return $password;
    }
    
    /**
     * Updates website admin password.
     * @param String $password
     */
    public function setAdminPassword($password) {
        $this->connector->pquery("UPDATE `sp_cml_site_settings` SET
            `value`= '$password' where `key`='adminPassword'", array());
    }
    
    /**
     * Return website URI.
     * @return String
     */
    public function getSiteUrl() {
        $result = $this->connector->pquery(
                "select * from sp_cml_site_settings where `key`='siteURL'",
                array());
        $url = $this->connector->query_result($result,0,'value');
        return $url;
    }
    
    /**
     * Updates website URI.
     * @param String $url
     */
    public function setSiteUrl($url) {
        $this->connector->pquery("UPDATE `sp_cml_site_settings` SET
            `value`= '$url' where `key`='siteURL'", array());
    }
    
    /**
     * Return id of user, which will be assigned on new entity create.
     * @return int
     */
    public function getAssignedUser() {
        $result = $this->connector->pquery(
                "select * from sp_cml_site_settings where `key`='assignedUser'",
                array());
        $user = $this->connector->query_result($result,0,'value');
        return $user;
    }
    
    /**
     * Set user id, which will be assigned on new entity create.
     * @param type $userId
     */
    public function setAssignedUser($user) {
        $this->connector->pquery("UPDATE `sp_cml_site_settings` SET
            `value`= '$user' where `key`='assignedUser'", array());
    }
    
    /**
     * Return all statuses settings as array.
     * @return array<String>
     */
    public function getStatusesSettings() {
        global $mod_strings;
        $queryResult = $this->connector->pquery(
                "select `id`, `key`, `value` from sp_cml_site_settings where `setting_type`='statusParam'",
                array());
        $statusesSettings = array();
        
        //save all records
        while($setting = $this->connector->fetchByAssoc($queryResult)) {
            
            //russification - no very vell solution
            $setting['key'] = $this->translate($setting['key']);
            
            array_push($statusesSettings, $setting);
        }
        
        return $statusesSettings;
    }
    
    /**
     * Returns CRM status by record id.
     * @param int $recordId
     * @return String
     */
    public function getCrmStatusById($recordId) {
        $queryResult = $this->connector->pquery(
                "select `key` from sp_cml_site_settings where `id`='$recordId'",
                array());
        
        $crmStatus = $this->connector->query_result($queryResult,0,'key'); 
        return $crmStatus;
    }
    
    /**
     * Returns site status by record id.
     * @param int $recordId
     * @return String
     */
    public function getSiteStatusById($recordId) {
        $queryResult = $this->connector->pquery(
                "select `value` from sp_cml_site_settings where `id`='$recordId'",
                array());
        
        $siteStatus = $this->connector->query_result($queryResult,0,'value'); 
        return $siteStatus;
    }
    
    /**
     * Updates status record by $recordId.
     * @param String $recordId
     * @param String $crmStatus
     * @param String $siteStatus
     */
    public function updateStatusSetting($recordId, $crmStatus, $siteStatus) {
        $this->connector->pquery("UPDATE `sp_cml_site_settings` SET
            `key`='$crmStatus', `value`='$siteStatus' where `id`='$recordId'", array());
    }
    
    /**
     * Creates new status record.
     * @param String $crmStatus
     * @param String $siteStatus
     */
    public function createStatusSetting($crmStatus, $siteStatus) {
        $this->connector->pquery("INSERT INTO `sp_cml_site_settings` (`setting_type`,`key`,`value`)"
                . " VALUES ('statusParam','$crmStatus','$siteStatus')",array());
                
    }
    
    /**
     * Deletes status record by it's $id.
     * @param int $recordId
     */
    public function deleteStatusSetting($recordId) {
        $this->connector->pquery("DELETE FROM `sp_cml_site_settings` WHERE `id`='$recordId'",array());
    }
    
    public function getCrmStatusBySite($siteStatus) {
        $queryResult = $this->connector->pquery("select `key` from `sp_cml_site_settings` "
                . "where `value`='$siteStatus' and `setting_type`='statusParam'",array());
        if($queryResult == false) {
            return null;
        }
        
        $crmStatus = $this->connector->query_result($queryResult, 0, 'key'); 
        return $crmStatus;
    }
}
