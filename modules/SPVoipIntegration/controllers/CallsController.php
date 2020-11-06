<?php

namespace SPVoipIntegration;

register_shutdown_function(function() {
    $error = error_get_last();
    error_log(print_r($error, true));
});
if (isset($_GET['zd_echo']))
    exit($_GET['zd_echo']);
$headers = getallheaders();
if (isset($headers['Echo'])) {
    header("Echo: {$headers['Echo']}");
    exit();
}
chdir(dirname(__FILE__) . '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'libraries/htmlpurifier/library/HTMLPurifier.auto.php';
vimport('includes.http.Request');
include_once 'modules/SPVoipIntegration/vendor/autoload.php';

global $current_user;

use SPVoipIntegration\ProvidersEnum;
use SPVoipIntegration\integration\AbstractCallManagerFactory;
use SPVoipIntegration\loggers\Logger;

class CallsController {

    public function process(\Vtiger_Request $request) {
        try {
            $voipManagerName = $this->parseRequest($request);
            $factory = AbstractCallManagerFactory::getEventsFacory($voipManagerName);
            $notificationModel = $factory->getNotificationModel($request->getAll());
            $notificationModel->validateNotification();
            $notificationModel->process();
        } catch (\Exception $ex) {
            Logger::log('Error on process notification', $ex);
        }
    }

    private function parseRequest(\Vtiger_Request $request) {
        if ($request->get('provider') == ProvidersEnum::ZADARMA) {
            return ProvidersEnum::ZADARMA;
        }
        
        if (strpos($_SERVER['REQUEST_URI'], "sipuni") !== FALSE) {
            return ProvidersEnum::SIPUNI;
        }

        // check for rostelecom
        $clientIdHeader = $_SERVER['HTTP_X_CLIENT_ID'];
        $clientSignHeader = $_SERVER['HTTP_X_CLIENT_SIGN'];
        if (!empty($clientIdHeader) && !empty($clientSignHeader)) {
            $validRequest = \Settings_SPVoipIntegration_Record_Model::checkRostelecomSignRequest($clientIdHeader, $clientSignHeader);
            if ($validRequest) {
                return ProvidersEnum::ROSTELECOM;
            }
        }

        $token = $request->get('crm_token');
        if (!empty($token) && $token === \Settings_SPVoipIntegration_Record_Model::getGravitelCrmToken()) {
            return ProvidersEnum::GRAVITEL;
        }
        
        $mangoKey = $request->get('vpbx_api_key');
        if(!empty($mangoKey) && $mangoKey === \Settings_SPVoipIntegration_Record_Model::getMangoKey()) {
            return ProvidersEnum::MANGO;
        }
        
        if (!empty($token) && $token === \Settings_SPVoipIntegration_Record_Model::getMegafonCrmToken()) {
            return ProvidersEnum::MEGAFON;
        }

        if (!empty($token) && $token === \Settings_SPVoipIntegration_Record_Model::getDomruCrmToken()) {
            return ProvidersEnum::DOMRU;
        }

        if (!empty($token) && $token === \Settings_SPVoipIntegration_Record_Model::getWestCallSPBCrmToken()) {
            return ProvidersEnum::WESTCALL_SPB;
        }

        if ($request->get('provider') == ProvidersEnum::TELPHIN) {
            return ProvidersEnum::TELPHIN;
        }

        if ($request->get('provider') == ProvidersEnum::YANDEX) {
            return ProvidersEnum::YANDEX;
        }

        if ($request->get('provider') == ProvidersEnum::UISCOM) {
            return ProvidersEnum::UISCOM;
        }

        if ($request->get('provider') == ProvidersEnum::MCN) {
            return ProvidersEnum::MCN;
        }

        throw new \Exception("Unknown request sender");
    }

}

$current_user = \Users::getActiveAdminUser();

$callController = new CallsController();
$callController->process(new \Vtiger_Request($_REQUEST));
