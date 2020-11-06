<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

vimport('~~/modules/Consignment/ConsignmentPDFController.php');

class Consignment_ExportPDF_Action extends Inventory_ExportPDF_Action {
    public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        ob_clean();     // always '/n' in buffer  ??? 
		//SalesPlatform.ru begin get PDF template
        $templateId = $request->get('templateid');
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		//$recordModel->getPDF();
        $recordModel->getSalesPlatformPDF($templateId);
        //SalesPlatform.ru end
	}
}
