<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	
include_once 'include/InventoryPDFController.php';

class Vtiger_QuotesPDFController extends Vtiger_InventoryPDFController{
	function buildHeaderModelTitle() {
		$singularModuleNameKey = 'SINGLE_'.$this->moduleName;
		$translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
		if($translatedSingularModuleLabel == $singularModuleNameKey) {
			$translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
		}
		return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('quote_no'));
	}

	function getWatermarkContent() {
		return $this->focusColumnValue('quotestatus');
	}

	function buildHeaderModelColumnRight() {
		$issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
		$validDateLabel = getTranslatedString('Valid Date', $this->moduleName);
		$billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
		$shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

		$modelColumn2 = array(
				'dates' => array(
					$issueDateLabel  => $this->formatDate(date("Y-m-d")),
					$validDateLabel => $this->formatDate($this->focusColumnValue('validtill')),
				),
				$billingAddressLabel  => $this->buildHeaderBillingAddress(),
				$shippingAddressLabel => $this->buildHeaderShippingAddress()
			);
		return $modelColumn2;
	}
}


//SalesPlatform.ru begin

include_once 'includes/SalesPlatform/PDF/ProductListPDFController.php';
require_once 'modules/Potentials/Potentials.php';
require_once 'modules/Accounts/Accounts.php';

class SalesPlatform_QuotesPDFController extends SalesPlatform_PDF_ProductListDocumentPDFController{

    function buildDocumentModel() {
        global $app_strings;

        try {
            $model = parent::buildDocumentModel();

            $this->generateEntityModel($this->focus, 'Quotes', 'quote_', $model);

            //SalesPaltform.ru begin
            $entity = CRMEntity::getInstance('Potentials');
            //$entity = new Potentials();
            //SalesPaltform.ru end
            if ($this->focusColumnValue('potential_id'))
                $entity->retrieve_entity_info($this->focusColumnValue('potential_id'), 'Potentials');
            $this->generateEntityModel($entity, 'Potentials', 'potential_', $model);

            //SalesPaltform.ru begin
            $entity = CRMEntity::getInstance('Accounts');
            //$entity = new Accounts();
            //SalesPlatform.ru end
            
            if ($this->focusColumnValue('account_id'))
                $entity->retrieve_entity_info($this->focusColumnValue('account_id'), 'Accounts');
            $this->generateEntityModel($entity, 'Accounts', 'account_', $model);

            //SalesPaltform.ru begin
            $entity = CRMEntity::getInstance('Contacts');
            //$entity = new Contacts();
            //SalesPlatform.ru end
            
            if ($this->focusColumnValue('contact_id'))
                $entity->retrieve_entity_info($this->focusColumnValue('contact_id'), 'Contacts');
            $this->generateEntityModel($entity, 'Contacts', 'contact_', $model);

            $this->generateUi10Models($model);
            $this->generateRelatedListModels($model);

            $model->set('quote_no', $this->focusColumnValue('quote_no'));
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

    function getWatermarkContent() {
        return '';
    }

}

//SalesPlatform.ru end
?>
