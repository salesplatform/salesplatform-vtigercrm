<?php

/**
 * Parser for protocol version 2.00-2.06
 */
class CMLParser200 extends AbstractCMLParser {
    
    public function parseImport($importXml) {
        $this->checkMandatoryChildElement($importXml, "Каталог", "Not catalog in import.xml!");
        return $this->initilizateCatalog($importXml->Каталог);
    }

    public function parseOffer($offerXml) {
        $this->checkMandatoryChildElement($offerXml, "ПакетПредложений", "Not package in offers.xml!");
        return $this->initilizatePackage($offerXml->ПакетПредложений);
    }

    public function parseOrders($ordersXml) {
        $cmlSalesOrders = array();
        foreach($ordersXml->Документ as $xmlOrder) {
            $number = $this->getMandatoryChildContent($xmlOrder, 'Номер', 'Not document number in orders.xml');
            $oneEsidentifier = $this->getMandatoryChildContent($xmlOrder, 'Ид', 'Not document id in orders.xml! Document number - ' . $number);
            $currency = $this->getMandatoryChildContent($xmlOrder, 'Валюта', 'Not document currency in orders.xml! Document number - ' . $number);
            
            $salesOrder = new CmlSalesOrder($number, $oneEsidentifier, $currency);
            $salesOrder = $this->getDocumentAccount($salesOrder,$xmlOrder);
            $salesOrder = $this->initilizateOrderInventories($salesOrder, $xmlOrder);
            $salesOrder = $this->initilizateOrderProps($salesOrder, $xmlOrder);
            
            $cmlSalesOrders[] = $salesOrder;
        }
        
        return $cmlSalesOrders;
    }
    
    /**
     * Initilizate new CmlCatalog by getted $catalog.
     * @param SimpleXMLElement $catalog
     * @return CmlCatalog
     */
    protected function initilizateCatalog($catalog) {
        
        /* Get catalog mandatory fields */
        $catalogName = $this->getMandatoryChildContent($catalog, 'Наименование', 'Not catalog name in import.xml');
        $catalogName = $this->filter($catalogName);
        $oneEsIdentifier =  $this->getMandatoryChildContent($catalog, 'Ид', 'Not catalog identificator in import.xml');
        $cmlCatalog = new CmlCatalog($catalogName, $oneEsIdentifier);
        
        $this->checkMandatoryChildElement($catalog, 'Товары', 'Not products in import.xml');
        
        $cmlCatalog = $this->initilizateCatalogInventories($cmlCatalog, $catalog->Товары);
        
        return $cmlCatalog;
    }
    
    /**
     * Initilizate catalog products and services and return CmlCatalog.
     * @param CmlCatalog $cmlCatalog
     * @param SimpleXMLElement $catalogInventories
     * return CmlCatalog
     */
    protected function initilizateCatalogInventories($cmlCatalog, $catalogInventories) {
        foreach($catalogInventories->Товар as $importInventory) {
            if($this->isProduct($importInventory)) {
                $inventory = new CmlProduct();
                $inventory = $this->initilizateCatalogInventory($importInventory, $inventory);
                $cmlCatalog->addProduct($inventory);
            } else {
                $inventory = new CmlService();
                $inventory = $this->initilizateCatalogInventory($importInventory, $inventory);
                $cmlCatalog->addService($inventory);
            }
        }
        
        return $cmlCatalog;
    }
    
