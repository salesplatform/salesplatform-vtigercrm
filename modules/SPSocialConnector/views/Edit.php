<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPSocialConnector_Edit_View extends Vtiger_Edit_View {
    
    /**
     * Edit record not allowed
     * 
     * @param \Vtiger_Request $request
     * @throws AppException
     */
    public function checkPermission(\Vtiger_Request $request) {
        throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
    }
}
