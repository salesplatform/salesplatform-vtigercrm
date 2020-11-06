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
require_once 'modules/Products/Products.php';
require_once 'modules/Accounts/Accounts.php';
require_once 'modules/Contacts/Contacts.php';

class SalesPlatform_HelpDeskPDFController extends SalesPlatform_PDF_SPPDFController {

    function buildDocumentModel() {
        global $app_strings;

        try {
            $model = parent::buildDocumentModel();

		$this->generateEntityModel($this->focus, 'HelpDesk', 'helpdesk_', $model);

                $entity = CRMEntity::getInstance('Products');
                if($this->focusColumnValue('product_id'))
            	    $entity->retrieve_entity_info($this->focusColumnValue('product_id'), 'Products');
                $this->generateEntityModel($entity, 'Products', 'product_', $model);

                if($this->focusColumnValue('parent_id'))
                    $setype = getSalesEntityType($this->focusColumnValue('parent_id'));

                $account = CRMEntity::getInstance('Accounts');
                $contact = CRMEntity::getInstance('Contacts');

                if($setype == 'Accounts')
           	    $account->retrieve_entity_info($this->focusColumnValue('parent_id'), $setype);
                elseif($setype == 'Contacts')
           	    $contact->retrieve_entity_info($this->focusColumnValue('parent_id'), $setype);

                $this->generateEntityModel($account, 'Accounts', 'account_', $model);
                $this->generateEntityModel($contact, 'Contacts', 'contact_', $model);

                $this->generateUi10Models($model);
                $this->generateRelatedListModels($model);

                $model->set('helpdesk_owner', getUserFullName($this->focusColumnValue('assigned_user_id')));

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

