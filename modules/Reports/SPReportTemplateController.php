<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/


require_once 'includes/SalesPlatform/PDF/SPPDFController.php';

class SPReportTemplateController extends SalesPlatform_PDF_SPPDFController {
    
    private $templateModel;
    
    private $prefixesMap = array(
        'Accounts' => 'account_',
        'Act' => 'act_',
        'Assets' => 'asset_',
        'Calendar' => 'activity_',
        'Campaigns' => 'campaign_',
        'Consignment' => 'consignment_',
        'Contacts' => 'contact_',
        'Documents' => 'note_',
        'Emails' => 'activity_',
        'Events' => 'activity_',
        'Faq' => 'faq_',
        'HelpDesk' => 'troubleticket_',
        'Invoice' => 'invoice_',
        'Leads' => 'lead_',
        'ModComments' => 'modcomment_',
        'PBXManager' => 'pbxmanager_',
        'Potentials' => 'potential_',
        'PriceBooks' => 'pricebook_',
        'Products' => 'product_',
        'Project' => 'project_',
        'ProjectMilestone' => 'projectmilestone_',
        'ProjectTask' => 'projecttask_',
        'PurchaseOrder' => 'purchaseorder_',
        'Quotes' => 'quote_',
        'SMSNotifier' => 'smsnotifier_',
        'SPSocialConnector' => 'sp_socialconnector',
        'SPPayments' => 'sp_payment_',
        'SPUnits' => 'sp_unit_',
        'SalesOrder' => 'salesorder_',
        'ServiceContracts' => 'servicecontract_',
        'Services' => 'service_',
        'Vendors' => 'vendor_',
    );
    
    public function __construct() {
        $this->templateModel = new Vtiger_PDF_Model();
    }
    
    public function loadRecord($id) {
        $recordModel = Vtiger_Record_Model::getInstanceById($id);
        $this->moduleName = $recordModel->getModuleName();
        $this->focus = $recordModel->getEntity();
        $this->buildRecordTemplateModel();
    }
    
    /**
     * Check transmitted value fpr template match
     * @param type $value
     * @return boolean
     */
    public function isTemplateValue($value) {
        foreach($this->prefixesMap as $prefix) {
            if(strpos($value, $prefix) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check is template value exists in controller or not
     * @param type $value
     */
    public function isTemplateValueExists($value) {
        return in_array($value, $this->templateModel->keys());
    }
    
    /**
     * Return real value matches to template
     * @param string $templateValue
     * @return type
     */
    public function getTransformedValue($templateValue) {
        return $this->templateModel->get($templateValue, $templateValue);
    }
    
    private function buildRecordTemplateModel() {
        $this->templateModel = new Vtiger_PDF_Model();
        $this->generateEntityModel($this->focus, $this->moduleName, $this->getModuleFieldsPrefix(), $this->templateModel);
    }
    
    private function getModuleFieldsPrefix() {
        if(array_key_exists($this->moduleName, $this->prefixesMap)) {
            return $this->prefixesMap[$this->moduleName];
        }
        return '';
    }
}