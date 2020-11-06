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
 * Service contains business logic for kladr integration. It provides API
 * to search some address parts. Specific endless value 00 of code - it record is actual.
 */
class KladrService {
     
    private $db;
    
    public function __construct() {
        /* Load config params and create connection */
        require_once 'modules/SPKladr/kladrDB.config.inc';
        
        $this->db = new PearDatabase($spKladrDB['type'], $spKladrDB['hostname'], $spKladrDB['name'], $spKladrDB['username'], $spKladrDB['password']);
    }
    
    /**
     * Search city by part of it name. Return container, which have city name 
     * and socr, state name and socr and region name and socr
     * 
     * @param array $searchParams - list which contains values of search params
     * @return array
     */
    public function searchCity($searchParams) {

        /* Check params to search */
        if($searchParams["cityName"] == null || $searchParams["cityRecordsLimit"] <= 0 || $searchParams["cityOffset"] < 0) {
            return array();
        }
           
        $sql = "select name, socr, code FROM sp_kladr " .
                    "WHERE socr IN (select scname FROM sp_kladr_socrbase WHERE level IN (3)) " .
                    "AND code LIKE '%00' AND name LIKE ? ORDER BY socr, name LIMIT ?,?";
        $searchResult = $this->db->pquery($sql, array($searchParams["cityName"] . "%", $searchParams["cityOffset"], $searchParams["cityRecordsLimit"]));
        
        $citiesList = array();
        $count = 0;
        if($searchResult) {
            while($searchedRow = $this->db->fetch_row($searchResult)) {
                $currentCity = array();
                $currentCity["cityName"] = $searchedRow["name"];
                $currentCity["citySocr"] = $searchedRow["socr"];
                $currentCity["cityCode"] = substr($searchedRow["code"], 0, 11);

                $this->selectCityStateAndRegion($currentCity);

                $citiesList[] = $currentCity;
            }

            /* Get all records count without paging */
            $sql = "select count(code) AS count FROM sp_kladr WHERE socr IN (select scname FROM sp_kladr_socrbase WHERE level IN (3,4)) " .
                        "AND code LIKE '%00' AND name LIKE ?";

            $countResult = $this->db->pquery($sql, array($searchParams["cityName"] . "%"));
            $countRow = $this->db->fetch_row($countResult);
            $count = $countRow['count'];
        }
         
        return array("totalCities" => $count, "selectedCities" => $citiesList);
    }
    
    /**
     * Searches state name and socr by it part of name.
     * 
     * @param array $searchParams - list which contains values of search params
     * @return array
     */
    public function searchStateByName($searchParams) {
        
        if($searchParams["stateName"] == null) {
            return array();
        }
        
        $searchResult = $this->db->pquery("select name, socr FROM sp_kladr WHERE code LIKE '%00000000000' AND name LIKE ? ORDER BY socr, name", 
                array($searchParams["stateName"] . "%"));
        
        $statesList = array();
        if($searchResult) {
            while($searchedRow = $this->db->fetch_row($searchResult)) {
                $currentState = array();
                $currentState['stateName'] = $searchedRow['name'];
                $currentState['stateSocr'] = $searchedRow['socr'];

                $statesList[] = $currentState;   
            }
        }
        
        return $statesList;
    }
      
    /**
     * Search localtion of 5 level which included in transmitted location of 3-4 level
     * 
     * @param array $searchParams - list which contains values of search params
     * @return arary
     */
    private function searchStreet($searchParams) {
        
        /* Check search params */
        if($searchParams["cityCode"] == null || $searchParams["streetName"] == null) {
            return array();
        }

        /* Prepare selection - level 5 locations by KLADR? included in transmited location of 3-4 level */
        $params = array();
        $params[] = $searchParams["cityCode"] . "%00";
        $params[] = $searchParams["streetName"] . "%";
        $sql = "select name, socr, code FROM sp_kladr_street WHERE code LIKE ? AND name LIKE ? ORDER BY name";

        /* Fill in list search results */
        $searchResult = $this->db->pquery($sql, $params);
        $streetsList = array();
        
        if($searchResult) {
            while($searchedRow = $this->db->fetch_row($searchResult)) { 
                $currentStreet = array();
                $currentStreet["streetName"] = $searchedRow["name"];
                $currentStreet["streetSocr"] = $searchedRow["socr"];
                $currentStreet["streetCode"] = substr($searchedRow["code"], 0, 15);

                $streetsList[] = $currentStreet;
            }
        }
        
        return $streetsList;
    }
    
