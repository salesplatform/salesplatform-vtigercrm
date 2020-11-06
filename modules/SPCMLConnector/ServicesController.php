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
 * Describes operations with services - upload services and save them by cml data.
 * @author alexd
 */
class ServicesController extends InventoryController {
    
    public function __construct($assignedUserName) {
        parent::__construct($assignedUserName, 'Services');
    }
    
    /**
     * Add to service xml description identifier by REST info
     * @param SimpleXmlElement $xmlProduct
     * @param array $serviceRest
     */
    private function addXmlIdentifier($xmlProduct, $serviceRest) {
        if($serviceRest['one_s_id'] != null ) {
            $xmlProduct->addChild("Ид", $serviceRest['one_s_id']); 
        } else {
            $xmlProduct->addChild("Ид", $serviceRest['servicename']); 
        }
    }
    
    /**
     * Add props to xml description, specified for service. Need to identificate service in
     * One Es.
     * @param SimpleXmlElement $xmlService
     */
    private function addXmlProps($xmlProduct) {
        $props = $xmlProduct->addChild("ЗначенияРеквизитов");
        $prop = $props->addChild("ЗначениеРеквизита");
        $prop->addChild("Наименование","ВидНоменклатуры");
        $prop->addChild("Значение","Услуга");
        $prop = $props->addChild("ЗначениеРеквизита");
        $prop->addChild("Наименование","ТипНоменклатуры");
        $prop->addChild("Значение","Услуга");
    }
    
    /**
     * Get reference from cmlService.
     * @param CmlService $abstractProduct
     */
    public function getReference($abstractProduct) {
        $name = $abstractProduct->getName();
        $result = $this->query("select id from Services where servicename='$name';");
        return $this->getFirstReference($result);
    }
    
    /**
     * Return SimpleXmlElement of service by it id or null if product not exists.
     * @param int $id
     * @return SimpleXmlElement | NULL
     */
    public function getCatalogXmlInventoryById($id) {
        $result = $this->query("select * from Services where id=x$id;");        //rest need prefix in request
        $serviceRest = $this->getFirstQueryResult($result);
        
        if($serviceRest != null) {
            $xmlService = new SimpleXMLElement("<Товар></Товар>");
            $xmlService->addChild("Наименование", $serviceRest['servicename']);
            $this->addUsageUnit($xmlService, $serviceRest['service_usageunit']);
            $xmlService->addChild("Единица", vtranslate($serviceRest['service_usageunit']));
            $this->addXmlIdentifier($xmlService, $serviceRest);
            $this->addXmlProps($xmlService);
        }
        
        return $xmlService;
    }
    
    /**
     * Return product xml element, describes its offer.
     * @param int $inventoryId
     * @return SimpleXMLElement | null
     */
    public function getPackageXmlInventoryById($inventoryId) {
        $result = $this->query("select * from Services where id=x$inventoryId;");    //rest need prefix in request
        $serviceRest = $this->getFirstQueryResult($result);
        
        if($serviceRest != null) {
            $serviceRest['currency_code'] = $this->getInventoryCurrencyCode($inventoryId);  //rest not return currency
            
            $xmlService = new SimpleXMLElement("<Предложение></Предложение>");
            $xmlService->addChild("Наименование", $serviceRest['servicename']);
            $xmlService->addChild("Количество", $serviceRest['qtyinstock']);
            $this->addXmlIdentifier($xmlService, $serviceRest);
            $this->addXmlPrice($xmlService, $serviceRest);
        }
        
        return $xmlService;
    }

}
