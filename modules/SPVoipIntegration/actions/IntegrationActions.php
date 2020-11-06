<?php
include_once 'modules/SPVoipIntegration/vendor/autoload.php';

use SPVoipIntegration\integration\AbstractCallManagerFactory;
class SPVoipIntegration_IntegrationActions_Action extends Vtiger_Action_Controller {    
    
    function __construct() {
		parent::__construct();
		$this->exposeMethod('startOutgoingCall');
        $this->exposeMethod('getOutgoingPermissions');
        $this->exposeMethod("checkClickToCall");
	}
    
    function checkPermission(Vtiger_Request $request) {
		return;
	}
    
    public function checkClickToCall(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setResult(array(
            'enabled' => Settings_SPVoipIntegration_Record_Model::isClickToCallEnabled()
        ));
        $response->emit();
    }
    
    public function startOutgoingCall(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $factory = AbstractCallManagerFactory::getDefaultFactory();
            $callApiManager = $factory->getCallApiManager();        
            $callApiManager->doOutgoingCall($request->get('number'));
            $response->setResult(array('success'=>true));
        } catch (Exception $ex) {
            $response->setError($ex->getMessage());
        }
        $response->emit();
        
    }
    
    public function getOutgoingPermissions(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = Users_Privileges_Model::isPermitted('PBXManager', 'MakeOutgoingCalls');

        $response->setResult(array('success' => true, 'permission' => $permission));
        $response->emit();
    }

    public function process(\Vtiger_Request $request) {
        $mode = $request->getMode();
        try {
            if(!empty($mode)) {
                $this->invokeExposedMethod($mode, $request);
                return;
            }
        } catch (Exception $ex) {
            $response = new Vtiger_Response();
            $response->setError(vtranslate($ex->getMessage(), $request->getModule()));
            $response->emit();
            return;
        }
        
    }

}