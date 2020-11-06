<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_SavePDFTemplate_Action extends Vtiger_Save_Action {
    
    public function process(Vtiger_Request $request) {

        $record = new SPPDFTemplates_Record_Model();
        $record->set('templateid', $request->get('templateid'));
        $record->set('module', $request->get('modulename'));
        $record->set('name', $request->get('templatename'));
        $record->set('header_size', $request->get('header_size'));
        $record->set('footer_size', $request->get('footer_size'));
        $record->set('page_orientation', $request->get('page_orientation'));
        $record->set('spcompany', $request->get('spcompany'));
        $record->set('template', fck_from_html($_REQUEST["body"]));             //hack to save template html-structures
        
        $record->save();
        
        /* Display detail view */
        header("Location:index.php?module=SPPDFTemplates&view=Detail&templateid=".$record->getId());
    }
}

?>
