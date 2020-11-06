<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

include_once 'includes/SalesPlatform/PDF/SPPDFController.php';

class SalesPlatform_SPPaymentsPDFController extends SalesPlatform_PDF_SPPDFController {

	function buildDocumentModel() {
        global $app_strings;

        try {
            $model = parent::buildDocumentModel();

            $this->generateEntityModel($this->focus, 'SPPayments', 'payment_', $model);

            $this->generateUi10Models($model);
            $this->generateRelatedListModels($model);

            $model->set('payment_owner', getUserFullName($this->focusColumnValue('assigned_user_id')));
            $model->set('payment_payer', getParentName($this->focusColumnValue('payer')));
            return $model;

        } catch (Exception $e) {
            echo '<meta charset="utf-8" />';
            if($e->getMessage() == $app_strings['LBL_RECORD_DELETE']) {
                echo $app_strings['LBL_RECORD_INCORRECT'];
                echo '<br><br>';
            } else {
                echo $e->getMessage();
                echo '<br><br>';
            }
            return null;
        }
    }

}
?>
