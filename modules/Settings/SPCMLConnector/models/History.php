<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_History_Model {  
    
    /**
     * Return names of history entries.
     * @return array
     */
    public function getHeaders() {
        return array('Date','Type','Status','Direction','Error');
    }
    
    /**
     * Return all history entries as array
     */
    public function getEntries() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('select date, type, status, direction, error from sp_commercetranzaction '
                . 'WHERE `date` > (NOW() - INTERVAL ? DAY)', array(30));
        //$result = $db->query('select date, type, status, direction, error from sp_commercetranzaction');

        $entries = array();
        while($row = $db->fetchByAssoc($result)) {
            $entry = array();
            $entry[] = $row['date'];
            $entry[] = $row['type'];
            
            /* To display message - not number */
            if( $row['status'] == 1 ) {
                $entry[] = "success";
            } else {
                $entry[] = "error";
            }
            
            $entry[] = $row['direction'];
            $entry[] = $row['error'];
            
            $entries[] = $entry;
        }
        return $entries;
    }
}