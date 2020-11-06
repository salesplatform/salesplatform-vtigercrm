<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_ListView_Model extends Settings_Vtiger_ListView_Model {
    
    /**
     * Function return only statuses type settings
     * @return string
     */
    public function getBasicListQuery() {
        $query = parent::getBasicListQuery();
        $query .= ' WHERE setting_type=\'statusParam\'';
        return $query;
    }
    
}