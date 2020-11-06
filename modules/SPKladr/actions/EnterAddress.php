<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPKladr/models/KladrService.php';

/**
 * Handles requests for address help typing actions.
 */
class SPKladr_EnterAddress_Action extends Vtiger_Action_Controller {
    
    private $kladrService;
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('indexTyped');
        $this->exposeMethod('stateTyped');
        $this->exposeMethod('checkEnable');
        $this->exposeMethod('fullAddressTyped');
        
        $this->kladrService = new KladrService();
    }
    
    /**
     * All users can get help on typing address.
     * @param Vtiger_Request $request
     * @return type
     */
    public function checkPermission(Vtiger_Request $request) {
        return;
    }
    
    public function process(\Vtiger_Request $request) {
        $this->invokeExposedMethod($request->getMode(), $request);
    }
    
    public function stateTyped(\Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $searchParams = array(
            "stateName" => trim($request->get('stateName'))
        );
        
        $response->setResult($this->kladrService->searchStateByName($searchParams));
        $response->emit();
    }
    
    public function fullAddressTyped(\Vtiger_Request $request) {
        $response = new Vtiger_Response();

        /* Prepare list of search params and delegate search */
        $searchParams = array(
            "requestStep" => $request->get("requestStep"),
            "cityRecordsLimit" => (int) $request->get("cityRecordsLimit"),
            "cityOffset" => (int) $request->get("cityOffset"),
            "cityName" => trim($request->get("cityName")),
            "cityCode" => trim($request->get("cityCode")),
            "streetName" => trim($request->get("streetName")),
            "streetCode" => trim($request->get("streetCode")),
            "houseNumber" => trim($request->get("houseNumber"))
        );
        
        $response->setResult($this->kladrService->searchFullAddress($searchParams));
        $response->emit();
    }
    
    public function checkEnable(\Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getCleanInstance($request->getModule());
        $response = new Vtiger_Response();
        $response->setResult($moduleModel->isActive());
        $response->emit();
    }
}