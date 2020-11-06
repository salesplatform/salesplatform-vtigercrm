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
require_once "modules/SPCMLConnector/AccountsController.php";

/**
 * Describes create/update and xml upload operations by CmlSalesOrder inventory.
 */
class SalesOrderController extends OperationController {
    private $productsController;
    private $servicesController;
    protected $accountController;       //need acces in childs - to redefine account in order
    
    /**
     * CmlSalesorder object which need to be saved.
     * @var CmlSalesOrder 
     */
    protected $cmlSalesOrder;
    
    public function __construct($assignedUserName) {
        parent::__construct($assignedUserName);
        $this->productsController = new ProductsController($assignedUserName);
        $this->servicesController = new ServicesController($assignedUserName);
        $this->accountController = new AccountsController($assignedUserName);
    }
    
    /**
     * Return reference of products module.
     * @return int
     */
    private function getProductsPrefix() {
        $result = $this->describe("Products");
        return $result['idPrefix'];
    }
    
    /**
     * Generate REST description of inventories by $cmlSalesOrder
     * @return array
     */
    private function generateInventoriesRest() {
        $inventoriesRest = array();
        foreach($this->cmlSalesOrder->getProducts() as $cmlProduct) {
            $productRest = $this->productsController->getSalesOrderInventoryRest($cmlProduct);
            $inventoriesRest['LineItems'][] = $productRest;
        }
        
        foreach($this->cmlSalesOrder->getServices() as $cmlService) {
            $serviceRest = $this->servicesController->getSalesOrderInventoryRest($cmlService);
            $inventoriesRest['LineItems'][] =  $serviceRest;
        }
        
        $inventoriesRest['productid'] = $this->getProductsPrefix();   //vtiger 6 need it param
        return $inventoriesRest;
    }
    
    /**
     * Generates REST description of account in order.
     * @return array
     */
    private function generateAccountRest() {
        $cmlAccount = $this->cmlSalesOrder->getAccount();
        return $this->accountController->getSalesOrderAccountRest($cmlAccount);
    }
    
    /**
     * Return REST description of currency and tax type (individual)
     * @return array
     */
    private function generateTaxRest() {
        $currencyCode = $this->cmlSalesOrder->getCurrency();
        $currencyReference = $this->getCurrencyReference($currencyCode);
        $restData = array();
        $restData['currency_id'] = $currencyReference;
        $restData['hdnTaxType'] = 'individual';
        
        return $restData;
    }
    
    /**
     * Add inventories REST to order
     * @param array $order
     * @param array $inventories
     * @return array
     */
    private function joinInventoriesToOrder($order, $inventories) {
        return array_merge($order, $inventories);
    }
    
    /**
     * Updates exists record from 1C information.
     * @param cmlSalesOrder $cmlSalesOrder
     * @param String $reference
     */
    private function updateOrder($reference) {
        $restData = $this->buildSalesOrderRest();
        $this->update($restData, $reference);
    }
    
    /**
     * Return reference on order specified to 1C.
     * @param cmlSalesorder $cmlSalesOrder
     */
    protected function getReference() {
        $number = $this->cmlSalesOrder->getNumber();
        $result = $this->query("select id from SalesOrder where salesorder_no = '$number';");
        return $this->getFirstReference($result);
    }
    
    /**
     * Return basic SalesOrder REST array, specified for controller type (here it 1C)
     * @return array 
     */
    protected function getBasicRest() {
        $restData['one_s_id'] = $this->cmlSalesOrder->getOneEsId();
        return $restData;
    }
    
    /**
     * Return REST description of sostatus and invoicestatus
     * @return array
     */
    protected function generateStatuses() {
        $restData = array();
        
        if($this->cmlSalesOrder->getPropValue("Номер оплаты по 1С") != null) {
            $restData['invoicestatus'] = 'Paid';
        } else {
            $restData['invoicestatus'] = 'AutoCreated';
        }
        
        /* One Es generate values as strings true or false */
        if($this->cmlSalesOrder->getPropValue("Проведен") === 'true') {
            $restData['sostatus'] = 'Delivered'; 
        } elseif($this->cmlSalesOrder->getPropValue("ПометкаУдаления") === 'true') {
            $restData['sostatus'] = 'Cancelled';
        } else {
            $restData['sostatus'] = 'Created';
        }
        
        return $restData;
    }
    
    /**
     * Return REST to save salesOrder by information from one es.
     * @param cmlSalesorder $cmlSalesOrder
     */
    protected function buildSalesOrderRest() {
        $orderBasic = $this->getBasicRest();
        $statuses = $this->generateStatuses();
        $account = $this->generateAccountRest();
        $taxes = $this->generateTaxRest();
        $orederRest = array_merge($statuses, $account, $taxes, $orderBasic);          
        
        /* Add to order REST inventories to save, and return REST description */
        $inventories = $this->generateInventoriesRest();
        return $this->joinInventoriesToOrder($orederRest, $inventories);
    }
    
    /**
     * Filter SalesOrders which will be uploaded.
     * @param String $beginTime
     * @return array
     */
    protected function getSalesOrdersToUpload($beginTime) {
        $salesOrdersResult = $this->query("select id from SalesOrder where modifiedtime > '$beginTime';");
        $salesOrders = array();
        foreach($salesOrdersResult as $resultLine) {
            $salesOrders[] = $this->retrieve($resultLine['id']);
        }
        return $salesOrders;
    }
    
    /**
     * Save SalesOrder by 1C information.
     * @param CmlSalesOrder $cmlSalesOrder
     */
    public function saveOrder($cmlSalesOrder) {
        $this->cmlSalesOrder = $cmlSalesOrder;
        $reference = $this->getReference();
        
        /* Only update exists SalesOrder from 1C - never create new */
        if($reference != null) {
            $this->updateOrder($reference);
        }
    }
    
