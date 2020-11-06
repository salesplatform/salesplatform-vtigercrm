<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class SPPayments_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
		$isRelationOperation = $request->get('relationOperation');
        $record = $request->get('record');
        /* If it's relation operation then adding to record model 
           values from related modules for display on Edit page */
        if ($isRelationOperation && empty($record)) {
            $moduleName = $request->getModule();
            $return_module = $request->get('sourceModule');
            // Get clean record model
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            // Include related module main class
            require_once ("modules/$return_module/$return_module.php");
            //SalesPlatform.ru begin
            //$return_focus = new $return_module();
            $return_focus = CRMEntity::getInstance($return_module);
            //SalesPlatform.ru end
            $return_focus->id = $request->get('sourceRecord');
            $return_focus->retrieve_entity_info($request->get('sourceRecord'), $return_module);
            
            if ($return_module == 'Accounts' || $return_module == 'Contacts'
                    || $return_module == 'Vendors') {
                $payer = $return_focus->column_fields['record_id'];
            } elseif ($return_module == 'Invoice' || $return_module == 'SalesOrder'
                    || $return_module == 'PurchaseOrder') {
                $related_to = $return_focus->column_fields['record_id'];
                $amount = number_format($return_focus->column_fields['hdnGrandTotal'],2,'.','');
                if ($return_module == 'PurchaseOrder') {
                    $payer = $return_focus->column_fields['vendor_id'];
                } else {
                    $payer = $return_focus->column_fields['account_id'];
                }
            }
            // Add values to model
            $recordModel->set("payer", $payer);
            $recordModel->set("related_to", $related_to);
            $recordModel->set("amount", $amount);
            
            $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
            $recordStructureInstance->getStructure();
        }

        parent::process($request);
	}

}