    /**
     * Return AbstractProduct by $importProduct
     * @param SimpleXMLElement $xmlInventoryDescription
     * @param CmlProduct|CmlService $inventory
     * @return AbstractProduct
     */
    protected function initilizateCatalogInventory($xmlInventoryDescription, $inventory) {
        $name = $this->getMandatoryChildContent($xmlInventoryDescription, 'Наименование',
                'Not product name in import.xml');
        $oneEsIdentifier = $this->getMandatoryChildContent($xmlInventoryDescription, 'Ид',
                'Not product identificator in import.xml');
        
        $unitName = '';
        if($this->isChildExists($xmlInventoryDescription, 'БазоваяЕдиница')) {
            $unitName = $this->getBasicUnitFromXml($xmlInventoryDescription->БазоваяЕдиница);
        }
        
        $article = $this->getChildContent($xmlInventoryDescription, 'Артикул');
        $NDS = $this->getTaxRate($xmlInventoryDescription);
        
        /* REST API broke on special symbols */
        $name = $this->filter($name);
        $article = $this->filter($article);
        
        $inventory->catalogInitilizate($oneEsIdentifier, $name, $article, $unitName, $NDS);
        
        return $inventory;
    }
    
    /**
     * Return tax rate. If no tax - return 0.
     * @param SimpleXMLElement $importProduct
     * @return int
     */
    protected function getTaxRate($importProduct) {
        if($this->isChildExists($importProduct, 'СтавкиНалогов')) {
            $rates = $importProduct->СтавкиНалогов;
            
            /* Search rate named NDS  */
            foreach($rates->СтавкаНалога as $rate) {
                $rateName = $this->getChildContent($rate, 'Наименование');
                $rateValue = $this->getChildContent($rate, 'Ставка');
                
                /* If tax rate not NULL */
                if($rateName == 'НДС' && $rateValue != 'Без налога' && $rateValue != NULL) {
                    return $rateValue;
                }
            }
        }
        return 0;
    }
    
    /**
     * Initilizate prices, count and currencies of all inventories.
     * @param SimpleXMLElement $package
     * @return \CmlCatalog
     */
    protected function initilizatePackage($package) {
        $oneEsIdentifier = $this->getMandatoryChildContent($package, 'Ид', 'Not id in offers!');
        $name = $this->getMandatoryChildContent($package, 'Наименование', 'Not name in offers!');
        $name = $this->filter($name);
        $cmlCatalog = new CmlCatalog($name, $oneEsIdentifier);
        
        /* Get package currency */
        $packageCurrency = $this->getPackageCurrency($package);
        $cmlCatalog->setCurrency($packageCurrency);
        
        /* Initilizate products and services */
        $this->checkMandatoryChildElement($package, "Предложения", "Not offers in offers.xml!");
        $cmlCatalog = $this->initilizatePackageInventories($package->Предложения, $cmlCatalog);
        
        return $cmlCatalog;
    } 
    
    /**
     * Return currency from first price type.
     * @param SimpleXMLElement $package
     * @return String
     */
    protected function getPackageCurrency($package) {
        $this->checkMandatoryChildElement($package, "ТипыЦен", "Not price type in offers.xml!");
        $priceTypes = $package->ТипыЦен;
        
        /* Get first price type */
        $this->checkMandatoryChildElement($priceTypes, "ТипЦены", "Not price type in offers.xml!");
        $priceType = $priceTypes->ТипЦены;
        
        $currency = $this->getMandatoryChildContent($priceType, "Валюта", "Not price type in offers.xml!");
        return $currency;
    }
    
    /**
     * Return CmlCatalog with only products - because in package no data about
     * inventory type.
     * @param SimpleXMLElement $package
     * @param CmlCatalog $cmlCatalog
     * @return \CmlCatalog
     */
    protected function initilizatePackageInventories($packageOffers, $cmlCatalog) {
        foreach($packageOffers->Предложение as $offer) {   
            $inventory = $this->initilizatePackageInventory($offer);
            if($inventory->getCurrency() == NULL) {
                $inventory->setCurrency($cmlCatalog->getCurrency());
            }
            $cmlCatalog->addProduct($inventory);
        }     
        
        return $cmlCatalog;
    }
    
