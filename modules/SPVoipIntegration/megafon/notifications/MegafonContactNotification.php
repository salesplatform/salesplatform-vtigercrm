<?php
namespace SPVoipIntegration\megafon\notifications;

use SPVoipIntegration\gravitel\notifications\GravitelContactNotification;

class MegafonContactNotification extends GravitelContactNotification {
    use GravitelAdapterTrait;
}
