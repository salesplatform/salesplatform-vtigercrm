<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_EditStatus_View extends Settings_Vtiger_IndexAjax_View {
    
    /**
     * Display Ajax edit view to status setting.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request) {
        $record = $request->get('record');

        /* Get record or create new */
        if(!empty($record)) {
            $recordModel = Settings_SPCMLConnector_Record_Model::getInstance($record);
        }else {
            $recordModel = new Settings_SPCMLConnector_Record_Model(); 
        }
        
        $qualifiedModuleName = $request->getModule(false);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('RECORD_MODEL',$recordModel);
        $viewer->view('EditStatus.tpl',$qualifiedModuleName);
    }
}