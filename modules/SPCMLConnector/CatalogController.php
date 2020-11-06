<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once "modules/SPCMLConnector/OperationController.php";
require_once "modules/SPCMLConnector/ProductsController.php";
require_once "modules/SPCMLConnector/ServicesController.php";

/**
 * Describes create/update and xml upload operation by CmlCatalog inventory.
 */
class CatalogController extends OperationController {
    private $productsController;
    private $servicesController;
    
    public function __construct($assignedUserName) {
        parent::__construct($assignedUserName);
        $this->productsController = new ProductsController($assignedUserName);
        $this->servicesController = new ServicesController($assignedUserName);
    }
    
    /**
     * Return catalog reference by ir name. If name no find - retur NULL.
     * @param String $catalogName
     * @return String|NULL
     */
    private function getReference($catalogName) {
        $result = $this->query("select id from PriceBooks where bookname='$catalogName';");
        return $this->getFirstReference($result);
    }
    
    /**
     * Prepare catalog description to save. Sets currency reference to catalog.
     * @param type $cmlCatalog
     * @return type
     */
    private function getCatalogRest($cmlCatalog) {
        $currencyReference = $this->getCurrencyReference($cmlCatalog->getCurrency());
        $cmlCatalog->setCurrency($currencyReference);
        
        return $cmlCatalog->toRestDescription();
    }
    
    /**
     * Updates catalog with id=$reference description and inventories by information from
     * $cmlCatalog.
     * @param String $reference
     * @param CmlCatalog $cmlCatalog
     */
    private function updateCatalog($reference, $cmlCatalog) {
        $restData = $this->getCatalogRest($cmlCatalog);
        $this->update($restData, $reference);
        
        /* REST API not provides methods to add inventories to catalog. */
        $this->updateCatalogInventories($reference, $cmlCatalog);
    }
    
    /**
     * Create new catalog in crm.
     * @param CmlCatalog $cmlCatalog
     */
    private function createCatalog($cmlCatalog) {
        $restData = $this->getCatalogRest($cmlCatalog);
        $catalogReference = $this->create('PriceBooks', $restData);
        
        $this->updateCatalogInventories($catalogReference, $cmlCatalog);
    }
    
    /**
     * Save products and services from $cmlCatalog and add them to Catalog with reference
     * $catalogReference.
     * @param String $catalogReference
     * @param CmlCatalog $cmlCatalog
     */
    private function updateCatalogInventories($catalogReference, $cmlCatalog) {
        $products = $cmlCatalog->getProducts();
        foreach($products as $cmlProduct) {
            $currencyId = $this->getCurrencyReference($cmlProduct->getCurrency());
            $cmlProduct->setCurrencyId($currencyId);
            
            $inventoryReference = $this->productsController->save($cmlProduct);
            $this->addInventory($catalogReference, $inventoryReference, $cmlProduct);
        }
        
        $services = $cmlCatalog->getServices();
        foreach($services as $cmlService) {
            $currencyId = $this->getCurrencyReference($cmlService->getCurrency());
            $cmlService->setCurrencyId($currencyId);
                    
            $inventoryReference = $this->servicesController->save($cmlService);
            $this->addInventory($catalogReference, $inventoryReference, $cmlService);
        }
    }
    
    /**
     * Add inventory to catalog in CRM.
     * @param String $catalogReference
     * @param String $productReference
     * @param AbstractProduct $abstractProduct
     */    
    private function addInventory($catalogReference, $inventoryReference, $abstractProduct) {
        $price = $abstractProduct->getPrice();
        $currencyReference = $this->getCurrencyReference($abstractProduct->getCurrency());
        $currency = $this->trimReference($currencyReference);
        $catalogId = $this->trimReference($catalogReference);
        $inventoryId = $this->trimReference($inventoryReference);
        
        $this->insertInventoryToCatalog($catalogId, $inventoryId, $price, $currency);
    }
    
