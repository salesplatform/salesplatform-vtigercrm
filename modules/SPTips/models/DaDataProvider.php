<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_DaDataProvider_Model extends SPTips_AbstractProvider_Model {
    
    private $APIKey;
    private $partnerValue;
    
    public function __construct($settings) {
        $this->APIKey = $settings['api_key'];
        $this->partnerValue = 'SALESPLATFORM';
    }
    
    public function getAPIKey() {
        return $this->APIKey;
    }
    
    public function getSecretKey() {
        return $this->secretKey;
    }
    
    /**
     * 
     * @param string $search
     * @param Settings_SPTips_RuleDependentField_Model[] $dependentFields
     * @return type
     */
    public function searchAddress($search, $dependentFields) {
        $response = $this->executeRequest($this->getSearchAddressURL(), ['query' => $search]);
        $result = [];
        foreach ($response['suggestions'] as $value) {
            $providerData = $value['data'];
            $result[] = [
                'tip' => $value['value'],
                'fill' => $this->getFillData($providerData, $dependentFields)
            ];
        }
        
        return $result;
    }
    
    public function searchOrganization($search, $dependentFields) {
        $response = $this->executeRequest($this->getSearchOrganizationURL(), ['query' => $search]);
        $result = [];
        foreach ($response['suggestions'] as $value) {
            $providerData = $value['data'];
            $result[] = [
                'tip' => $value['value'],
                'fill' => $this->getSearchOrganizationFillData($providerData, $dependentFields)
            ];
        }
        
        return $result;
    }
    
    public function getSupportedSearchTypes() {
        return [
            SPTips_SearchType_Model::ADDRESS,
            SPTips_SearchType_Model::ORGANIZATION
        ];
    }
    
    /**
     * 
     * @param array $providerData
     * @param Settings_SPTips_RuleDependentField_Model[] $dependentFields
     */
    private function getSearchOrganizationFillData($providerData, $dependentFields) {
        $fillData = [];
        foreach($dependentFields as $fieldModel) {
            $providerFieldName = $fieldModel->getProviderFieldName();
            $fillData[] = [
                'vtigerField' => $fieldModel->getVtigerFieldName(),
                'value' => $this->getDependentDataValue($providerData, $providerFieldName)
            ];
        }
        
        return $fillData;
    }
    
    private function getOrganizationDataPath($providerFieldName) {
        return explode(".", $providerFieldName);
    }
    
    private function getDependentDataValue($providerData, $providerFieldName) {
        if(empty($providerFieldName)) {
            return null;
        }
        
        $searchValue = $providerData;
        $notFound = false;
        foreach($this->getOrganizationDataPath($providerFieldName) as $levelFieldName) {
            if(!array_key_exists($levelFieldName, $searchValue)) {
                $notFound = true;
                break;
            }
            
            $searchValue = $searchValue[$levelFieldName];
        }
        
        if($notFound) {
            return null;
        }
        
        return $searchValue;
    }
    
    private function getFillData($providerData, $dependentFields) {
        $fillData = [];
        foreach($dependentFields as $dependentField) {
            $providerFieldName = $dependentField->getProviderFieldName();
            $providerValue = '';
            if(array_key_exists($providerFieldName, $providerData)) {
                $providerValue = $providerData[$providerFieldName];
            }

            $fillData[] = [
                'vtigerField' => $dependentField->getVtigerFieldName(),
                'value' => $providerValue
            ];
        }
        
        return $fillData;
    }
    
    public function getProviderFields($type) {
        switch($type) {
            
            case SPTips_SearchType_Model::ADDRESS:
                return [
                    'postal_code' => 'Postal code',
                    'country' => 'Country',
                    'region_fias_id' => 'FIAS region code',
                    'region_kladr_id' => 'KLADR region code',
                    'region_with_type' => 'Region with type',
                    'region_type' => 'Region type (short)',
                    'region_type_full' => 'Region type (full)',
                    'region' => 'Region',
                    'area_fias_id' => 'FIAS region code in the region',
                    'area_kladr_id' => 'KLADR region code in the region',
                    'area_with_type' => 'Area in the region with type',
                    'area_type' => 'Area in the region with type (short)',
                    'area_type_full' => 'Area in the region with type (full)',
                    'area' => 'Area in the region',
                    'city_fias_id' => 'FIAS code of the city',
                    'city_kladr_id' => 'KLADR code of the city',
                    'city_with_type' => 'City with type',
                    'city_type' => 'Type of city (short)',
                    'city_type_full' => 'Type of city (full)',
                    'city' => 'City',
                    'city_area' => 'Administrative district (only for Moscow)',
                    'city_district_fias_id' => 'FIAS code of the ciry district (only if the district is in FIAS)',
                    'city_district_kladr_id' => 'KLADR district code of the city (do not fill out)',
                    'city_district_with_type' => 'City district with type',
                    'city_district_type' => 'Type of city district (short)',
                    'city_district_type_full' => 'Type of city district (long)',
                    'city_district' => 'City district',
                    'settlement_fias_id' => 'FIAS code of the settlement',
                    'settlement_kladr_id' => 'KLADR code of the settlement',
                    'settlement_with_type' => 'Settlement with type',
                    'settlement_type' => 'Type of settlement (short)',
                    'settlement_type_full' => 'Type of settlement (full)',
                    'settlement' => 'Settlement',
                    'street_fias_id' => 'FIAS street code',
                    'street_kladr_id' => 'KLADR street code',
                    'street_with_type' => 'Street with type',
                    'street_type' => 'Street type (short)',
                    'street_type_full' => 'Street type (full)',
                    'street' => 'Street',
                    'house_fias_id' => 'FIAS house code',
                    'house_kladr_id' => 'KLADR house code',
                    'house_type' => 'Type of house (short)',
                    'house_type_full' => 'Type of house (long)',
                    'house' => 'House',
                    'block_type' => 'Type of house/block (short)',
                    'block_type_full' => 'Type of house/block (long)',
                    'block' => 'Block',
                    'flat_type' => 'Type of appartment (short)',
                    'flat_type_full' => 'Type of appartment (long)',
                    'flat' => 'Appartment',
                    'flat_area' => 'Appartment area',
                    'square_meter_price' => 'Market value m²',
                    'flat_price' => 'Market value of an appartment',
                    'postal_box' => 'Postal box',
                    'fias_id' => 'FIAS code',
                    'fias_code' => 'Hierarchical address code in FIAS',
                    'fias_level' => 'Detail level which address is found in FIAS',
                    'fias_actuality_state' => 'Sign of relevance of the address in FIAS',
                    'kladr_id' => 'KLADR code',
                    'capital_marker' => 'Sing of the center of a district of region',
                    'okato' => 'OKATO code',
                    'oktmo' => 'OKTMO code',
                    'tax_office' => 'Individual tax code for natural persons',
                    'tax_office_legal' => 'Individual tax code for legal personality',
                    'timezone' => 'Timezone',
                    'geo_lat' => 'Geocode: latitude',
                    'geo_lon' => 'Geocode: longitude',
                    'beltway_hit' => 'Inside Koltsevaya line?',
                    'beltway_distance' => 'Distance from Koltsevaya line in km',
                    'qc_geo' => 'Coordinate precision code',
                    'qc_complete' => 'Code of eligibility for dispatch',
                    'qc_house' => 'Home in FIAS?',
                    'qc' => 'Address verification code',
                    'unparsed_parts' => 'Unrecognized part of the address',
                    'metro' => 'List of nearest metro stations (<= 3)'
                ];
                
            case SPTips_SearchType_Model::ORGANIZATION:
                return [
                    'address.value' => 'Address',
                    'address.unrestricted_value' => 'Full address',
                    'address.data.source' => 'EGRUL address',
                    'branch_count' => 'Branch count',
                    'branch_type' => 'Branch type',
                    'inn' => 'INN',
                    'kpp' => 'KPP',
                    'ogrn' => 'OGRN',
                    'ogrn_date' => 'OGRN date',
                    'management.name' => 'Managment name',
                    'management.post' => 'Managment post',
                    'name.full_with_opf' => 'Full name with opf',
                    'name.short_with_opf' => 'Short name with opf',
                    'name.full' => 'Full name',
                    'name.short' => 'Short name',
                    'okved' => 'OKVED',
                    'okved_type' => 'OKVED type',
                    'opf.code' => 'OPF code',
                    'opf.full' => 'OPF full name',
                    'opf.short' => 'OPF short name',
                    'opf.type' => 'OPF type',
                    'state.actuality_date' => 'Data actual date',
                    'state.registration_date' => 'Registration date',
                    'state.liquidation_date' => 'Liquidation date',
                    'state.status' => 'Status',
                    'type' => 'Organization type'
                ];
                
            default:
                return [];
        }
        
        
    }  
    
    private function getSearchAddressURL() {
        return "http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
    }
    
    private function getSearchOrganizationURL() {
        return "http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party";
    }
    
    private function getHeaders() {
        return array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $this->APIKey,
            'X-Partner: ' . $this->partnerValue
        );
    }
    
    private function prepareRequest($curl, $data) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    private function executeRequest($url, $data) {
        $result = false;
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl, $data);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        return $result;
    }
}