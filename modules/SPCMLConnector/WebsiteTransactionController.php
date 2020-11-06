<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPCMLConnector/SiteExchangeSettings.php';
require_once 'modules/SPCMLConnector/CatalogController.php';
require_once 'modules/SPCMLConnector/UploadFileManager.php';
require_once 'modules/SPCMLConnector/TranzactionHistory.php';
require_once 'includes/runtime/LanguageHandler.php';
require_once 'modules/SPCMLConnector/WebsiteSalesOrderController.php';
require_once 'modules/SPCMLConnector/UnitsConverter.php';
require_once 'modules/SPCMLConnector/CmlParser.php';

/**
 * Describes control operations to upload Catalogs to website and exchange
 * SalesOrders
 * @author alexd
 */
class WebsiteTransactionController {
   
    private $cookies;
    private $fileManager;
    private $settings;
    private $exchangeType;
    private $history;
    
    public function __construct() {
        $this->cookies = array();
        $this->fileManager = new UploadFileManager();
        $this->settings = new SiteExchangeSettings();
        $this->history = new TranzactionHistory();
    }
    
    /**
     * Create request object.
     * @param array $params
     * @return Object
     */
    private function getCurlRequestObject($params) {
        $user = $this->settings->getAdminLogin();
        $password = $this->settings->getAdminPassword();
        $curlRequest = curl_init();
        
        curl_setopt($curlRequest, CURLOPT_URL, $this->createURI($this->settings->getSiteUrl(), $params));
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curlRequest, CURLOPT_USERPWD, "$user:$password"); 
        
        foreach($this->cookies as $cookieName => $cookieValue) {
            curl_setopt($curlRequest, CURLOPT_COOKIE, "$cookieName=$cookieValue");
        }
        
        return $curlRequest;
    }
    
    /**
     * Send GET request and return it answer.
     * @param array $params
     * @return String
     */
    private function sendGet($params) {
        $request = $this->getCurlRequestObject($params);
        $result = curl_exec($request);
        
        curl_close($request);
        
        return $result;
    }
    
    /**
     * Send POST request and return answer.
     * @param Array $params
     * @param String $postContent
     * @return String
     */
    private function sendPost($params, $postContent) {
        $request = $this->getCurlRequestObject($params);
        
        curl_setopt($request, CURLOPT_POST, 1); 
        curl_setopt($request, CURLOPT_POSTFIELDS, $postContent);

        $result = curl_exec($request);
        curl_close($request);
        
        return $result;
    }
    
    /**
     * Return URI addres
     * @param String $url
     * @param array $params
     * @return String
     */
    private function createURI($url,$params) {
        $path = http_build_query($params);
        $url = $url."?".$path;
        return $url;
    }
    
    /**
     * Initilizate Cookies from site answer. Throw Exception, if cannot connect!
     * @throws Exception
     */
    private function authStep() {
        $params = array('type' => $this->exchangeType, 'mode' => 'checkauth');
        $response = $this->sendGet($params);
        $answerParts = explode("\n", $response);
        
        if($answerParts[0] == 'success') {
            $this->cookies = array($answerParts[1] => $answerParts[2]);
            return;
        }
        
        throw new Exception('Cannot connect to website! Check settings and network connection!');
    }
    
    /**
     * Request max part of zip file to exchange. If no zip support - throw exception.
     * @throws Exception
     */
    private function initStep() {
        $params = array('type' => $this->exchangeType, 'mode' => 'init');
        $response = $this->sendGet($params);
        
        if(strstr($response, 'zip=yes')) { 
            return;
        }
        
        throw new Exception('Support only zip excahge!');
    }
    
    /**
     * Import step. Send names of files, which need to import.
     * @throws Exception
     */
    private function catalogsImportStep() {
        foreach ($this->fileManager->getArchiveFileNames() as $importFileName) {
            
            /* Web site import by parts */
            do {
                $params = array('type' => 'catalog','mode' => 'import', 'filename' => $importFileName);
                $answer = $this->sendGet($params);
                if(strstr($answer, 'failure')) {
                    throw new Exception($answer);
                }
            } while(strstr($answer, 'progress'));
        }
    }
    
    /**
     * Send qeury to website to get SalesOrders and import them.
     */
    private function salesOrderQueryStep() {
        $params = array('type' => $this->exchangeType, 'mode' => 'query');
        $response = $this->sendGet($params);
        $parser = new CmlParser();
        $cmlSalesorders = $parser->parseOrders($response);
        
        $this->history->fixSuccessTranzaction('SalesOrder', 'from_site');
        $salesOrderController = new WebsiteSalesOrderController($this->settings->getAssignedUser());
        foreach($cmlSalesorders as $order) {
            $salesOrderController->saveOrder($order);
        }
        
        /* Send answer what import was success */
        $params = array('type' => $this->exchangeType, 'mode' => 'success');
        $this->sendGet($params);
    }
    
    /**
     * Send SalesOrders information to website.
     */
    private function salesOrderFileStep() {
        $salesOrderController = new WebsiteSalesOrderController($this->settings->getAssignedUser());
        $ordersContent = $salesOrderController->getXmlOrders($this->history->getLastSalesSiteTranzaction());
        $this->fileManager->setZipOrdersContent($ordersContent);
        $this->fileTransmittion();
    }
    
    /**
     * Send zipped file content to website.
     * @throws Exception
     */
    private function fileTransmittion() {
        $fileContent = $this->fileManager->getZipContent();
        
        $params = array('type' => $this->exchangeType, 'mode' => 'file', 'filename' => $this->fileManager->getZipFileName());
        $response = $this->sendPost($params, $fileContent);
        if( !strstr($response, 'success') ) {
            throw new Exception($response);
        }
    }
    
    /**
     * Send information of the catalogs to site in zip archive. Control site answer.
     * If fail on send - throw exception.
     */
    private function catalogsFileStep() {
        $catalogController = new CatalogController($this->settings->getAssignedUser());
        $importsContent = $catalogController->getXmlCatalogs();
        $offersContent = $catalogController->getXmlOffers();
        
        $this->fileManager->setZipCatalogsContent($importsContent, $offersContent);
        $this->fileTransmittion();
    }
    
    /**
     * Start upload catalogs from vtiger by CommerceMl stadart.
     */
    public function startCatalogsUpload() {
        $this->exchangeType = 'catalog';
        try {
            $this->authStep();
            $this->initStep();
            $this->catalogsFileStep();
            $this->catalogsImportStep();
            $this->history->fixSuccessTranzaction("Products", "to_site");
        } catch (Exception $ex) {
            $this->history->fixTranzactionError("Products", "to_site", $ex->getMessage());
        }
    }
    
    /**
     * Start exchange SalesOrders with site.
     */
    public function startSalesOrderExchange() {
        $this->exchangeType = 'sale';
        try {
            $this->authStep();
            $this->initStep();
            $this->salesOrderQueryStep();
            $this->salesOrderFileStep();
            $this->history->fixSuccessTranzaction("SalesOrder", "to_site");
        } catch (Exception $ex) {
            $this->history->fixTranzactionError("SalesOrder", "to_site", $ex->getMessage());
        }
    }
}
