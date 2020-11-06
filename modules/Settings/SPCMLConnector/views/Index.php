<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPCMLConnector_Index_View extends Settings_Vtiger_Index_View {
    
    /* Не надо делать ListView - чрезе foreach формируем результаты и все обновляем JS как в currency */
    
    /**
     * Indicates user view by module model.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $qualifiedModuleName = $request->getModule(false);  //full name in Settings module

            /* Model of module see  ../models/Module.php */
            $moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
            
            /* To get statuses */
            $listViewModel = Settings_Vtiger_ListView_Model::getInstance($qualifiedModuleName);
            
            $pagingModel = new Vtiger_Paging_Model();
            
            /* Smarty display viewer */
            $viewer = $this->getViewer($request);
            $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
            $viewer->assign('MODEL', $moduleModel);
            $viewer->assign('USERS', Users_Record_Model::getAll(true));
            
            /* To display currnet statuses settings */
            $viewer->assign('LISTVIEW_HEADERS', $listViewModel->getListViewHeaders());
            $viewer->assign('LISTVIEW_ENTRIES', $listViewModel->getListViewEntries($pagingModel));
            $viewer->view('Index.tpl', $qualifiedModuleName);
    }
        
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
                "modules.Settings.$moduleName.resourses.$moduleName"
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
