<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_Save_Action extends Settings_Vtiger_Basic_Action {
    public function process(Vtiger_Request $request) {
        
        /* Get settings from request settings */
        $adminLogin = $request->get('adminLogin');
        $adminPassword = $request->get('adminPassword');
        $websiteURL = $request->get('websiteURL');
        $assignedUser = $request->get('assignedUser');
        
        /* Save settings */
        $qualifiedModuleName = $request->getModule(false);
        $moduleModel = Settings_SPCMLConnector_Module_Model::getInstance($qualifiedModuleName);
        $moduleModel->setAdminLogin($adminLogin);
        $moduleModel->setAdminPassword($adminPassword);
        $moduleModel->setSiteUrl($websiteURL);
        $moduleModel->setAssignedUser($assignedUser);
        
        /* Answer */
        $responce = new Vtiger_Response();
        $responce->setResult(array('success'=>true));
        $responce->emit();
    }
}