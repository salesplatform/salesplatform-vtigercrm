<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once "include/Webservices/OperationManager.php";
require_once "include/Webservices/Query.php";
require_once "include/Webservices/Create.php";
require_once "include/Webservices/Update.php";
require_once "include/Webservices/Retrieve.php";
require_once "include/Webservices/Delete.php";
require_once "include/Webservices/DescribeObject.php";
require_once "modules/Users/Users.php";
require_once 'includes/main/WebUI.php';

/**
 * Controls load and upload operations on CRM enttities. Need to save, search and get information.
 * Need better think about class interfaces.
 * @author alexd
 */
class OperationController {
    private $assignedUserReference;
    private $restUser;
    
    /**
     * Initilizate commerce operation controller.
     * @param String $assignedUserName
     * @param String $userKey
     */
    public function __construct($assignedUserName) {
        $this->restUser = Users::getActiveAdminUser();
        $this->assignedUserReference = $this->getUserReference($assignedUserName);
    }
    
    /**
     * Return user reference by it name. If user not found - return NULL.
     * @param String $assignedUserName
     * @return String
     */
    private function getUserReference($assignedUserName) {
        $result = vtws_query("select id from Users where user_name='$assignedUserName';",$this->restUser);
        return $this->getFirstReference($result);
    }
    
     /**
     * Merge exist record rest information with new $restDescription.
     * @param String $reference
     * @param Array $restData
     * @return Array
     */
    private function mergeOnExist($reference, $restData) {
        $record = vtws_retrieve($reference, $this->restUser);
        foreach($restData as $key => $value) {
            $record[$key] = $value;
        }
        return $record;
    }
    
    /**
     * Return record id from vtws_query result. If No id - return NULL.
     * @param type $queryResult
     */
    protected function getFirstReference($queryResult) {
        $reference = current($queryResult);
        if($reference !== FALSE) {
            return $reference['id'];
        }
        return NULL;
    }
     
    /**
     * Return currency reference by it code. If no currency - return NULL.
     * @param String $currencyCode
     * @return String
     */
    protected function getCurrencyReference($currencyCode) {
        if($currencyCode == null) {
            throw new Exception('Empty currency code!');
        }
        
        $result = vtws_query("select id from Currency where currency_code='$currencyCode' OR currency_symbol='$currencyCode';", $this->restUser);
        $currencyReference = $this->getFirstReference($result);
        if($currencyReference == null) {
            throw new Exception('Not currency in CRM with code = '.$currencyCode);
        }
        return $currencyReference;
    }
    
    /**
     * Return currency code by it reference.
     * @param String $reference
     * @return String
     */
    protected function getCurrencyCode($reference) {
        $result = vtws_retrieve($reference, $this->restUser);
        return $result['currency_code'];
    }

    /**
     * Return an id without reference part
     * @param String $id
     * @return id
     */
    protected function trimReference($id) {
        if(strpos($id, "x") === false) {
            return $id;
        }
        return substr($id, strpos($id, "x")+1);
    }
    
    /**
     * Appends to element $to SimpleXmlElement $from
     * @param SimpleXMLElement $to - get element by link.
     * @param SimpleXMLElement $from
     */
    protected function appendXmlElement(SimpleXMLElement $to, SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
    
    /**
     * Return header as SimpleXmlElement for each commerce document.
     * @return \SimpleXMLElement
     */
    protected function getCommerceHeader() {
        $timechange = time();
        $commHeader = '<?xml version="1.0" encoding="UTF-8"?>
                            <КоммерческаяИнформация ВерсияСхемы="2.04" 
                            ДатаФормирования="'.date( 'Y-m-d', $timechange) .'T'.date( 'H:m:s',$timechange).'">
                            </КоммерческаяИнформация>';
        
        return  new SimpleXMLElement($commHeader);
    } 
    
    /**
     * Return first query result or nul if empty result.
     * @param type $queryResult
     * @return null
     */
    protected function getFirstQueryResult($queryResult) {
        $firstResult = current($queryResult);
        if($firstResult !== FALSE) {
            return $firstResult;
        }
        return null;
    }
    
    /**
     * Create new record in module $moduleName by params $restData.
     * Return created record reference.
     * @param String $moduleName
     * @param Array $restData
     * @return String
     */
    public function create($moduleName, $restData) {
        $restData['assigned_user_id'] = $this->assignedUserReference;
        $result = vtws_create($moduleName, $restData, $this->restUser);
        return $result['id'];
    }
    
    /**
     * Updates data. Based on rest. Return reference of updated record.
     * @return String
     */
    public function update($restData, $reference) {
        $restData = $this->mergeOnExist($reference, $restData);
        $restData['assigned_user_id'] = $this->assignedUserReference;
        $restData['id'] = $reference;

        $result = vtws_update($restData, $this->restUser);
        
        return $result['id'];
    }
    
    /**
     * vtws_query wrap.
     * @param type $query
     * @return type
     */
    public function query($query) {
        return vtws_query($query, $this->restUser);
    }
    
    /**
     * Retrieve record.
     * @param String $reference
     * @return array
     */
    public function retrieve($reference) {
        return vtws_retrieve($reference, $this->restUser);
    }
    
    public function describe($module) {
        return vtws_describe($module, $this->restUser);
    }
    
    
}
