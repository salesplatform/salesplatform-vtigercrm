<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_History_View extends Settings_Vtiger_Index_View {
    
    /* Не надо делать ListView - чрезе foreach формируем результаты и все обновляем JS как в currency */
    
    /**
     * Indicates user view by module model.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request) {

            $qualifiedModuleName = $request->getModule(false);  //full name in Settings module
            $moduleModel = new Settings_SPCMLConnector_History_Model();
            /* Smarty display viewer */
            $viewer = $this->getViewer($request);
            $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
            $viewer->assign('MODEL', $moduleModel);
            $viewer->view('History.tpl', $qualifiedModuleName);
    }
}