    /**
     * Initilizate inventory cost parameters by getted SimpleXMLElement.
     * @param SimpleXMLElement $offer
     * @return \CmlProduct
     */
    protected function initilizatePackageInventory($offer) {
        $priceXmlElement = $this->getMandatoryPriceElement($offer);
        
        $price = $this->getMandatoryChildContent($priceXmlElement, 'ЦенаЗаЕдиницу',
                'Not product price in offers.xml');
        $currency = $this->getChildContent($priceXmlElement, 'Валюта');
        $conversionRate = $this->getChildContent($priceXmlElement, 'Коэффициент');
        
        $name = $this->getMandatoryChildContent($offer, 'Наименование',
                'Not product name in offers.xml');
        $name = $this->filter($name);
        
        $oneEsIdentifier = $this->getMandatoryChildContent($offer, 'Ид',
                'Not product identificator in offers.xml');
        $count = $this->getChildContent($offer, 'Количество');
        
        $inventory = new CmlProduct();
        $inventory->offersInitilizate($oneEsIdentifier, $name, $price, $currency, $conversionRate, $count);
        
        return $inventory;
    }
    
    /**
     * Initilizate account from document. If no account - throw exception.
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXMLElement $order
     * @return CmlSalesOrder
     */
    protected function getDocumentAccount($salesOrder, $xmlOrder) {
        $xmlAccount = $xmlOrder;
        if($this->isChildExists($xmlOrder, 'Контрагенты')) {
            $xmlAccount = $xmlOrder->Контрагенты;
        }
        
        $this->checkMandatoryChildElement($xmlAccount, 'Контрагент', 'Not account in order! Order number - ' . $salesOrder->getNumber());
        $account = $xmlAccount->Контрагент;
                
        $oneEsIdentifier = $this->getMandatoryChildContent($account, 'Ид', 'Not account id in order! Order number - ' . $salesOrder->getNumber());
        
        /* Phisical or legal face */
        $accountName = $this->getChildContent($account, 'ПолноеНаименование');
        if($accountName == null) {
            $accountName = $this->getMandatoryChildContent($account, 'ОфициальноеНаименование', 'Not account name in order! Order number - ' . $salesOrder->getNumber());
        }
        $accountName = $this->filter($accountName);
        
        $inn = $this->getChildContent($account, 'ИНН');
        $kpp = $this->getChildContent($account, 'КПП');
        
        $cmlAccount = new CmlAccount($accountName, $oneEsIdentifier);
        $cmlAccount->initAccountTaxInfo($inn, $kpp);
        $this->parseAccountAddress($cmlAccount, $account);
        
        $salesOrder->addAccount($cmlAccount);
        return $salesOrder;
    }
    
    /**
     * Initlizate account address if it exists.
     * @param CmlAccount $cmlAccount
     * @param SimpleXmlElement $xmlAccount
     */
    protected function parseAccountAddress($cmlAccount, $xmlAccount) {
        //TODO: In next version diff phis and legal addreses
        
        $address = null;

        /* Phisical or legal face */
        if($this->isChildExists($xmlAccount, 'АдресРегистрации')) {
            $xmlAddress = $xmlAccount->АдресРегистрации;
        } elseif($this->isChildExists($xmlAccount, 'Адрес')) {
            $xmlAddress = $xmlAccount->Адрес;
        } else {
            return;
        }

        /* Parse addres fields */
        foreach($xmlAddress->АдресноеПоле as $xmlAddressPart) {

            if($this->isChildExists($xmlAddressPart, 'Значение')) {
                
                $address = $address.$this->getChildContent($xmlAddressPart, 'Значение')." ";
            } 
        }
        
        if($address != null) {
            $cmlAccount->initAccountAddress($address, $address);        //shipping and billing equals in this version
        }
    }
    
