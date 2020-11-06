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
 * Description of AccountController
 * Describes create/update xml upload operation on account.
 * @author alexd
 */
class AccountsController extends OperationController {
    
    /**
     * Add to account xml description identifier.
     * @param SimpleXmlElement $account
     * @param array $accountRest
     */
    private function addXmlAccountId($account, $accountRest) {
        $accountId = $accountRest['one_s_id'];
        if($accountId == null) {
            $accountId = $accountRest['accountname'];
        }
        $account->addChild("Ид", $accountId);
    }
    
    /**
     * Return reference of account by $cmlAccount object.
     * @param CmlAccount $cmlAccount
     */
    private function getReference($cmlAccount) {
        $name = $cmlAccount->getName();
        $result = $this->query("select id from Accounts where accountname='$name';");
        return $this->getFirstReference($result);
    }
    
    /**
     * Return REST fields, needed only to SalesOrder
     * @param array $restData
     */
    private function trimAccountRest($restData) {
        $trimmedRest['account_id'] = $restData['id'];
        $trimmedRest['bill_street'] = $restData['bill_street'];
        $trimmedRest['ship_street'] = $restData['ship_street'];
        
        return $trimmedRest;
    }
    
    /**
     * Retrur REST data of account neede to SalesOrder
     * @param CmlAccount $cmlAccount
     */
    public function getSalesOrderAccountRest($cmlAccount) {
        $reference = $this->getReference($cmlAccount);
        if($reference == null) {
            $reference = $this->create('Accounts', $cmlAccount->toRestDescription());
        }
        
        $restData = $this->retrieve($reference);
        return $this->trimAccountRest($restData);
    }
    
    /**
     * Return account description as SimpleXmlElement without shipping and billing addresses.
     * @param String $reference
     * return SimpleXmlElement
     */
    public function getXmlBaseAccount($reference) {
        $accountRest = $this->retrieve($reference);
        
        $accountXml = new SimpleXmlElement("<Контрагент></Контрагент>");
        $this->addXmlAccountId($accountXml, $accountRest);                    
        $accountXml->addChild("Наименование",$accountRest['accountname']);
        $accountXml->addChild("ПолноеНаименование",$accountRest['accountname']);      
        $accountXml->addChild("Роль","Покупатель");
        $accountXml->addChild("ИНН", $accountRest['inn']);
        $accountXml->addChild("КПП", $accountRest['kpp']);
        
        $address = $accountXml->addChild("АдресРегистрации");
        $address->addChild("Представление", $accountRest['bill_street']);
        
        return $accountXml;
    }
}
