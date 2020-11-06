<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPDynamicBlocks_List_View extends Settings_Vtiger_List_View {    
    
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        parent::initializeListViewContents($request, $viewer);
        $viewer->assign('SHOW_LISTVIEW_CHECKBOX', false);
    }
}