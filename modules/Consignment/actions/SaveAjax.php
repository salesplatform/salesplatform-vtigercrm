<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Consignment_SaveAjax_Action extends Inventory_SaveAjax_Action {

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');

		if ($recordId && $_REQUEST['action'] == 'SaveAjax') {
			// While saving Consignment record Line items quantities should not get updated
			// This is a dependency on the older code, where in Consignment save_module we decide wheather to update or not.
			$_REQUEST['action'] = 'ConsignmentAjax';
		}

		return parent::process($request);
	}
}
