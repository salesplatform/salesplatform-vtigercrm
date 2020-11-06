<?php


abstract class AbstractCMLParser {
    
    
    /**
     * Parse  offer of catalog described in xml and return CmlCatalog
     * representation of parsed documents. If error on parse - throw Exception.
     * @param SimpleXMLElement $offerXml
     * @return CmlCatalog
     */
    public abstract function parseOffer($offerXml);
    
    /**
     * Parse  offer of catalog described in xml and return CmlCatalog
     * representation of parsed documents. If error on parse - throw Exception.
     * @param SimpleXMLElement $importXml
     * @return CmlCatalog
     */
    public abstract function parseImport($importXml);
    
    /**
     * Parse orders described in xml and return array of CmlSalesOrder
     * @param SimpleXMLElement $ordersXml
     * @return CmlSalesOrder[]
     */
    public abstract function parseOrders($ordersXml);
    
    
    /**
     * If $rootXmlElement not have child with name $childName or it empty - return false.
     * @param SimpleXmlElement $rootXmlElement
     * @param String $childName
     * @return boolean
     */
    protected function isChildExists($rootXmlElement, $childName) {
        $element = $rootXmlElement->$childName;

        if(!empty($element)) {     
            return true;
        }
        return false;
    }
    
    /**
     * Return child entity content. if no entity - return null.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @return null|String
     */
    protected function getChildContent($rootXmlElement, $childName) {
        if($this->isChildExists($rootXmlElement, $childName)) {         
                return strip_tags($rootXmlElement->$childName->asXML());
        }
        return null;
    }
    
    /**
     * Check by props of xmlElement product type of current product.
     * If no props or - product will be service.
     * @param SimpleXMLElement $product
     * @return boolean
     */
    protected function isProduct($product) {
        if($this->isChildExists($product,'ЗначенияРеквизитов')) {
            $props = $product->ЗначенияРеквизитов;
            foreach($props->ЗначениеРеквизита as $prop) {
               $propName = $this->getChildContent($prop, 'Наименование');
               $propValue = $this->getChildContent($prop, 'Значение');
               if($propName == 'ТипНоменклатуры' && $propValue == 'Товар' ) {
                   return true;
               }
            }
        }
        
        return false;
    }
    
    /**
     * Get first child contenct without tags. If no child or empty content - 
     * throw exception.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @param String $errorMessage
     * @return String
     * @throws ParseException
     */
    protected function getMandatoryChildContent($rootXmlElement, $childName, $errorMessage) {
        
        /*  If tag exists but empty - condition will be true*/
        if($this->isChildExists($rootXmlElement, $childName)) {         
            return strip_tags($rootXmlElement->$childName->asXML());
        }   
        
        /* If no field or it empty */
        throw new ParseException($errorMessage);
    }
    
    /**
     * Returns content of xml node
     * @param type $node
     * @return type
     */
    protected function getNodeContent($node) {
        return strip_tags($node->asXML());
    }
    
    protected function filter($value) {
        return str_replace("'", "", $value);
    }
    
    /*
     * Check field as mandatory. If field not exists or it content empty - throw exception.
     * @param SimpleXMLElement $rootXmlElement
     * @param String $childName
     * @param String $errorMessage
     * @throws ParseException
     */
    protected function checkMandatoryChildElement($rootXmlElement, $childName, $errorMessage) {

        /*  If tag exists but empty - condition will be true*/
        if($this->isChildExists($rootXmlElement, $childName)) {         
                return;
        }   
        
        /* If no field or it empty */
        throw new ParseException($errorMessage);
    }
}