    /**
     * Insert product to catalog.
     * @param int $catalodId
     * @param int $productId
     * @param double $price
     * @param int $currency
     */
    private function insertInventoryToCatalog($catalogId, $inventoryId, $price, $currency) {
        global $adb;
        $params = array($catalogId, $inventoryId, $price, $currency);
        $adb->pquery("INSERT INTO `vtiger_pricebookproductrel`
            (`pricebookid`, `productid`, `listprice`, `usedcurrency`)
            VALUES (?, ?, ?, ?);", $params);
    }
    
    /**
     * Return basic information of the CRM owner.
     * @return array
     */
    private function getOwnerRest() {
        $result = $this->query("select inn, organizationname from CompanyDetails;");
        return $this->getFirstQueryResult($result);
    }
    
    /**
     * Adds owner of the catalog to thr document.
     * @param type $document
     */
    private function addXmlOwner($document) {
        $organizationRest = $this->getOwnerRest();
        $xmlOwner = $document->addChild("Владелец");
        $xmlOwner->addChild("Ид", $organizationRest['inn']);
        $xmlOwner->addChild("Наименование", $organizationRest['organizationname']);
        $xmlOwner->addChild("ПолноеНаименование", $organizationRest['organizationname']);
    }
    
    /**
     * Return all catalogs REST description.
     * @return array
     */
    private function getAllCatalogsRest() {
        $result = $this->query("select * from PriceBooks;");
        return $result;
    }
    
    /**
     * Return ids of inventories, belong to catalog with reference $catalogReference.
     * @param String $catalogReference
     * @return array 
     */
    private function getCatalogInventoiresId($catalogReference) {
        global $adb;
        $params = array( $this->trimReference($catalogReference) );
        $queryResult = $adb->pquery("select productid from vtiger_pricebookproductrel where pricebookid=?;",$params);
        
        $inventoriesId = array();
        while($productId = $adb->fetchByAssoc($queryResult)) {
            $inventoriesId[] = $productId['productid'];
        }
        
        return $inventoriesId;
    }
    
    /**
     * Add to catalog inventories, without price and count.
     * @param SimpleXmlElement $catalogXml
     * @param String $catalogReference
     */
    private function addXmlInventories($catalogXml, $catalogReference) {
        $inventoriesContainer = $catalogXml->addChild("Товары");
        foreach($this->getCatalogInventoiresId($catalogReference) as $inventoryId) {
            $this->addXmlInventory($inventoriesContainer, $inventoryId);
        }
    }
    
    /**
     * Add invetory xml element to $inventoriesContainer.
     * @param SimpleXmlElement $inventoriesContainer
     * @param int $inventoryId
     */
    private function addXmlInventory($inventoriesContainer, $inventoryId) {
        $inventoryXml = $this->productsController->getCatalogXmlInventoryById($inventoryId);
        if($inventoryXml == null) {
            $inventoryXml = $this->servicesController->getCatalogXmlInventoryById($inventoryId);
        }
        
        /* In CRM may be deleted inventories, but not deleted in database */
        if($inventoryXml == null) {
            return;
        }
        
        $taxRateXml = $this->servicesController->getXmlInventoryTaxById($inventoryId);
        $this->appendXmlElement($inventoryXml, $taxRateXml);
        $this->appendXmlElement($inventoriesContainer, $inventoryXml);
    }
    
    /**
     * Adds identificator to catalog
     * @param SimpleXmlElement $document
     * @param array $catalogRest
     */
    private function addXmlCatalogIdentificator($document, $catalogRest) {
        if($catalogRest['one_s_id'] != null) {
            $document->addChild("Ид", $catalogRest['one_s_id']);
        } else {
            $document->addChild("Ид", $catalogRest['bookname']);
        }
    }
     
    /**
     * Adds identificator of the catalog, which offer describes it.
     * @param SimpleXmlElement $document
     * @param array $catalogRest
     */
    private function addXmlPackageIdentificator($document, $catalogRest) {
        if($catalogRest['one_s_id'] != null) {
            $document->addChild("ИдКаталога", $catalogRest['one_s_id']);
        } else {
            $document->addChild("ИдКаталога", $catalogRest['bookname']);
        }
    }
    
    /**
     * Add to $xmlOffer inventories with price and count, contains to catalog id = $reference
     * @param SimpleXmlElement $xmlOffer
     * @param String $reference
     */
    private function addXmlPackageInventories($xmlOffer, $reference) {
        $inventoriesContainer = $xmlOffer->addChild("Предложения");
        foreach($this->getCatalogInventoiresId($reference) as $inventoryId) {
            $this->addXmlPackageInventory($inventoriesContainer, $inventoryId);
        }
    }
    
    /**
     * Add to $inventoriesContainer inventory xml with id = $inventoryId 
     * @param SimpleXmlElement $inventoriesContainer
     * @param int $inventoryId
     */
    private function addXmlPackageInventory($inventoriesContainer, $inventoryId) {
        $inventoryXml = $this->productsController->getPackageXmlInventoryById($inventoryId);
        if($inventoryXml == null) {
            $inventoryXml = $this->servicesController->getPackageXmlInventoryById($inventoryId);
        }
        
        /* In CRM may be deleted inventories, before theu drop from catalog */
        if($inventoryXml == null) {
            return;
        }
        
        $this->appendXmlElement($inventoriesContainer, $inventoryXml);
    }

    /**
     * Return xml descriprion of the catalog.
     * @param array $catalogRest
     * @return String
     */
    private function getXmlCatalog($catalogRest) {
        $commerceData = $this->getCommerceHeader();
        $catalogXml = $commerceData->addChild("Каталог");
        $catalogXml->addAttribute("СодержитТолькоИзменения", "false");
        $catalogXml->addChild("Наименование", $catalogRest['bookname']);
        
        /* Add information, which need select from other entities */
        $this->addXmlCatalogIdentificator($catalogXml, $catalogRest);
        $this->addXmlOwner($catalogXml);
        $this->addXmlInventories($catalogXml, $catalogRest['id']);
        
        return $commerceData->asXML();
    }
    
    /**
     * Add Element, contains information of the price type of the package.
     * @param SimpleXmlElement $document
     * @param array $catalogRest
     */
    private function addXmlPriceType($document, $catalogRest) {
        $priceTypes = $document->addChild("ТипыЦен");
        
        /* in current version identifier of price type is currency CODE */
        $price = $priceTypes->addChild("ТипЦены");
        $price->addChild("Ид", 1);     
        $price->addChild("Валюта", $this->getCurrencyCode($catalogRest['currency_id']));
        $price->addChild("Наименование", "Розничная");
        
        $tax = $price->addChild("Налог");
        $tax->addChild("Наименование", "НДС");
        $tax->addChild("УчтеноВСумме", "false");
    }
    
    /**
     * Return offers of the catalog by it Rest.
     * @param array $catalogRest
     * @return String
     */
    private function getXmlOffer($catalogRest) {
        $commerceData = $this->getCommerceHeader();
        $xmlOffer = $commerceData->addChild("ПакетПредложений");
        $xmlOffer->addChild("Наименование", $catalogRest['bookname']);
        
        /* Add information, which need select from other entities */
        $this->addXmlCatalogIdentificator($xmlOffer, $catalogRest);
        $this->addXmlPackageIdentificator($xmlOffer, $catalogRest);
        $this->addXmlOwner($xmlOffer);
        $this->addXmlPriceType($xmlOffer, $catalogRest);
        $this->addXmlPackageInventories($xmlOffer, $catalogRest['id']);
        
        return $commerceData->asXML();
    }
    
    /**
     * Updates information in CRM about catalog.$assignedUserName
     * @param CmlCatalog $cmlCatalog
     */
    public function save($cmlCatalog) {
        $catalogName = $cmlCatalog->getName();
        $reference = $this->getReference($catalogName);
        if($reference != null) {
            $this->updateCatalog($reference, $cmlCatalog);
        } else {
            $this->createCatalog($cmlCatalog);
        }
    }
    
    /**
     * Return array of catalogs description in xml format by CommerceML standart.
     * @return array<String>
     */
    public function getXmlCatalogs() {
        $catalogs = array();
        foreach($this->getAllCatalogsRest() as $catalogRest) {
            $catalogs[] = $this->getXmlCatalog($catalogRest);
        }
        return $catalogs;
    }
    
    
    /**
     * Return array of offers description in xml format by CommerceML standart.
     * @return array<String>
     */
    public function getXmlOffers() {
        $offers = array();
        foreach($this->getAllCatalogsRest() as $catalogRest) {
            $offers[] = $this->getXmlOffer($catalogRest);
        }

        return $offers;
    }
}
