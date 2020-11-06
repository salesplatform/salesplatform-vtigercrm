<?php
include_once 'modules/SPVoipIntegration/vendor/autoload.php';

use SPVoipIntegration\integration\AbstractCallManagerFactory;

class Settings_SPVoipIntegration_RegisterWebhooks_Action extends Vtiger_SaveAjax_Action {

    public function process(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $provider = $request->get('provider');
            $factory = AbstractCallManagerFactory::getEventsFacory($provider);
            $apiManager = $factory->getCallApiManager();
            $apiManager->registerWebhooks();
            $response->setResult(true);
        } catch (Exception $ex) {
            $response->setError($ex->getMessage());
        }                        
        $response->emit();
    }        
}


