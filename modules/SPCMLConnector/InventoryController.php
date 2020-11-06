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

/**
 * Describes operations of save, update and xml upload of abstract product.
 */
abstract class InventoryController extends OperationController {
    protected $module;
    const NDS_TAX_ID = 1;
    
    public function __construct($assignedUserName, $module) {
        parent::__construct($assignedUserName);
        $this->module = $module;
    }
    
    abstract function getReference($abstractProduct);
    
    abstract function getCatalogXmlInventoryById($id);
    
    abstract function getPackageXmlInventoryById($id);

    /**
     * Add tax if it exists in AbstractProduct. If no tax - delete it.
     * @param AbstractProduct $abstractProduct
     * @param String $reference
     */
    private function setTax($abstractProduct, $reference) {
        global $adb;
        $inventoryId = $this->trimReference($reference);
        if($abstractProduct->isNDS()) {
            $params = array($inventoryId, self::NDS_TAX_ID, $abstractProduct->getNDS());    
            $query = "insert into vtiger_producttaxrel values(?,?,?)";
            $adb->pquery($query,$params);
        } else {
            $params = array($inventoryId);
            $query = "delete from vtiger_producttaxrel where productid='?'";
            $adb->pquery($query,$params);
        }
    }
    
    /**
     * Create new product.
     * @param AbstractProduct $abstractProduct
     */
    private function createInventory($abstractProduct) {
        $reference = $this->create($this->module, $abstractProduct->toRestDescription());
        $this->setTax($abstractProduct, $reference);
        return $reference;
    }
    
    /**
     * Update exists product
     * @param AbstractProduct $abstractProduct
     * @param String $reference
     */
    private function updateInventory($abstractProduct, $reference) {
        $reference = $this->update($abstractProduct->toRestDescription(), $reference);
        $this->setTax($abstractProduct, $reference);
        return $reference;
    }
    
    /**
     * Return tax rate of the inventory. If no rate, return String "Без налога",
     * because 1C need this String as value.
     * @param int $inventoryId
     * @return String 
     */
    private function getInventoryTaxValue($inventoryId) {
        global $adb;
        $queryResult = $adb->pquery("select taxpercentage from vtiger_producttaxrel where productid=?;",
                array($inventoryId));
        $result = $adb->fetchByAssoc($queryResult);
        if($result == null) {
            $taxValue = "Без налога";
        } else {
            $taxValue = $result['taxpercentage'];
        }
        return $taxValue;
    }
    
    /**
     * Return prefix of currency module.
     * @return int 
     */
    private function getCurrencyPrefix() {
        $result = $this->describe('Currency');
        return $result['idPrefix'];
    }
    
    /**
     * Returns an currency code of the product. Because rest not return currency_id
     * @param int $id
     * @return String
     */
    private function getInventoryCurrencyReference($inventoryId) {
        global $adb;
        $params=array($inventoryId);
        $request = $adb->pquery("select currency_id from vtiger_products where productid=?;",$params);
        $result = $adb->fetchByAssoc($request);
        if($result['currency_id'] == null) {
            $request = $adb->pquery("select currency_id from vtiger_service where serviceid=?;",$params);
            $result = $adb->fetchByAssoc($request);
        } 
        
        $prefix = $this->getCurrencyPrefix();
        return $prefix.'x'.$result['currency_id'];
    }
    
    /**
     * Return inventory currecy code.
     * @param int $id
     * @return String
     */
    protected function getInventoryCurrencyCode($inventoryId) {
        $currencyReference = $this->getInventoryCurrencyReference($inventoryId);
        return $this->getCurrencyCode($currencyReference);
    }
    
    /**
     * Add to $xmlProduct price type, which contains currency. 
     * @param SimpleXmlElement $xmlProduct
     * @param array $inventoryRest
     * @param type $priceTypeId
     */
    protected function addXmlPrice($xmlProduct, $inventoryRest) {
        $prices = $xmlProduct->addChild("Цены");
        $price = $prices->addChild("Цена");
        
        /* In current version - id of price type is name currency code */
        $price->addChild("ИдТипаЦены", 1);
        $price->addChild("ЦенаЗаЕдиницу", $inventoryRest['unit_price']);
        $price->addChild("Валюта", $inventoryRest['currency_code']);
    }
    
    /**
     * Update or create product.
     * @param AbstractProduct $abstractProduct
     */
    public function save($abstractProduct) {
        $reference = $this->getReference($abstractProduct);
        if($reference != null) {
            return $this->updateInventory($abstractProduct, $reference);
        } else {
            return $this->createInventory($abstractProduct);
        }
    }
    
    /**
     * Return REST description of inventory neeed SalesOrder.
     * @param AbstractProduct $abstractProduct
     */
    public function getSalesOrderInventoryRest($abstractProduct) {
        $reference = $this->getReference($abstractProduct);
        if($reference == null) {
            $reference = $this->create($this->module, $abstractProduct->toRestDescription());
        }
        
        /* Inventory description, needed to save in salesOrder */
        $restData = array();
        $restData['productid'] = $this->trimReference($reference);
        $restData['quantity'] = $abstractProduct->getCount();
        $restData['listprice'] = $abstractProduct->getPrice();
        
        return $restData;
    }
    
    /**
     * Return inventory tax as SimpleXmlElement by it id.
     * @param int $inventoryId
     * @return SimpleXmlElement
     */
    public function getXmlInventoryTaxById($inventoryId) {
        $taxRates = new SimpleXMLElement('<СтавкиНалогов></СтавкиНалогов>');
        $rate = $taxRates->addChild('СтавкаНалога');
        $rate->addChild("Наименование", "НДС");
        $rate->addChild("Значение", $this->getInventoryTaxValue($inventoryId));
        
        return $taxRates;
    }
    
    /**
     * Adds usageUnit node 
     * @param SimpleXMLElement $xmlNode
     * @param string $usageUnit
     */
    protected function addUsageUnit($xmlNode, $usageUnit) {
        $xmlNode->addChild("БазоваяЕдиница");
        $usageUnitXml = $xmlNode->БазоваяЕдиница;

        $unitCode = UnitsConverter::convertFromCrmValueToCode($usageUnit);
        if($unitCode == null) {
            $unitCode = UnitsConverter::getDefaultUnitCode();
        }
        
        $usageUnitXml->addAttribute('Код', $unitCode);
        $usageUnitXml->addAttribute('НаименованиеПолное', vtranslate($usageUnit));
        $usageUnitXml->addChild('Пересчет');
        $recalculation = $usageUnitXml->Пересчет;
        $recalculation->addChild('Единица', $unitCode);
        $recalculation->addChild('Пересчет', 1);
    }
}
