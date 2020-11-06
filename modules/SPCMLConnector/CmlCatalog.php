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
 * Description of CmlCatalog
 * @author alexd
 */
class CmlCatalog {
    private $name;
    private $oneEsIdentifier;
    private $currency;
    private $products;
    private $services;
    
    public function __construct($name, $oneEsIdentifier) {
        $this->name = $name;
        $this->oneEsIdentifier = $oneEsIdentifier;
        $this->currency = NULL;
        $this->products = array();
        $this->services = array();
    }
    
    /**
     * Add CmlProduct to catalog.
     * @param CmlProduct $product
     */
    public function addProduct($product) {
        array_push($this->products, $product);
    }
    
    /**
     * Retur all catalog products as array.
     * @return array<CmlProduct>
     */
    public function getProducts() {
        return $this->products;
    }
    
    /**
     * Return all catalog services as array.
     * @return array
     */
    public function getServices() {
        return $this->services;
    }
    
    /**
     * Set CmlCatalog services
     * @param array<CmlService> $services
     */
    public function setServices($services) {
        $this->services = $services;
    }
    
    /**
     * Set CmlCatalog products.
     * @param array<CmlProducts> $products
     */
    public function setProducts($products) {
        $this->products = $products;
    }
    
    /**
     * Add CmlServise to catalog.
     * @param CmlService $service
     */
    public function addService($service) {
        array_push($this->services, $service);
    }
    
    /**
     * Retrun currency code.
     * @return String
     */
    public function getCurrency() {
        return $this->currency;
    }
    
    /**
     * Set currency of catalog.
     * @param String $currency
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    
    /**
     * Reutrn catalog name.
     * @return String
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set default inventories currency
     * @param type $currencyCode
     */
    public function setInventoriesCurrencyCode($currencyCode) {
        foreach($this->products as $product) {
            $product->setCurrency($currencyCode);
        }
        
        foreach($this->services as $service) {
            $service->setCurrency($currencyCode);
        }
    }
    
    public function toRestDescription() {
        $restDescription['bookname'] = $this->name;
        $restDescription['active'] = 1;           //active catalog
        $restDescription['one_s_id'] = $this->oneEsIdentifier;
        $restDescription['currency_id'] = $this->currency;
        
        return $restDescription;
    }
}