    /**
     * Return xml description of all orders, which modified after $beginTime. 
     * @param String $beginTime - date after which SalesOrder search. Need Mysql DateTime format.
     */
    public function getXmlOrders($beginTime) {  
        if($beginTime == null) {
           $beginTime = date("Y-m-d H:i:s", 0);     //if no time - upload all salesOrders
        }
        
        $salesOrders = $this->getSalesOrdersToUpload($beginTime);
        $commerceData = $this->getCommerceHeader();
        foreach($salesOrders as $order) {
            $this->addSalesOrder($commerceData, $order);
        }
        return $commerceData->asXML();
    }
    
    /**
     * Add currency code to document by refernece.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     * @return String
     */
    private function addXmlCurrencyCode($document, $salesOrderRest) {
        $currencyReference = $salesOrderRest['currency_id'];
        $currencyCode = $this->getCurrencyCode($currencyReference);
        $document->addChild("Валюта", $currencyCode);
    }
    
    /**
     * Add to order account information. Billing and shipping addresses add from
     * salesOrder REST data - because in account can be empty fileds. 
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addXmlAccount($document, $salesOrderRest) {
        $accountReference = $salesOrderRest['account_id'];
        $account = $this->accountController->getXmlBaseAccount($accountReference);
        $accounts = $document->addChild("Контрагенты");

        $this->appendXmlElement($accounts, $account);
    }
    
    /**
     * 
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    private function addXmlOrderInventories($document, $salesOrderRest) {
        $inventoriesContainer = $document->addChild("Товары");
        foreach($salesOrderRest['LineItems'] as $inventory) {
            $this->addXmlInventory($inventoriesContainer, $inventory);
        }
    }
    
    /**
     * Add inventory to inventories tag container.
     * @param SimpleXmlElement $inventoriesContainer
     * @param array $inventory
     */
    private function addXmlInventory($inventoriesContainer, $inventory) {
        $inventoryId = $this->trimReference($inventory['productid']);
        $inventoryXml = $this->productsController->getCatalogXmlInventoryById($inventoryId);
        if($inventoryXml == null) {
            $inventoryXml = $this->servicesController->getCatalogXmlInventoryById($inventoryId);
        } 
        
        /* In CRM may be deleted inventories from CRM, but not deleted from order. */
        if($inventoryXml == null) {
            return;
        }
        
        /* Common fields of products and services on SalesOrder */
        $inventoryXml->addChild("ЦенаЗаЕдиницу", $inventory['listprice']);
        $inventoryXml->addChild("Количество", $inventory['quantity']);
        $inventoryXml->addChild("Сумма", $inventory['quantity'] * $inventory['listprice']);

        $this->appendXmlElement($inventoriesContainer, $inventoryXml);
    }
    
    /**
     * Add order identificator to order.
     * @param SimpleXmlElement $order
     * @param array $salesOrderRest
     */
    protected function addXmlOrderIdentificator($order, $salesOrderRest) {
        if($salesOrderRest['one_s_id']!=null){
            $order->addChild("Ид", $salesOrderRest['one_s_id']);
        } else {
            $order->addChild("Ид", $salesOrderRest['salesorder_no']);
        }
    }
    
    /**
     * Add props to document by information from REST description.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addOrderProps($document, $salesOrderRest) {
        $documentProps = $document->addChild("ЗначенияРеквизитов");
            
        $documentProp = $documentProps->addChild("ЗначениеРеквизита");
        $documentProp->addChild("Наименование", "НомерНаСайте");
        $documentProp->addChild("Значение", $salesOrderRest['salesorder_no']);

        $documentProp = $documentProps->addChild("ЗначениеРеквизита");
        $documentProp->addChild("Наименование", "ДатаНаСайте");
        $documentProp->addChild("Значение", strstr($salesOrderRest['createdtime'], " ", true));

        $documentProp = $documentProps->addChild("ЗначениеРеквизита");
        $documentProp->addChild("Наименование", "Статус заказа");
        $documentProp->addChild("Значение", $salesOrderRest['sostatus']);
    }
    
    /**
     * Add to documrnt it number, specified for order - from one es or from website.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addXmlOrderNumber($document, $salesOrderRest) {
        $document->addChild("Номер",$salesOrderRest['salesorder_no']);
    }
    
    /**
     * Add to xml order description base order information.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    private function addXmlOrderHeader($document, $salesOrderRest) {
        $this->addXmlOrderNumber($document, $salesOrderRest);
        $document->addChild("Дата", strstr($salesOrderRest['createdtime'], " ", true));
        $document->addChild("ХозОперация", "Заказ товара");
        $document->addChild("Роль", "Продавец");
        $document->addChild("Время", substr($salesOrderRest['createdtime'],
                strpos($salesOrderRest['createdtime'], " ") + 1) );
    }
    
    /**
     * Add to commerceData salesOrder.
     * @param SimpleXmlElement $commerceData
     * @param array $salesOrderRest
     */
    private function addSalesOrder($commerceData, $salesOrderRest) {
        $document = $commerceData->addChild("Документ");
        
        $this->addXmlOrderHeader($document, $salesOrderRest);
        $this->addXmlOrderIdentificator($document, $salesOrderRest);
        $this->addXmlCurrencyCode($document, $salesOrderRest);
        $this->addXmlAccount($document, $salesOrderRest);
        $this->addXmlOrderInventories($document, $salesOrderRest);
        $this->addOrderProps($document, $salesOrderRest);
    }
    
}
