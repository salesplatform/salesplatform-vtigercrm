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
 * Converts codes of usage units to crm values
 */
class UnitsConverter {
    
    private static $basicUnitsMap = array(
        '812' => 'Box',
        '8751' => 'Carton',
        '641' => 'Dozen',
        '796' => 'Each',
        '356' => 'Hours',
        '166' => 'Lb',
        '006' => 'M',
        '778' => 'Pack',
        '642' => 'Pieces',
        '625' => 'Sheet',
        '055' => 'Sq Ft'
    );
    
    public static function convertFrom1cToCrm($unitCode) {
        $convertedUnit = null;
        if(array_key_exists($unitCode, self::$basicUnitsMap)) {
            $convertedUnit = self::$basicUnitsMap[$unitCode];
        }
        
        return $convertedUnit;
    }
    
    public static function convertFromCrmValueToCode($crmValue) {
        $unitCode = array_search($crmValue, self::$basicUnitsMap);
        if($unitCode === false) {
            $unitCode = null;
        }
        
        return $unitCode;
    }
    
    public static function getDefaultUnitCode() {
        return 796;
    }
}
