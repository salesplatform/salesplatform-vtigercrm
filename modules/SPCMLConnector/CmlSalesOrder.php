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
 * Description of CmlSalesOrder
 * @author alexd
 */
class CmlSalesOrder {
    private $number;
    private $oneEsIdentifier;
    private $produtcs;
    private $services;
    private $account;
    private $currency;
    private $props;
    
    /**
     * Init not inventoy part of CmlSalesOrder.
     * @param type $number
     * @param type $oneEsidentifier
     * @param type $currency
     */
    public function __construct($number, $oneEsidentifier, $currency) {
        $this->number = $number;
        $this->oneEsIdentifier = $oneEsidentifier;
        $this->currency = $currency;
        $this->produtcs = array();
        $this->services = array();
        $this->props = array();
    }
    
    /**
     * Add product to order.
     * @param CmlProduct $product
     */
    public function addProduct($product) {
        array_push($this->produtcs, $product);
    }
    
    /**
     * Add service to order
     * @param CmlService $service
     */
    public function addService($service) {
        array_push($this->services, $service);
    }
    
    /**
     * Add CmlAccount to order.
     * @param CmlAccount $account
     */
    public function addAccount($account) {
        $this->account = $account;
    }
    
    /**
     * Add prop to CmlSalesOrder
     * @param String $name
     * @param String $value
     */
    public function addProp($name, $value) {
        $this->props[$name] = $value;
    }
    
    /**
     * Return values of prop. If prop no exists - return null.
     * @param String $propName
     * @return String
     */
    public function getPropValue($propName) {
        return $this->props[$propName];
    }
    
    public function getNumber() {
        return  $this->number;
    }
    
    public function getAccount() {
        return $this->account;
    }
    
    public function getCurrency() {
        return $this->currency;
    }
    
    /**
     * Return products
     * @return array<CmlProduct>
     */
    public function getProducts() {
        return $this->produtcs;
    }
    
    /**
     * Return Services
     * @return array<CmlService>
     */
    public function getServices() {
        return $this->services;
    }
    
    public function getOneEsId() {
        return $this->oneEsIdentifier;
    }
}
