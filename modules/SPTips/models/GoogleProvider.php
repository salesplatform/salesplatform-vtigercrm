<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPTips_GoogleProvider_Model extends SPTips_AbstractProvider_Model {
    
    private $APIKey;
    
    public function __construct($settings) {
        $this->APIKey = $settings['api_key'];
        $this->curLanguage = Vtiger_Language_Handler::getShortLanguageName();
    }

    public function getProviderFields($type) {
        switch($type) {
            case SPTips_SearchType_Model::ADDRESS:
                return [
                    'street_number' => 'Street number',
                    'route' => 'Street',
                    'locality' => 'City',
                    'country' => 'Country',
                    'postal_code' => 'Postal code'
                ];
            
            default:
                return [];
        }
    }
    
    public function searchAddress($search, $dependentFields) {
        $response = [];

        $url = $this->getAPIAutocompleteUrl($search);
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        
        foreach ($result['predictions'] as $prediction) {
            $placeId = $prediction['place_id'];
            $detailInfo = $this->searchDetailAddress($placeId);
            
            $tipData = [
                'tip' => $prediction['description']
            ];
            if(!empty($dependentFields)) {
                $tipData['fill'] = $this->getFillData($detailInfo, $dependentFields);
            }
            
            $response[] = $tipData;
        }
        return $response;
    }
    
    public function getSupportedSearchTypes() {
        return [
            SPTips_SearchType_Model::ADDRESS
        ];
    }

    public function searchOrganization($search, $dependentFields) {
        /* Not supported */
        return [];
    }
    
    private function getHeaders() {
        return array(
            'Content-Type: application/json',
            'Accept: application/json',
        );
    }
    
    private function prepareRequest($curl) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_GET, 1);
    }
    
    private function searchDetailAddress($placeId) {
        $url = $this->getAPIDetailsUrl($placeId);
        $result = [];
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl);
            $json = curl_exec($curl);
            $result = json_decode($json, true);
            curl_close($curl);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $detailInfo
     * @param Settings_SPTips_RuleDependentField_Model[] $dependentFields
     * @return type
     */
    private function getFillData($detailInfo, $dependentFields) {
        $fillMap = [];
        foreach($dependentFields as $dependentField) {
            $fillMap[$dependentField->getProviderFieldName()] = $dependentField->getVtigerFieldName();
        }
        
        $result = [];
        foreach ($detailInfo["result"]["address_components"] as $value) {
            $answerLocationTypes = $value['types'];
            foreach($answerLocationTypes as $locationType) {
                if(array_key_exists($locationType, $fillMap)) {
                    $result[] = [
                        'vtigerField' => $fillMap[$locationType],
                        'value' => $value['long_name']
                    ];
                }
            }
        }
        
        return $result;
    }
    
    private function getAPIAutocompleteUrl($searchParam, $types = "address", $format = "json") {
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/";
        $url .= $format . "?input=" . urlencode($searchParam) . "&types=" . $types . "&language=" . $this->curLanguage . "&key=" . $this->APIKey;
        return $url;
    }
    
    private function getAPIDetailsUrl($placeId, $format = "json") {
        $url = "https://maps.googleapis.com/maps/api/place/details/";
        $url .= $format . "?placeid=" . $placeId . "&language=" . $this->curLanguage . "&key=" . $this->APIKey;
        return $url;
    }

    
}

