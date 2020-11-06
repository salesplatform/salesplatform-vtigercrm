<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_Detail_View extends Vtiger_Index_View { 

    public function preProcess (Vtiger_Request $request, $display=true) {
        $viewer = $this->getViewer($request);
        $recordModel = SPPDFTemplates_Record_Model::getInstanceById($request->get('templateid'));
        $viewer->assign('RECORD', $recordModel);
        parent::preProcess($request, $display);
        
    }
    
    public function process(Vtiger_Request $request) {
        
        $recordModel = SPPDFTemplates_Record_Model::getInstanceById($request->get('templateid'));
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $request->getModule());
        $viewer->assign('MODEL', $recordModel);
        $viewer->view('DetailViewPDFTemplate.tpl', $request->getModule());
    }
}