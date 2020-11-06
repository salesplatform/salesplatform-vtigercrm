<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once 'modules/SPSocialConnector/SPSocialConnectorHelper.php';

class Settings_SPSocialConnector_Record_Model extends Settings_Vtiger_Record_Model {

    const TABLE_NAME = 'vtiger_sp_socialconnector_settings';
    
    public function getId() {
    }

    public function getName() {
    }
    
    public function getModule(){
        return new Settings_SPSocialConnector_Module_Model;
    }
    
    static function getCleanInstance(){
        return new self;
    }
    
     public static function getInstance(){
        $model = new self();
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM '.self::TABLE_NAME;
        $result = $db->pquery($query, array());
        $resultCount = $db->num_rows($result);
        if($resultCount > 0) {
            for ($i = 0; $i < $resultCount; $i++) {
                $key = $db->query_result($result, $i,'key');
                $value = $db->query_result($result, $i,'value');
                $model->set($key, $value);
            }
            return $model;
        }
        return $model;
    }
    
    public function save() {
		$db = PearDatabase::getInstance();
        $parameters = '';
        $model = new Settings_SPSocialConnector_Module_Model;
        foreach ($model->getSettingsParameters() as $field => $type) {
            $query = 'UPDATE '.self::TABLE_NAME.' SET `value` = ? WHERE `key` = ?';
            $db->pquery($query, array($this->get($field), $field));
            $parameters[$field] = $this->get($field);
        }
        
        SPSocialConnectorHelper::generateHybridAuthConfig($parameters);
	}
}