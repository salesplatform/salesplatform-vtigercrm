<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

/**
 * Describes account in CRM
 */
class CmlAccount { 
    
    private $name;
    private $oneEsIdentifier;
    private $billingStreet;
    private $shippingStreet;
    private $inn;
    private $kpp;
    
    public function __construct($name, $oneEsIdentifier) {
        $this->name = $name;
        $this->oneEsIdentifier = $oneEsIdentifier;
        $this->billingStreet = "-";
        $this->shippingStreet = "-";
    }
    
    /**
     * Initilizate billing and shipping addresses.
     * @param String $billing
     * @param String $shipping
     */
    public function initAccountAddress($billing, $shipping) {
        $this->billingStreet = $billing;
        $this->shippingStreet = $shipping;
    }
    
    public function initAccountTaxInfo($inn, $kpp) {
        $this->inn = $inn;
        $this->kpp = $kpp;
    }
    
    /**
     * Return account name.
     * @return String
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Return accounts oneEs identifier.
     * @return String
     */
    public function getOneEsIdentifier() {
        return $this->oneEsIdentifier;
    }
    
    public function toRestDescription() {
        $restDescription['accountname'] = $this->name;
        $restDescription['discontinued'] = 1;           //active paccount
        $restDescription['bill_street'] = $this->billingStreet;
        $restDescription['ship_street'] = $this->shippingStreet;
        $restDescription['one_s_id'] = $this->oneEsIdentifier;
        $restDescription['inn'] = $this->inn;
        $restDescription['kpp'] = $this->kpp;
        
        return $restDescription;
    }
}
