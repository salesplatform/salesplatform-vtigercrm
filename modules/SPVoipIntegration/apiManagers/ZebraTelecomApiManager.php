<?php
namespace SPVoipIntegration\apiManagers;

use SPVoipIntegration\integration\AbstractCallApiManager;
use SPVoipIntegration\api\ZebraClient;

class ZebraTelecomApiManager extends AbstractCallApiManager {
    
    private $client = null;
    public function __construct() {
        $this->client = new ZebraClient();        
    }
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $phoneNumber = $currentUser->get('phone_crm_extension');        
        $result = $this->client->makeCall($phoneNumber, $number);
    }
    
    public function registerWebhooks() {
        global $site_URL;
        
        $currentWebhooks = $this->client->getWebhooks();
        foreach ($currentWebhooks as $webhook) {
            $this->client->deleteWebhook($webhook['id']);
        }        
        
        $webhooks = array('channel_create', 'channel_answer', 'channel_destroy');
        $params['data']['uri'] = $site_URL . 'modules/SPVoipIntegration/controllers/CallsController.php';
        $params['data']['http_verb'] = 'post';
        
        $params['data']['retries'] = 1;
        foreach($webhooks as $hookName) {
            $params['data']['name'] = $hookName;
            $params['data']['hook'] = $hookName;
            $result = $this->client->registerWebhook($params);
        }        
        
    }        
}