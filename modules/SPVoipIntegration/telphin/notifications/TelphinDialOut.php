<?php
namespace SPVoipIntegration\telphin\notifications;

class TelphinDialOut extends TelphinDialIn {
    
    protected $direction = 'outbound';
    
    public function __construct($values = array()) {
        parent::__construct($values);
        $this->fieldsMapping['customernumber'] = 'CalledNumber';
    }

    protected function getCustomerPhoneNumber() {
        return $this->get('CalledNumber');
    }

    protected function getUserPhoneNumber() {
        return $this->get('CallerIDNum');
    }
    
}
