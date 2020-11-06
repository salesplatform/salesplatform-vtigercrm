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
 * Describes abstract cml product.
 */
abstract class AbstractProduct {
    protected $name;
    protected $price;
    protected $article;
    protected $unitName;
    protected $stockCount;
    protected $oneEsIdentifier;
    protected $NDS;
    protected $currency;
    protected $currencyId;
    protected $conversionRate;


    /*
     * Basic product initilization.
     */
    public function __construct() {
        $this->NDS = 0;
    }
    
    /**
     * Returns name of the product.
     * @return String
     */
    public function getName() {
        return $this->name;
    }
    
    public function getArticle() {
        return $this->article;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getCurrency() {
        return $this->currency;
    }
    
    /**
     * Return one es identifier of the product.
     * @return String
     */
    public function getOneEsIdentifier() {
        return $this->oneEsIdentifier;
    }
    
    /**
     * Compares two products by it's one es identifiers.
     * @param AbstractProduct $product
     * @return boolean
     */
    public function compare($product) {
        if($this->oneEsIdentifier == $product->oneEsIdentifier) {
            return true;
        }
        return false;
    }
    
    /**
     * If one es identifiers of the products match - return true.
     * @param CmlProduct $product
     * @return boolean
     */
    public function isSame($product) {
        if($this->oneEsIdentifier == $product->oneEsIdentifier) {
            return true;
        }
        return false;
    }
    
    /**
     * Add to current product price, currency, conversion rate and stock count
     * of the product.
     * @param AbstractProduct $offerProduct
     */
    public function mergeImportWithOffer($offerProduct) {
        $this->price = $offerProduct->price;
        $this->stockCount = $offerProduct->stockCount;
        $this->currency = $offerProduct->currency;
        $this->conversionRate = $offerProduct->conversionRate;
    }
    
    /**
     * If tax not equals 0  - return true.
     * @return boolean
     */
    public function isNDS() {
        if($this->NDS == 0) {
            return false;
        }
        return true;
    }
    
    /**
     * Set rate of tax.
     * @param int $value
     */
    public function setNDSValue($value) {
        $this->NDS = $value;
    }
    
    /**
     * Return rate of tax.
     * @return int
     */
    public function getNDS() {
        return $this->NDS;
    }
    
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    
    public function setCurrencyId($currencyId) {
        $this->currencyId = $currencyId;
    }
    
    /**
     * Initilizate product by values from document named catalog
     * @param type $oneEsIdentifier
     * @param type $name
     * @param type $article
     * @param type $unitName
     * @param type $NDS
     */
    public function catalogInitilizate($oneEsIdentifier, $name, $article, $unitName, $NDS) {
        $this->oneEsIdentifier = $oneEsIdentifier;
        $this->name = $name;
        $this->article = $article;
        $this->unitName = $unitName;
        $this->NDS = $NDS;
    }
    
    /**
     * Initilizate product by values from document named offers.
     * @param type $oneEsIdentifier
     * @param type $name
     * @param type $price
     * @param type $currency
     * @param type $conversionRate
     * @param type $count
     */
    public function offersInitilizate($oneEsIdentifier, $name, $price, $currency, $conversionRate, $count) {
        $this->oneEsIdentifier = $oneEsIdentifier;
        $this->name = $name;
        $this->price = $price;
        $this->currency = $currency;
        $this->conversionRate = $conversionRate;
        $this->stockCount = $count;
    }
    
    /**
     * Return count of product.
     * @return int
     */
    public function getCount() {
        return $this->stockCount;
    }
    
    /**
     * Initilizate product from document, named order.
     * @param type $oneEsIdentifier
     * @param type $name
     * @param type $article
     * @param type $unitName
     * @param type $price
     * @param type $count
     */
    public function orderInitilizate($oneEsIdentifier, $name, $article, $unitName, $price, $count, $NDS) {
        $this->oneEsIdentifier = $oneEsIdentifier;
        $this->name = $name;
        $this->article = $article;
        $this->unitName = $unitName;
        $this->price = $price;
        $this->stockCount = $count;
        $this->NDS = $NDS;
    }
    
    public abstract  function toRestDescription();
}
