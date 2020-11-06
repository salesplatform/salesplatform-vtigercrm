<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once "modules/SPCMLConnector/InventoryController.php";

/**
 * Describes operations to update, create and xml upload products.
 * @author alexd
 */
class ProductsController extends InventoryController {
    
    public function __construct($assignedUserName) {
        parent::__construct($assignedUserName, 'Products');
    }
    
    /**
     * Add to product xml description identifier by REST info
     * @param SimpleXmlElement $xmlProduct
     * @param array $productRest
     */
    private function addXmlIdentifier($xmlProduct, $productRest) {
        if($productRest['one_s_id'] != null ) {
            $xmlProduct->addChild("Ид", $productRest['one_s_id']); 
        } else {
            $xmlProduct->addChild("Ид", $productRest['productname']); 
        }
    }
    
    /**
     * Add props to xml description, specified for product. Need to identificate
     * inventory as product in One Es.
     * @param SimpleXmlElement $xmlProduct
     */
    private function addXmlProps($xmlProduct) {
        $props = $xmlProduct->addChild("ЗначенияРеквизитов");
        $prop = $props->addChild("ЗначениеРеквизита");
        $prop->addChild("Наименование","ВидНоменклатуры");
        $prop->addChild("Значение","Товар");
        $prop = $props->addChild("ЗначениеРеквизита");
        $prop->addChild("Наименование","ТипНоменклатуры");
        $prop->addChild("Значение","Товар");
    }
    
    /**
     * Get refernece by CmlProduct.
     * @param CmlProduct $abstractProduct
     */
    public function getReference($abstractProduct) {
        $name = $abstractProduct->getName(); 
 	$article = $abstractProduct->getArticle();
        if($article != null) {
            $result = $this->query("select id from Products where productname='$name' "
                . "and productcode='$article';");       //if in article null - request generate error
        } else {
            $result = $this->query("select id from Products where productname='$name';");
        }
        
        return $this->getFirstReference($result);
    }
    
    /**
     * Return SimpleXmlElement of service by it id or null if product not exists.
     * @param int $id
     * @return SimpleXmlElement | null
     */
    public function getCatalogXmlInventoryById($id) {
        $result = $this->query("select * from Products where id=x$id;");    //rest need prefix in request or will be error
        $productRest = $this->getFirstQueryResult($result);
        
        if($productRest != null) {
            $xmlProduct = new SimpleXMLElement("<Товар></Товар>");
            $xmlProduct->addChild("Наименование", $productRest['productname']);
            $xmlProduct->addChild("Артикул", $productRest['productcode']);
            $this->addUsageUnit($xmlProduct, $productRest['usageunit']);
            $xmlProduct->addChild("Единица", vtranslate($productRest['usageunit']));
            $this->addXmlIdentifier($xmlProduct, $productRest);
            $this->addXmlProps($xmlProduct);
        }
        
        return $xmlProduct;
    }
    
    /**
     * Return product xml element, describes its offer.
     * @param int $inventoryId
     * @return SimpleXMLElement
     */
    public function getPackageXmlInventoryById($inventoryId) {
        $result = $this->query("select * from Products where id=x$inventoryId;");    //rest need prefix in request
        $productRest = $this->getFirstQueryResult($result);
        
        if($productRest != null) {
            $productRest['currency_code'] = $this->getInventoryCurrencyCode($inventoryId);  //rest not return currency
            
            $xmlProduct = new SimpleXMLElement("<Предложение></Предложение>");
            $xmlProduct->addChild("Наименование", $productRest['productname']);
            $xmlProduct->addChild("Количество", $productRest['qtyinstock']);
            $this->addXmlIdentifier($xmlProduct, $productRest);
            $this->addXmlPrice($xmlProduct, $productRest);
        }
        return $xmlProduct;
    }
}