    /**
     * Search list of houses by transfered params.
     * 
     * @param array $searchParams
     * @return arary
     */
    private function searchHouse($searchParams) {
        
        /* Check mandatory search param */
        if($searchParams["streetCode"] == null || $searchParams["houseNumber"] == null) {
            return array();
        }
                
        /* Prepare house number selection */
        $params = array();
        $params[] = $searchParams["streetCode"] . "%";
        $params[] = $searchParams["houseNumber"] . "%";
        $sql = "select name, mail_index FROM sp_kladr_formatted_house WHERE code LIKE ? AND name LIKE ? ORDER BY CAST(name as UNSIGNED)";

        /* Get search results */
        $searchResult = $this->db->pquery($sql, $params);
        $housesList = array();
        while($searchedRow = $this->db->fetch_row($searchResult)) { 
            $currentHouse = array();
            $currentHouse["houseNumber"] = $searchedRow["name"];
            $currentHouse["mailIndex"] = $searchedRow["mail_index"];
            
            $housesList[] = $currentHouse;
        }

        return $housesList;
    }
    
    /**
     * Return list of all full address strings, which matches for part of adress,
     * transmitted in $addressPart
     * 
     * @param array $searchParams
     * @return array
     */
    public function searchFullAddress($searchParams) {
        $fullAddressesList = array();
        switch($searchParams['requestStep']) {
            case 1:
                $fullAddressesList = $this->searchCity($searchParams);
                break;
            
            case 2:
                $fullAddressesList = $this->searchStreet($searchParams);
                break;
       
            case 3:
                $fullAddressesList = $this->searchHouse($searchParams);
        }
        
        return $fullAddressesList;
    }
    
    
    /**
     * Add state and region to city container.
     * 
     * @param array $currentCity - description of city name, socr and code
     * @param PearDatabase $db
     */
    private function selectCityStateAndRegion(&$currentCity) {
        
        /* Get lelvel 1 location for current entity of level 3-4 */
        $firstLevelLocationCode = substr($currentCity["cityCode"], 0, 2) . "00000000000";
        $firstLevelLocationResult = $this->db->pquery("select name, socr FROM sp_kladr WHERE code=?", array($firstLevelLocationCode));
        $currentCity["stateName"] = "";
        $currentCity["stateSocr"] = "";
        if($firstLevelLocationResult && $firstLevelLocationRow = $this->db->fetch_row($firstLevelLocationResult)) {
            $currentCity["stateName"] = $firstLevelLocationRow["name"];
            $currentCity["stateSocr"] = $firstLevelLocationRow["socr"];
        }

        /* Get lelvel 2 location for current entity of level 3-4 */
        $currentCity["regionName"] = "";
        $currentCity["regionSocr"] = "";
        $secondLevelLocationCode = substr($currentCity["cityCode"], 0, 5) . "00000000";
        $secondLevelLocationResult = $this->db->pquery("select name, socr FROM sp_kladr WHERE code=? "
                                                    . "AND socr IN (select scname FROM sp_kladr_socrbase WHERE level=2)", array($secondLevelLocationCode));
        if($secondLevelLocationResult && $secondLevelLocationRow = $this->db->fetch_row($secondLevelLocationResult)) {
            $currentCity["regionName"] = $secondLevelLocationRow["name"];
            $currentCity["regionSocr"] = $secondLevelLocationRow["socr"];
        } 
    }
}