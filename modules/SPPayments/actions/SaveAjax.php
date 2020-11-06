<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPPayments_SaveAjax_Action extends Vtiger_SaveAjax_Action {
    
    public function getRecordModelFromRequest(Vtiger_Request $request) {
        $recordModel = parent::getRecordModelFromRequest($request);
        
        /* Add related customer when create from related list */
        $recordId = $request->get('record');
        if(empty($recordId)) {
            $sourceModule = $request->get('sourceModule');
            $accountId = $request->get('relatedorganization');
            if(!empty($accountId) && ($sourceModule == 'Invoice' || $sourceModule == 'SalesOrder')) {
                $recordModel->set('payer', $accountId);
            }
            
            $vendorId = $request->get('relatedvendor');
            if(!empty($vendorId) && $sourceModule == 'PurchaseOrder') {
                $recordModel->set('payer', $vendorId);
            }
        }

        return $recordModel;
    }
    
}
