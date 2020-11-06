<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_Provider_Model extends Settings_Vtiger_Record_Model {
    
    const PROVIDERS_TABLE_NAME = 'sp_tips_providers';
    
    private $settingsMap = [];
    
    public function getId() {
        return $this->get('provider_id');
    }

    public function getName() {
        return $this->get('provider_name');
    }
    
    public function getSettingsMap() {
        return $this->settingsMap;
    }
    
    public function getSettingsFields() {
        return array_keys($this->settingsMap);
    }
    
    public function setSetting($name, $value) {
        $this->settingsMap[$name] = $value;
    }
    
    public function save() {
        $id = $this->getId();
        if($id == null) {
            throw new AppException(vtranslate('LBL_USUPPORTED_CREATE'));
        }
        
        $this->serializeSettings();
        $db = PearDatabase::getInstance();
        $db->pquery(
            "UPDATE " . Settings_SPTips_Provider_Model::PROVIDERS_TABLE_NAME . " SET settings=? WHERE provider_id=?", 
            [$this->get('settings'), $id]
        );
    }
    
    /**
     * 
     * @return \SPTips_DaDataProvider_Model|\SPTips_Google_Model
     * @throws AppException
     */
    public function getConcreteRealization() {
        switch($this->getName()) {
            case 'DaData':
                return new SPTips_DaDataProvider_Model($this->settingsMap);
                
            case 'Google':
                return new SPTips_GoogleProvider_Model($this->settingsMap);
            
            default:
                throw new AppException('Unsupported provider');
        }
    }
    
    /**
     * 
     * @param integer $providerId
     * @return \Settings_SPTips_Provider_Model
     * @throws AppException
     */
    public static function getInstanceById($providerId) {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM ' . Settings_SPTips_Provider_Model::PROVIDERS_TABLE_NAME . ' WHERE provider_id = ?';
        $result = $db->pquery($sql, array($providerId));
        
        if(!$result) {
            throw new AppException(vtranslate('LBL_DATABASE_QUERY_ERROR'));
        }
        
        $instance = null;
        $resultRow = $db->fetchByAssoc($result);
        if($resultRow != null) {
            $instance = new Settings_SPTips_Provider_Model();
            $instance->setData($resultRow);
            $instance->initSettingsAfterFind();
        }

        return $instance;
    }
    
    /**
     * 
     * @return \Settings_SPTips_Provider_Model[]
     * @throws AppException
     */
    public static function getAll() {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM ' . Settings_SPTips_Provider_Model::PROVIDERS_TABLE_NAME;
        $result = $db->query($sql);
        
        if(!$result) {
            throw new AppException(vtranslate('LBL_DATABASE_QUERY_ERROR'));
        }
        
        $providersList = [];
        while($resultRow = $db->fetchByAssoc($result)) {
            $instance = new Settings_SPTips_Provider_Model();
            $instance->setData($resultRow);
            $instance->initSettingsAfterFind();
            $providersList[] = $instance;
        }
        
        return $providersList;
    }
    
    private function serializeSettings() {
        $this->set('settings', json_encode($this->settingsMap));
    }
    
    private function initSettingsAfterFind() {
        $this->settingsMap = json_decode(decode_html($this->get('settings')), true);
        if($this->settingsMap == null) {
            $this->settingsMap = [];
        }
    }
}