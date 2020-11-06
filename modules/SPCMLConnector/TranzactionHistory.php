<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class TranzactionHistory {
    
    const SUCCES_STATUS = 1;
    const FAIL_STATUS = 0;
    
    /**
     * Return array of Strings - names of fields.
     * @return array<String>
     */
    public function getTranzactionHistoryHeader() {
        
        global $app_strings;
        $header = array($app_strings['LBL_NUMBER'], $app_strings['LBL_TIME'],$app_strings['LBL_EXCHANGE_TYPE'],
            $app_strings['LBL_STATUS'], $app_strings['LBL_DIRECTION'], $app_strings['LBL_ERROR']);
        return $header;
    }
    
    /**
     * Return records< enumerated in $navigation_array.
     * @param array $navigation_array
     * @return array<array>
     */
    public function getTranzactionHistoryEntries($navigation_array) {
        global $adb;
        $entriesList = array();
        
        /* Get all records from tranzaction table */
        $result = $adb->pquery("select * from sp_commercetranzaction",array());
        
        /* Set in output only needed */
        if($navigation_array['end_val'] != 0) { 
            for($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++) {
                    $record = array();      //current record 
                    
                    /* Fill it */
                    $record[] = $adb->query_result($result, $i-1, 'id');
                    $record[] = $adb->query_result($result, $i-1, 'date');
                    $record[] = $adb->query_result($result, $i-1, 'type');
                    $record[] = $adb->query_result($result, $i-1, 'status');
                    $record[] = $adb->query_result($result, $i-1, 'direction');
                    $record[] = $adb->query_result($result, $i-1, 'error');

                    $entriesList[] = $record;
            }	
	}
        return $entriesList;
    }
    
    /**
     * Returns number of records, which was on CML exchange.
     * @return int
     */
    public function getRowsCount() {
        global $adb;
        $result = $adb->pquery("select * from sp_commercetranzaction",array());
        return $adb->num_rows($result);
    }
    
    /**
     * Prints an error to tranzaction table.
     * @param int $type
     * @param String $direction
     * @param String $error
     */
    public function fixTranzactionError($type,$direction,$error) {
        global $adb;
        $params = array(date("Y-m-d H:i:s"), $type, self::FAIL_STATUS ,$direction,$error);
        $adb->pquery("insert into sp_commercetranzaction 
            (`date`, `type`, `status`, `direction`,`error`)
            VALUES (?, ?, ?, ?,?);", $params);
    }
    
    /**
     * Create new record in tranzaction table.
     * @param int  $type
     * @param String $direction
     */
    public function fixSuccessTranzaction($type, $direction) {
        global $adb;
        $params = array(date("Y-m-d H:i:s"),$type,  self::SUCCES_STATUS ,$direction);
        $adb->pquery("insert into sp_commercetranzaction 
            (`date`, `type`, `status`, `direction`)
            VALUES (?, ?, ?, ?);", $params);
    }
    
    /**
     * Return last tranzaction time with site.
     * @return String type
     */
    public function getLastSalesSiteTranzaction() {
        global $adb;
        $params = array();
        $result = $adb->pquery("select MAX(date) from sp_commercetranzaction where 
            type='SalesOrder' and direction='from_site';",$params);
        $value = $adb->fetchByAssoc($result);
        if($value['max(date)'] == null) {
            return date("Y-m-d H:i:s");   
        }
        return $value['max(date)'];
    }
    
    /**
     * Return last time tranzaction with one es.
     * @return type
     */
    public function getLastSalesOneEsTranzaction() {
        global $adb;
        $params = array();
        $result = $adb->pquery("select MAX(date) from sp_commercetranzaction where 
            type='SalesOrder' and direction='from_1c';",$params);
        $value = $adb->fetchByAssoc($result);
        if($value['max(date)'] == null) {
            return date("Y-m-d H:i:s", 0);   
        }
        return $value['max(date)'];
    }
    
}
