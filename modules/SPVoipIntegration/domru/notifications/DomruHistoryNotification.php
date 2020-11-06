<?php
namespace SPVoipIntegration\domru\notifications;

use SPVoipIntegration\gravitel\notifications\GravitelHistoryNotification;

class DomruHistoryNotification extends GravitelHistoryNotification {
    use GravitelAdapterTrait;    
}