<?php
namespace SPVoipIntegration\integration;
abstract class AbstractCallApiManager {
    public abstract function doOutgoingCall($number);
}