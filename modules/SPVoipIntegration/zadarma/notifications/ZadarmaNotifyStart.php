<?php
namespace SPVoipIntegration\zadarma\notifications;

class ZadarmaNotifyStart extends ZadarmaNotifyInternal {        
    
    protected function getUserPhoneNumber() {
        return $this->get('called_did');
    }
    
}
