<?php

class Settings_SPVoipIntegration_Record_Model extends Settings_Vtiger_Record_Model {

    const settingsTable = 'vtiger_sp_voipintegration_settings';
    const defaultProvideTable = 'vtiger_sp_voip_default_provider';
    const optionsTable = 'vtiger_sp_voipintegration_options';
    
    const USE_CLICK_TO_CALL_FIELD = 'use_click_to_call';
    
    public function getId() {
        return $this->get('id');
    }

    public function getName() {
        return $this->get('id');
    }
    
    public static function isOutgoingCallsEnabled() {
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = Users_Privileges_Model::isPermitted('PBXManager', 'MakeOutgoingCalls');
        
        return $permission && self::isClickToCallEnabled();
    }
    
    public static function isClickToCallEnabled() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT value FROM " . self::optionsTable . " WHERE name=?", array(
            self::USE_CLICK_TO_CALL_FIELD
        ));
        
        if($result) {
            $resultRow = $db->fetchByAssoc($result);
            return ($resultRow != null && $resultRow['value'] == 1);
        }
        
        return false;
    }
    
    public static function getInstance() {
        $db = PearDatabase::getInstance();

        $query = 'SELECT * FROM ' . self::settingsTable;

        $instance = new Settings_SPVoipIntegration_Record_Model();

        $result = $db->query($query);
        $fieldsInfo = array();
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $fieldsInfo[$resRow['provider_name']][] = array(
                    'field_name' => $resRow['field_name'],
                    'field_label' => $resRow['field_label'],
                    'field_value' => $resRow['field_value']
                );
            }
        }
        $instance->set('fields_info', $fieldsInfo);

        $defaultProvider = self::getDefaultProvider();
        $instance->set('default_provider', $defaultProvider);
        return $instance;
    }

    public function getSettingsFieldsInfo() {
        return $this->get('fields_info');
    }

    public static function getProviders() {
        $db = PearDatabase::getInstance();
        $providers = array();
        $result = $db->pquery("SELECT DISTINCT provider_name FROM " . self::settingsTable);
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $providers[] = $resRow['provider_name'];
            }
        }

        return $providers;
    }

    public function saveSettings($request) {
        $db = PearDatabase::getInstance();
        $fieldsInfo = $this->get('fields_info');

        foreach ($fieldsInfo as $providerName => $providerFields) {
            foreach ($providerFields as $fieldInfo) {
                $db->pquery("UPDATE " . self::settingsTable . " SET field_value=? WHERE field_name=?", array(trim($request->get($fieldInfo['field_name'])), $fieldInfo['field_name']));
            }
        }
        $defaultProvider = self::getDefaultProvider();
        if (empty($defaultProvider)) {
            $db->pquery("INSERT INTO " . self::defaultProvideTable . " values(?)", array($request->get('default_provider')));
        } else {
            $db->pquery("UPDATE " . self::defaultProvideTable . " SET default_provider=?", array($request->get('default_provider')));
        }
        
        $isClickToCall = ($request->get('use_click_to_call') == "on" || $request->get('use_click_to_call') == 1);
        $db->pquery("UPDATE " . self::optionsTable . " SET value=? WHERE name=?", array(
            ($isClickToCall ? 1 : 0), self::USE_CLICK_TO_CALL_FIELD
        ));
    }

    public function getProviderFieldObject() {
        $provider = null;
        $provider['existing_providers'] = self::getProviders();
        $provider['default_provider'] = $this->get('default_provider');
        return $provider;
    }

    public static function getZadarmaSecret() {
        return self::getVoipSettingsFieldValue('zadarma_secret');
    }

    public static function getZadarmaKey() {
        return self::getVoipSettingsFieldValue('zadarma_key');
    }

    public static function getMangoSecret() {
        return self::getVoipSettingsFieldValue('mango_secret');
    }

    public static function getMangoKey() {
        return self::getVoipSettingsFieldValue('mango_key');
    }
    
    public static function getMangoAPIUrl() {
        return self::getVoipSettingsFieldValue('mango_url');
    }

    public static function getSipuniAPIUrl() {
        return self::getVoipSettingsFieldValue('sipuni_url');
    }

    public static function getSipuniId() {
        return self::getVoipSettingsFieldValue('sipuni_id');
    }

    public static function getSipuniKey() {
        return self::getVoipSettingsFieldValue('sipuni_key');
    }

    public static function getGravitelAPIUrl() {
        return self::getVoipSettingsFieldValue('gravitel_url');
    }

    public static function getGravitelToken() {
        return self::getVoipSettingsFieldValue('gravitel_key');
    }

    public static function getGravitelCrmToken() {
        return self::getVoipSettingsFieldValue('crm_key');
    }

    public static function getMegafonAPIUrl() {
        return self::getVoipSettingsFieldValue('megafon_url');
    }

    public static function getMegafonToken() {
        return self::getVoipSettingsFieldValue('megafon_key');
    }

    public static function getMegafonCrmToken() {
        return self::getVoipSettingsFieldValue('megafon_crm_key');
    }

    public static function getDomruAPIUrl() {
        return self::getVoipSettingsFieldValue('domru_url');
    }

    public static function getDomruToken() {
        return self::getVoipSettingsFieldValue('domru_key');
    }

    public static function getDomruCrmToken() {
        return self::getVoipSettingsFieldValue('domru_crm_key');
    }

    public static function getWestCallSPBAPIUrl() {
        return self::getVoipSettingsFieldValue('westcall_spb_url');
    }

    public static function getWestCallSPBToken() {
        return self::getVoipSettingsFieldValue('westcall_spb_key');
    }

    public static function getWestCallSPBCrmToken() {
        return self::getVoipSettingsFieldValue('westcall_spb_crm_key');
    }

    public static function getZadarmaIp() {
        return self::getVoipSettingsFieldValue('zadarma_ip');
    }

    public static function getZebraLogin() {
        return self::getVoipSettingsFieldValue('zebra_login');
    }

    public static function getZebraPassword() {
        return self::getVoipSettingsFieldValue('zebra_password');
    }

    public static function getZebraRealm() {
        return self::getVoipSettingsFieldValue('zebra_realm');
    }
    
    public static function getZebraApiUrl() {
        return self::getVoipSettingsFieldValue("zebra_api_url");
    }

    public static function getUIScomSecret() {
        return self::getVoipSettingsFieldValue('uiscom_access_token');
    }

    public static function getUIScomApiUrl() {
        return self::getVoipSettingsFieldValue('uiscom_api_url');
    }

    public static function getTelphinAPIUrl() {
        return self::getVoipSettingsFieldValue('telphin_api_url');
    }

    public static function getTelphinAppId() {
        return self::getVoipSettingsFieldValue('telphin_app_id');
    }

    public static function getTelphinAppSecret() {
        return self::getVoipSettingsFieldValue('telphin_app_secret');
    }

    public static function getYandexApiKey() {
        return self::getVoipSettingsFieldValue('yandex_api_key');
    }

    public static function getYandexDefaultOutgoing() {
        return self::getVoipSettingsFieldValue('yandex_default_outgoing');
    }

    public static function getYandexApiURL() {
        return self::getVoipSettingsFieldValue('yandex_api_url');
    }

    public static function getMCNApiToken() {
        return self::getVoipSettingsFieldValue('mcn_api_token');
    }

    public static function getMCNApiUrl() {
        return self::getVoipSettingsFieldValue('mcn_api_url');
    }

    public static function getMCNCrmToken() {
        return self::getVoipSettingsFieldValue('mcn_crm_token');
    }

    public static function getMCNAccountId() {
        return self::getVoipSettingsFieldValue('mcn_account_id');
    }

    public static function getMCNPBXId() {
        return self::getVoipSettingsFieldValue('mcn_pbx_id');
    }

    public static function getRostelecomSignKey() {
        return self::getVoipSettingsFieldValue('rostelecom_sign_key');
    }

    public static function getRostelecomIdentificationKey() {
        return self::getVoipSettingsFieldValue('rostelecom_identification_key');
    }

    public static function getRostelecomApiURL() {
        return self::getVoipSettingsFieldValue('rostelecom_api_url');
    }

    public static function getRostelecomSipURI() {
        return self::getRostelecomVoipSettingsFieldValue('sp_rostelecom_extension_sipiru');
    }

    public static function getYandexUserOutgoing() {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $outgoingNumber = null;
        if ($currentUser) {
            $outgoingNumber = $currentUser->get('sp_yandex_outgoing_number');
        }
        if (empty($outgoingNumber)) {
            $outgoingNumber = self::getYandexDefaultOutgoing();
        }
        return $outgoingNumber;
    }

    public static function getDefaultProvider() {
        $db = PearDatabase::getInstance();
        $defaultProvider = null;
        $result = $db->query("SELECT default_provider FROM " . self::defaultProvideTable);
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $defaultProvider = $resRow['default_provider'];
        }

        if (empty($defaultProvider)) {
            $providers = self::getProviders();
            $defaultProvider = $providers[0];
        }

        return $defaultProvider;
    }

    private static function getVoipSettingsFieldValue($fieldName) {
        $db = PearDatabase::getInstance();
        $fieldValue = null;
        $result = $db->pquery("SELECT field_value FROM " . self::settingsTable . " WHERE field_name=?", array($fieldName));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $fieldValue = $resRow['field_value'];
        }
        return $fieldValue;
    }

    /**
     * Description: header X-Client-Sign = sha256hex(client_api_id + json + client_api_key)
     * @param type $httpHeaders
     * @return boolean
     */
    public static function checkRostelecomSignRequest($clientId, $headerSign) {
        //get json from raw data
        $postData = file_get_contents("php://input");
        $signKey = Settings_SPVoipIntegration_Record_Model::getRostelecomSignKey();
        $dataNeedToSign = $clientId . $postData . $signKey;
        $sign = Settings_SPVoipIntegration_Record_Model::signRostelecomData($dataNeedToSign);
        if ($sign == $headerSign) {
            return true;
        }
        return false;
    }

    /*
     * IF $signResponse == true: from CRM to Rostelecom
     * Need to sign request from CRM to Rostelecom
     * IF $signResponse == false: from Rostelecom to CRM
     */

    public static function signRostelecomData($data, $signResponse = false) {
        // request from CRM to Rostelecom
        if ($signResponse) {
            $clientId = Settings_SPVoipIntegration_Record_Model::getRostelecomIdentificationKey();
            $signKey = Settings_SPVoipIntegration_Record_Model::getRostelecomSignKey();
            $data = $clientId . $data . $signKey;
        }

        return hash('sha256', $data);
    }
    
    /**
     * Returns current users's voip settings for rostelecom
     * @param type $voipSetting
     * @return type value from vtiger_users
     */
    public static function getRostelecomVoipSettingsFieldValue($voipSetting) {
        // allowed only rostelecom extension fields from vtiger_users
        $allowedFields = array(
            'sp_rostelecom_extension_internal',
            'sp_rostelecom_extension',
            'sp_rostelecom_extension_sipiru'
        );
        
        if (!in_array($voipSetting, $allowedFields)) {
            return;
        }
        
        $db = PearDatabase::getInstance();
        $sql = "SELECT " . $voipSetting  . " FROM vtiger_users WHERE id = ?";
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
        
        $result = $db->pquery($sql, array($currentUserId));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $fieldValue = $resRow[$voipSetting];
        }
        return $fieldValue;
    }

}
