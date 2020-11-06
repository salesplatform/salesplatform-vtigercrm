<?php
namespace SPVoipIntegration\megafon\notifications;

use SPVoipIntegration\ProvidersEnum;

trait GravitelAdapterTrait {    
    
    public function getCrmSavedToken() {
        return \Settings_SPVoipIntegration_Record_Model::getMegafonCrmToken();
    }

    public function getSourceUUId() {
        return 'megafon_' . $this->get('callid') . '_' . $this->get('user');
    }
    
    protected function getAssignedUser() {
        $db = \PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_megafon_id=?", array(
            $this->getGravitelUserId()
        ));
        
        if($result && $resultRow = $db->fetchByAssoc($result)) {
            return \Vtiger_Record_Model::getInstanceById($resultRow['id'], 'Users');
        }
        
        return null;
    }
    
    protected function getProviderName() {
        return ProvidersEnum::MEGAFON;
    }
}
