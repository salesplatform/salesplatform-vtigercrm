<?php

namespace SPVoipIntegration\mcntelecom\notifications;

class MCNInboundEnd extends MCNOutboundEnd {
    
    protected function getSourceUUId() {
        return MCNAbstractNotification::SOURCE_ID_PREFIX . $this->get('call_id') . "_" . $this->get('who_answered')[0];
    }
    
}
