<?php
namespace SPVoipIntegration\westcallspb\notifications;

use SPVoipIntegration\gravitel\notifications\GravitelContactNotification;

class WestCallSPBContactNotification extends GravitelContactNotification {
    use GravitelAdapterTrait;
}