    /**
     * Parse all order products and services. If no one servicew or product - throw exception.
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXMLElement $order
     * @return CmlSalesOrder 
     */
    protected function initilizateOrderInventories($salesOrder, $order) {
        $this->checkMandatoryChildElement($order, 'Товары', 'Not products in order!' . $salesOrder->getNumber());
        $orderInventories = $order->Товары;
        
        $this->checkMandatoryChildElement($orderInventories, 'Товар', 'No one product or service in order. Order number - ' . $salesOrder->getNumber());
        
        foreach($orderInventories->Товар as $xmlInventory) {
            if($this->isProduct($xmlInventory)) {
                $inventory = new CmlProduct();
                $inventory = $this->initilizateOrderInventory($xmlInventory, $inventory, $salesOrder);
                $salesOrder->addProduct($inventory);
            } else {
                $inventory = new CmlService();
                $inventory = $this->initilizateOrderInventory($xmlInventory, $inventory, $salesOrder);
                $salesOrder->addService($inventory);
            } 
        }
        return $salesOrder;
    }
    
    /**
     * Initilizate AbstractProduct by order xml.
     * @param SimpleXmlElement $orderProduct
     * @param AbstractProduct $inventory 
     * @param CmlSalesOrder $salesOrder
     * @return AbstractProduct
     */
    protected function initilizateOrderInventory($orderProduct, $inventory, $salesOrder) {
        $name = $this->getMandatoryChildContent($orderProduct, 'Наименование', 'Not product name in order! Order number - ' . $salesOrder->getNumber());
        $oneEsIdentifier = $this->getMandatoryChildContent($orderProduct, 'Ид', 'Not product identificator in order. Order number - ' . $salesOrder->getNumber());
        $price = $this->getMandatoryChildContent($orderProduct, 'ЦенаЗаЕдиницу', 'Not product price in order. Order number - ' . $salesOrder->getNumber());
        $count = $this->getMandatoryChildContent($orderProduct, 'Количество', 'Not product count in order. Order number - ' . $salesOrder->getNumber());
        
        $unitName = '';
        if($this->isChildExists($orderProduct, 'БазоваяЕдиница')) {
            $unitName = $this->getBasicUnitFromXml($orderProduct->БазоваяЕдиница);
        }
        
        $article = $this->getChildContent($orderProduct, 'Артикул');
        $NDS = $this->getTaxRate($orderProduct);
        
        $name = $this->filter($name);
        $article = $this->filter($article);
        
        $inventory->orderInitilizate($oneEsIdentifier, $name, $article, $unitName, $price, $count, $NDS);
        
        return $inventory;
    }
    
    /**
     * Return basic unit value from xml description
     * @param SimpleXMLElement $unitXml
     */
    protected function getBasicUnitFromXml($unitXml) {
        $unitCode = (string) $unitXml['Код'];
        $unitValue = UnitsConverter::convertFrom1cToCrm($unitCode);
        if($unitValue == null) {
            $unitValue = (string) $unitXml['НаименованиеПолное'];
        }
        
        return $unitValue;
    }
    
    /**
     * Initilizate order props
     * @param CmlSalesOrder $salesOrder
     * @param SimpleXmlElement $xmlOrder
     * @return CmlSalesOrder
     */
    protected function initilizateOrderProps($salesOrder, $xmlOrder) {
        if($this->isChildExists($xmlOrder, 'ЗначенияРеквизитов')) {
            $xmlProps = $xmlOrder->ЗначенияРеквизитов;
            foreach($xmlProps->ЗначениеРеквизита as $prop) {
                $propName = $this->getChildContent($prop, 'Наименование');
                $propValue = $this->getChildContent($prop, 'Значение');
                $salesOrder->addProp($propName, $propValue);
            }
        }
        
        return $salesOrder;
    }
    
    /**
     * Return SimpleXmlElement describes price of product.
     * @param SimpleXmlElement $offer
     * @return SimpleXmlElement
     */
    protected function getMandatoryPriceElement($offer) {
        $this->checkMandatoryChildElement($offer, "Цены", "Not product price in offers!");
        $prices = $offer->Цены;
        $this->checkMandatoryChildElement($prices,"Цена","Not product price in offers!");

        return $prices->Цена; 
    }
}