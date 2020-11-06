<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_Edit_View extends Vtiger_Index_View {
    
    /**
     * Display view.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request) {
        $recordModel = SPPDFTemplates_Record_Model::getInstanceById($request->get('templateid'));
        $moduleModel = new SPPDFTemplates_Module_Model();
        $pdfCompanies = array('All' => vtranslate('All'));
        foreach(Settings_Vtiger_CompanyDetails_Model::getCompanies() as $company) {
            $pdfCompanies[$company] = vtranslate($company, 'Settings:Vtiger');
        }
 
        if($request->get('isDuplicate') != NULL ) {
            $recordModel->toDuplicate();
        }
        
        $viewer = $this->getViewer($request);
        
        // SalesPlatform.ru begin Unifying method for EditView preparing 
        $recordModel = prepareEditView($recordModel, $_REQUEST, $viewer); 
        // SalesPlatform.ru end
        
        $viewer->assign('MODULE', $request->getModule());
        $viewer->assign('MODEL', $recordModel);
        $viewer->assign('MODULENAMES',$moduleModel->getModulesList());
        $viewer->assign('PAGE_ORIENTATIONS',$moduleModel->getPageOrientations());
        $viewer->assign('SP_PDF_COMPANIES',$pdfCompanies);
        $viewer->view('EditPDFTemplate.tpl', $request->getModule());
    }
}