<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPSocialConnector_Index_View extends Settings_Vtiger_Index_View{

    public function process(Vtiger_Request $request) {
        $recordModel = Settings_SPSocialConnector_Record_Model::getInstance();
        $moduleModel = Settings_SPSocialConnector_Module_Model::getCleanInstance();
        $viewer = $this->getViewer($request);
        
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MODULE', $request->getModule(false));
        $viewer->assign('QUALIFIED_MODULE', $request->getModule(false));
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->view('Index.tpl', $request->getModule(false));
    }

}
