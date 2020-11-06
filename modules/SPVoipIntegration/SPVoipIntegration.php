<?php

include_once('vtlib/Vtiger/Module.php');
include_once 'modules/SPVoipIntegration/ProvidersEnum.php';

use SPVoipIntegration\ProvidersEnum;

class SPVoipIntegration extends CRMEntity {

    function SPVoipIntegration() {
        global $log;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function save_module() {
        
    }

    function vtlib_handler($modulename, $event_type) {
        if ($event_type == 'module.postinstall') {
            $this->addResources();
            $this->createFields();
            $this->providerInfoInsertion();
            $this->settingsInsertion();
        } else if ($event_type == 'module.disabled') {
            $this->removeResources();
        } else if ($event_type == 'module.enabled') {
            $this->addResources();
        } else if ($event_type == 'module.preuninstall') {
            $this->removeResources();
        } else if ($event_type == 'module.preupdate') {
            
        } else if ($event_type == 'module.postupdate') {
            
        }
    }

    private function settingsInsertion() {
        $db = PearDatabase::getInstance();
        $displayLabel = 'VoipIntegration';

        $fieldid = $db->query_result(
                $db->pquery("SELECT fieldid FROM vtiger_settings_field WHERE name=?", array($displayLabel)), 0, 'fieldid');
        if (!$fieldid) {
            $blockid = $db->query_result(
                    $db->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_INTEGRATION'", array()), 0, 'blockid');
            $sequence = (int) $db->query_result(
                            $db->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_field WHERE blockid=?", array($blockid)), 0, 'sequence') + 1;
            $fieldid = $db->getUniqueId('vtiger_settings_field');
            $db->pquery("INSERT INTO vtiger_settings_field (fieldid, blockid, sequence, name, iconpath, linkto)
                        VALUES (?,?,?,?,?,?)", array($fieldid, $blockid, $sequence, $displayLabel, '',
                'index.php?module=SPVoipIntegration&parent=Settings&view=Index'));
        }
    }

    private function providerInfoInsertion() {
        $db = PearDatabase::getInstance();
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (1, '" . ProvidersEnum::ZADARMA . "', 'zadarma_secret', 'Zadarma secret', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (2, '" . ProvidersEnum::ZADARMA . "', 'zadarma_key', 'Zadarma key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (3, '" . ProvidersEnum::GRAVITEL . "', 'gravitel_url', 'Gravitel API url', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (4, '" . ProvidersEnum::GRAVITEL . "', 'gravitel_key', 'Gravitel key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (5, '" . ProvidersEnum::GRAVITEL . "', 'crm_key', 'Gravitel CRM key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (6, '" . ProvidersEnum::ZADARMA . "', 'zadarma_ip', 'Zadarma IP', '185.45.152.42')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (7, '" . ProvidersEnum::ZEBRA . "', 'zebra_login', 'ZebraTelecom login', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (8, '" . ProvidersEnum::ZEBRA . "', 'zebra_password', 'ZebraTelecom Password', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (9, '" . ProvidersEnum::ZEBRA . "', 'zebra_realm', 'ZebraTelecom Realm', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (10, '" . ProvidersEnum::MEGAFON . "', 'megafon_url', 'Megafon API url', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (11, '" . ProvidersEnum::MEGAFON . "', 'megafon_key', 'Megafon key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (12, '" . ProvidersEnum::MEGAFON . "', 'megafon_crm_key', 'Megafon CRM key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (13, '" . ProvidersEnum::MANGO . "', 'mango_secret', 'Key for sign', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (14, '" . ProvidersEnum::MANGO . "', 'mango_key', 'CRM id', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (16, '" . ProvidersEnum::MANGO . "', 'mango_url', 'Mango API url', 'https://app.mango-office.ru/vpbx')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (17, '" . ProvidersEnum::UISCOM . "', 'uiscom_access_token', 'UIScom access token', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (18, '" . ProvidersEnum::UISCOM . "', 'uiscom_api_url', 'UIScom API URL', 'https://callapi.uiscom.ru/v4.0')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (20, '" . ProvidersEnum::TELPHIN . "', 'telphin_app_id', 'Telphin app id', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (21, '" . ProvidersEnum::TELPHIN . "', 'telphin_app_secret', 'Telphin app secret', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (22, '" . ProvidersEnum::TELPHIN . "', 'telphin_api_url', 'Telphin API url', 'https://apiproxy.telphin.ru/')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (23, '" . ProvidersEnum::YANDEX . "', 'yandex_api_key', 'Yandex api key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (24, '" . ProvidersEnum::YANDEX . "', 'yandex_default_outgoing', 'Yandex default outgoing number', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (25, '" . ProvidersEnum::YANDEX . "', 'yandex_api_url', 'Yandex API URL', 'https://api.yandex.mightycall.ru/')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (26, '" . ProvidersEnum::DOMRU . "', 'domru_url', 'Dom.Ru API url', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (27, '" . ProvidersEnum::DOMRU . "', 'domru_key', 'Dom.Ru key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (28, '" . ProvidersEnum::DOMRU . "', 'domru_crm_key', 'Dom.Ru CRM key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (29, '" . ProvidersEnum::WESTCALL_SPB . "', 'westcall_spb_url', 'WestCall SPB API url', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (30, '" . ProvidersEnum::WESTCALL_SPB . "', 'westcall_spb_key', 'WestCall SPB key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (31, '" . ProvidersEnum::WESTCALL_SPB . "', 'westcall_spb_crm_key', 'WestCall SPB CRM key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (32, '" . ProvidersEnum::MCN . "', 'mcn_crm_token', 'MCN crm token', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (33, '" . ProvidersEnum::MCN . "', 'mcn_api_url', 'MCN API url', 'https://api.mcn.ru/v2/rest/')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (34, '" . ProvidersEnum::MCN . "', 'mcn_api_token', 'MCN api token', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (35, '" . ProvidersEnum::MCN . "', 'mcn_account_id', 'MCN accound id', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (36, '" . ProvidersEnum::MCN . "', 'mcn_pbx_id', 'MCN PBX id', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (37, '" . ProvidersEnum::ROSTELECOM . "', 'rostelecom_identification_key', 'Rostelecom Identification Key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (38, '" . ProvidersEnum::ROSTELECOM . "', 'rostelecom_sign_key', 'Rostelecom Signature Key', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (39, '" . ProvidersEnum::ROSTELECOM . "', 'rostelecom_default_outgoing', 'Rostelecom default outgoing number', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (40, '" . ProvidersEnum::ROSTELECOM . "', 'rostelecom_api_url', 'Rostelecom API URL', 'https://api.cloudpbx.rt.ru/')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (41, '" . ProvidersEnum::SIPUNI . "', 'sipuni_url', 'Sipuni API url', 'https://sipuni.com/api/')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (42, '" . ProvidersEnum::SIPUNI . "', 'sipuni_id', 'Sipuni user', '')");
        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (43, '" . ProvidersEnum::SIPUNI . "', 'sipuni_key', 'Sipuni key', '')");

        $db->query("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::settingsTable . " VALUES (45, '" . ProvidersEnum::ZEBRA . "', 'zebra_api_url', 'Zebra API URL', ' http://api.zebratelecom.ru/v1/kazoos/')");

        $db->pquery("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::defaultProvideTable . " values(?)", array(ProvidersEnum::ZADARMA));

	$db->pquery("INSERT INTO " . Settings_SPVoipIntegration_Record_Model::optionsTable . " VALUES('use_click_to_call', '0')", array());
    }

    private function createFields() {
        $moduleInstance = Vtiger_Module_Model::getInstance('PBXManager');
        $blockInstance = Vtiger_Block_Model::getInstance('LBL_PBXMANAGER_INFORMATION', $moduleInstance);

        if (!Vtiger_Field_Model::getInstance('sp_is_local_cached', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_is_local_cached';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Is local recorded';
            $fieldInstance->column = 'sp_is_local_cached';
            $fieldInstance->columntype = 'tinyint';
            $fieldInstance->uitype = 1;
            $fieldInstance->defaultvalue = 0;
            $fieldInstance->displaytype = 3;
            $fieldInstance->typeofdata = 'C~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_recordingurl', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_recordingurl';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Recording url';
            $fieldInstance->column = 'sp_recordingurl';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_is_recorded', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_is_recorded';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Is recorded';
            $fieldInstance->column = 'sp_is_recorded';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_recorded_call_id', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_recorded_call_id';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Recorder call id';
            $fieldInstance->column = 'sp_recorded_call_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_voip_provider', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_voip_provider';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Provider';
            $fieldInstance->column = 'sp_voip_provider';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_call_status_code', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_call_status_code';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Status code';
            $fieldInstance->column = 'sp_call_status_code';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_called_from_number', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_called_from_number';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'From number';
            $fieldInstance->column = 'sp_called_from_number';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_called_to_number', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_called_to_number';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'To number';
            $fieldInstance->column = 'sp_called_to_number';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        $usersModuleModel = Vtiger_Module_Model::getInstance("Users");
        if (!Vtiger_Field_Model::getInstance('sp_gravitel_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_gravitel_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Gravitel Id';
            $fieldInstance->column = 'sp_gravitel_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_megafon_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_megafon_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Megafon Id';
            $fieldInstance->column = 'sp_megafon_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_zebra_login', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_zebra_login';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Zebra login';
            $fieldInstance->column = 'sp_zebra_login';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_uiscom_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_uiscom_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'UIScom ID';
            $fieldInstance->column = 'sp_uiscom_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_uiscom_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_uiscom_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'UIScom extension';
            $fieldInstance->column = 'sp_uiscom_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_telphin_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_telphin_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Telphin extension';
            $fieldInstance->column = 'sp_telphin_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_zadarma_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_zadarma_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Zadarma extension';
            $fieldInstance->column = 'sp_zadarma_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_yandex_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_yandex_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Yandex extension';
            $fieldInstance->column = 'sp_yandex_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_yandex_outgoing_number', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_yandex_outgoing_number';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Yandex outgoing number';
            $fieldInstance->column = 'sp_yandex_outgoing_number';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_domru_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_domru_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Dom.RU Id';
            $fieldInstance->column = 'sp_domru_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_westcall_spb_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_westcall_spb_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'WestCall SPB Id';
            $fieldInstance->column = 'sp_westcall_spb_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_mcn_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_mcn_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'MCN Telecom extension';
            $fieldInstance->column = 'sp_mcn_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_rostelecom_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_rostelecom_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Rostelecom extension';
            $fieldInstance->column = 'sp_rostelecom_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_rostelecom_extension_internal', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_rostelecom_extension_internal';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Rostelecom extension internal';
            $fieldInstance->column = 'sp_rostelecom_extension_internal';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_rostelecom_extension_sipiru', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_rostelecom_extension_sipiru';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Rostelecom extension SIP URI';
            $fieldInstance->column = 'sp_rostelecom_extension_sipiru';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_sipuni_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_sipuni_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Sipuni extension';
            $fieldInstance->column = 'sp_sipuni_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('sp_mango_extension', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'sp_mango_extension';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Mango extension';
            $fieldInstance->column = 'sp_mango_extension';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }
    }

    private function addResources() {
        Vtiger_Link::addLink(0, 'HEADERSCRIPT', 'SPVoipIntegration', 'modules/SPVoipIntegration/resources/SPVoipIntegration.js');
    }

    private function removeResources() {
        Vtiger_Link::deleteLink(0, 'HEADERSCRIPT', 'SPVoipIntegration', 'modules/SPVoipIntegration/resources/SPVoipIntegration.js');
    }

}
