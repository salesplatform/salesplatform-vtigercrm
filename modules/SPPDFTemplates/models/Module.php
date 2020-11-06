<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_Module_Model extends Vtiger_Module_Model {
        
    var $name='SPPDFTemplates';
    
    public function getListTemplates() {
        $db = PearDatabase::getInstance();
        $result = $db->query('SELECT * FROM sp_templates ORDER BY templateid ASC ');
        $templatesList = array();
        while($template = $db->fetchByAssoc($result)) {
            $templatesList[] = new SPPDFTemplates_Record_Model($template);  
        }
        return $templatesList;
    }
    
    /**
     * Return array contains names of avalible modules to create template.
     * @return array
     */
    public function getModulesList() {
        $db =  PearDatabase::getInstance();
        $modules = Array('' => getTranslatedString("LBL_PLS_SELECT"));
        $sql = "SELECT name FROM vtiger_tab WHERE name IN ('SalesOrder', 'Invoice', 'Quotes', 'HelpDesk', 'Act', 'Consignment', 'PurchaseOrder', 'Potentials', 'SPPayments') ORDER BY name ASC";
        $result = $db->query($sql);
        while($row = $db->fetchByAssoc($result)){
          $modules[$row['name']] = getTranslatedString($row['name']);
        } 

        return $modules;
    }
    
    /**
     * Return avalible orientations of page.
     * @return type
     */
    public function getPageOrientations() {
        $orientations['P'] = getTranslatedString('Portrait');
        $orientations['L'] = getTranslatedString('Landscape');
        
        return $orientations;
    }
    
    /**
     * Return PDF templates id and name, avalible to $modulename.
     * @param String $moduleName
     * @return array<SPPDFTemplates_Record_Model>
     */
    public function getModuleTemplates($moduleName, $spCompany = '') {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT templateid, name FROM sp_templates where module=? AND spcompany IN ("All",?)', 
                array($moduleName, html_entity_decode($spCompany, ENT_QUOTES, 'UTF-8')));
        
        $templatesList = array();
        while($template = $db->fetchByAssoc($result)) {
            $templatesList[] = new SPPDFTemplates_Record_Model($template);  
        }
        return $templatesList;
    }
    
}
?>