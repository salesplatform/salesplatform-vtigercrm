<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPCMLConnector/CmlParserFactory.php';
require_once 'modules/SPCMLConnector/ParseException.php';
require_once 'modules/SPCMLConnector/CmlCatalog.php';
require_once 'modules/SPCMLConnector/CmlProduct.php';
require_once 'modules/SPCMLConnector/CmlService.php';
require_once 'modules/SPCMLConnector/CmlSalesOrder.php';
require_once 'modules/SPCMLConnector/CmlAccount.php';


/**
 * Provides API to parse two types of documents - Catalog/Package, and Order Document
 */
class CmlParser {
    
    const CML_VERSION_ATTRIBUTE_NAME = 'ВерсияСхемы';
    
    
    /**
     * Create SimpleXMLElement describes commerce data. 
     * If document not valid - throw Exception.
     * @param String $xmlData
     * @return \SimpleXMLElement
     * @throws ParseException
     */
    private function getCommerceData($xmlData) {
        try {
            $commerceData = new SimpleXMLElement($xmlData);
            return $commerceData;
        } catch (Exception $ex) {
            throw new ParseException("Wrong import file contents!");
        }
    }

    /**
     * Join prices of products from offers package with catalog services and
     * products. Joining inventories by it's one es identifier.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     */
    private function joinImportWithOffer($catalog, $package) {
        $catalog->setCurrency($package->getCurrency());
        $catalog->setInventoriesCurrencyCode($package->getCurrency());
        $catalog = $this->joinImportWithOfferProducts($catalog, $package);
        $catalog = $this->joinImportWithOfferServices($catalog, $package);

        return $catalog;
    }
    
    /**
     * Add to services in $catalog prices, count and currency from $package.
     * Return updated $catalog.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     * @return CmlCatalog
     */
    private function joinImportWithOfferServices($catalog, $package) {
        $catalogServices = $catalog->getServices();
        foreach($catalogServices as $number => $catalogService) {     //key need to change product in array
            foreach($package->getProducts() as $packageInventory) {     //in package all inventories as products
                if($catalogService->compare($packageInventory)) {
                    
                    /* Add price, currency and count of service - and reinitilizate array of services */
                    $catalogService->mergeImportWithOffer($packageInventory);
                    $catalogServices[$number] = $catalogService;
                    break;      //no need search more products
                }
            }
        }
        
        /* Reinit catalog services */
        $catalog->setServices($catalogServices);
        return $catalog;
    }
    
    /**
     * Add to products in $catalog prices, count and currency from $package.
     * Return updated $catalog.
     * @param CmlCatalog $catalog
     * @param CmlCatalog $package
     * @return CmlCatalog
     */
    private function joinImportWithOfferProducts($catalog, $package) {
        $catalogProducts = $catalog->getProducts();
        foreach($catalogProducts as $number => $catalogProduct) {     //key need to change product in array
            foreach($package->getProducts() as $packageProduct) {     //in package all inventories as products
                if($catalogProduct->compare($packageProduct)) {
                    
                    /* Add price, currency and count of product - and reinitilizate array of products */
                    $catalogProduct->mergeImportWithOffer($packageProduct);
                    $catalogProducts[$number] = $catalogProduct;
                    break;      //no need search more products
                }
            }
        }
        
        /* Reinit catalog products */
        $catalog->setProducts($catalogProducts);
        return $catalog;
    }

    /**
     * 
     * @param SimpleXMLElement $documentXml
     */
    private function getConcreceParser($documentXml) {
        $cmlVersion = (string) $documentXml[CmlParser::CML_VERSION_ATTRIBUTE_NAME];
        if($cmlVersion == null) {
            throw new ParseException("Empty CommerceML version");
        }
        
        return CmlParserFactory::getParser($cmlVersion);
    }
    
    /**
     * Parse import and offer of catalog described in xml and return CmlCatalog
     * representation of parsed documents. If error on parse - throw Exception.
     * @param String $import
     * @param String $offer
     * @return CmlCatalog
     */
    public function parseCatalog($import, $offer) {
        $importXml = $this->getCommerceData($import);
        $offerXml = $this->getCommerceData($offer);
        
        $concreceParser = $this->getConcreceParser($importXml);
        $catalog = $concreceParser->parseImport($importXml);
        $package = $concreceParser->parseOffer($offerXml);
        
        return $this->joinImportWithOffer($catalog, $package);
    }
    
    /**
     * Parse orders described in xml and return array of CmlSalesOrder
     * @param string $order
     * @return CmlSalesOrder[]
     */
    public function parseOrders($order) {
        $ordersXml = $this->getCommerceData($order);
        $concreceParser = $this->getConcreceParser($ordersXml);
        
        return $concreceParser->parseOrders($ordersXml);
    }
}
