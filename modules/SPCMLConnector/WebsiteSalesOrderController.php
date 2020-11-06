<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'includes/runtime/LanguageHandler.php';
require_once 'modules/SPCMLConnector/UnitsConverter.php';
require_once "modules/SPCMLConnector/SalesOrderController.php";
require_once "modules/SPCMLConnector/SiteExchangeSettings.php";

/**
 * Describes create/update and upload operation with orders, getted from website.
 * @author alexd
 */
class WebsiteSalesOrderController extends SalesOrderController {
    
    /**
     * Number,assigned order on site. Identificate order.
     * @return array
     */
    protected function getBasicRest() {
        $restData['subject'] = $this->cmlSalesOrder->getNumber();
        $restData['fromsite'] = $this->cmlSalesOrder->getNumber();
        return $restData;
    }
    
    /**
     * Site identifier are different of one es identifier.
     * @param type $order
     * @param type $salesOrderRest
     */
    protected function addXmlOrderIdentificator($order, $salesOrderRest) {
        $order->addChild("Ид", $salesOrderRest['fromsite']);
    }
    
    /**
     * Add to documrnt it number, specified for order - from one es or from website.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addXmlOrderNumber($document, $salesOrderRest) {
        $document->addChild("Номер", $salesOrderRest['fromsite']);
    }
    
    protected function getReference() {
        $number = $this->cmlSalesOrder->getNumber();
        $result = $this->query("select id from SalesOrder where fromsite = '$number';");
        
        return $this->getFirstReference($result);
    }
    
    /**
     * On CommerceML standart site and One Es different uploads in xml order account
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addXmlAccount($document, $salesOrderRest) {
        $accountReference = $salesOrderRest['account_id'];
        $account = $this->accountController->getXmlBaseAccount($accountReference);
        $this->appendXmlElement($document, $account);
    }
    
    /**
     * Add props to order, specified for website.
     * @param SimpleXmlElement $document
     * @param array $salesOrderRest
     */
    protected function addOrderProps($document, $salesOrderRest) {
        $documentProps = $document->addChild("ЗначенияРеквизитов");
            
        $documentProp = $documentProps->addChild("ЗначениеРеквизита");
        $documentProp->addChild("Наименование", "Номер по 1С");
        $documentProp->addChild("Значение", $salesOrderRest['fromsite']);

        if($salesOrderRest['sostatus'] == "Created") {
            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Проведен");
            $documentProp->addChild("Значение", "false");
        }
        if($salesOrderRest['sostatus'] == "Approved") {
            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Проведен");
            $documentProp->addChild("Значение", "true");
        }
        if($salesOrderRest['sostatus'] == "Delivered") {
            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Проведен");
            $documentProp->addChild("Значение", "true");

            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Номер оплаты по 1С");
            $documentProp->addChild("Значение", $salesOrderRest['salesorder_no']);
        }
        if($salesOrderRest['sostatus'] == "Cancelled") {
            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Отменен");
            $documentProp->addChild("Значение", "true");
            
            $documentProp = $documentProps->addChild("ЗначениеРеквизита");
            $documentProp->addChild("Наименование", "Проведен");
            $documentProp->addChild("Значение", "false");
        }
    }
    
    /**
     * Return invoicestatus and sostatus of the SalesOrder.
     * @return array
     */
    protected function generateStatuses() {
        /* Default values */
        $statuses = array();
        $statuses['invoicestatus'] = 'AutoCreated'; 
        $statuses['sostatus'] = 'Created';
        
        $websiteSettings = new SiteExchangeSettings();
        $websiteOrderStatus = $this->cmlSalesOrder->getPropValue("Статус заказа");
        $sostatus = $websiteSettings->getCrmStatusBySite($websiteOrderStatus);
        
        if($sostatus != null) {
            $statuses['sostatus'] = $sostatus;
        }
        
        return $statuses;
    }
    
    /**
     * Filter SalesOrders which will be uploaded.
     * @param String $beginTime
     * @return array
     */
    protected function getSalesOrdersToUpload($beginTime) {
        $salesOrdersResult = $this->query("select id from SalesOrder where modifiedtime < '$beginTime' and fromsite!='0';");
        $salesOrders = array();
        foreach($salesOrdersResult as $resultLine) {
            $salesOrders[] = $this->retrieve($resultLine['id']);
        }
        return $salesOrders;
    }
    
    /**
     * Create new or update exists SalesOrder, getted in xml from website.
     * @param CmlSalesOrder $cmlSalesOrder
     */
    public function saveOrder($cmlSalesOrder) {
        $this->cmlSalesOrder = $cmlSalesOrder;
        if($this->getReference() != null ) {
            parent::saveOrder($cmlSalesOrder);
        } else {
            $restData = $this->buildSalesOrderRest();
            $this->create('SalesOrder', $restData);
        }
    }
}
