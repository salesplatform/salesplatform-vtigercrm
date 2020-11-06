<?php
namespace SPVoipIntegration\zadarma\notifications;

class ZadarmaNotifyOutEnd extends ZadarmaNotifyEnd {        

    public function getValidationString() {
        return $this->get('internal') . $this->get('destination') . $this->get('call_start');
    }       
}
