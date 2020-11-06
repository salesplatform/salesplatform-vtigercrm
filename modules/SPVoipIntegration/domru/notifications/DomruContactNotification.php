<?php
namespace SPVoipIntegration\domru\notifications;

use SPVoipIntegration\gravitel\notifications\GravitelContactNotification;

class DomruContactNotification extends GravitelContactNotification {
    use GravitelAdapterTrait;
}
