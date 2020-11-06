<?php
namespace SPVoipIntegration\megafon\notifications;

use SPVoipIntegration\gravitel\notifications\GravitelEventNotification;

class MegafonEventNotification extends GravitelEventNotification {        
    use GravitelAdapterTrait;
}
