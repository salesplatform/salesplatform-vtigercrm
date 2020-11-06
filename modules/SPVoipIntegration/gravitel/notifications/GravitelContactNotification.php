<?php
namespace SPVoipIntegration\gravitel\notifications;

use SPVoipIntegration\ProvidersErrors;

class GravitelContactNotification extends AbstractGraviltelNotification {

    
    public function validateNotification() {
        $requestToken = $this->getRequestToken();
        if(empty($requestToken)) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        $crmToken = $this->getCrmSavedToken();
        if(empty($crmToken)) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        if($requestToken != $crmToken) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        $phone = $this->getCustomerPhoneNumber('phone');
        if(empty($phone)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
        
        $gravitelUserId = $this->getGravitelUserId();
        if(empty($gravitelUserId)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
    }
    
    public function process() {
        $assignedUser = null;
        $customerModel = $this->getCustomerByPhone();
        
        $responseData = array();
        if($customerModel != null) {
            $responseData['contact_name'] = $customerModel->getName();
            
            $userId = $customerModel->get('assigned_user_id');
            $db = \PearDatabase::getInstance();
            $result = $db->pquery("SELECT 1 FROM vtiger_users WHERE id=?", array($userId));
            if($result && $resultRow = $db->fetchByAssoc($result)) {
                $assignedUser = \Vtiger_Record_Model::getInstanceById($userId);
            }
        }
        
        if($assignedUser != null && $assignedUser->get('sp_gravitel_id') != null) {
            $responseData['responsible'] = $assignedUser->get('sp_gravitel_id');
        }
        
        echo json_encode($responseData);
    }
    
    protected function getNotificationDataMapping() {
        return array();
    }


    protected function prepareNotificationModel() {
        /* No need */
    }
}
