<?php
namespace SPVoipIntegration\integration;

use SPVoipIntegration\ProvidersEnum;
use SPVoipIntegration\zadarma\ZadarmaFactory;
use SPVoipIntegration\gravitel\GravitelManagerFactory;
use SPVoipIntegration\zebra\ZebraTelecomFactory;
use SPVoipIntegration\megafon\MegafonManagerFactory;
use SPVoipIntegration\telphin\TelphinFactory;
use SPVoipIntegration\uiscom\UIScomFactory;
use SPVoipIntegration\mango\MangoFactory;
use SPVoipIntegration\yandex\YandexFactory;
use SPVoipIntegration\domru\DomruManagerFactory;
use SPVoipIntegration\westcallspb\WestCallSPBManagerFactory;
use SPVoipIntegration\mcntelecom\MCNFactory;
use SPVoipIntegration\rostelecom\RostelecomFactory;
use SPVoipIntegration\sipuni\SipuniFactory;

abstract class AbstractCallManagerFactory {

    public abstract function getNotificationModel($requestData);
    public abstract function getCallApiManager();
    
    /**
     * 
     * @return AbstractCallManagerFactory
     * @throws \Exception
     */
    public static function getDefaultFactory() {
        $defaultProvider = \Settings_SPVoipIntegration_Record_Model::getDefaultProvider();
        return self::getEventsFacory($defaultProvider);
    }
    
    public static function getEventsFacory($providerName) {
        switch ($providerName) {
            case ProvidersEnum::ZADARMA :
                return new ZadarmaFactory();
            case ProvidersEnum::GRAVITEL:
                return new GravitelManagerFactory();
            case ProvidersEnum::ZEBRA:
                return new ZebraTelecomFactory();
            case ProvidersEnum::MEGAFON:
                return new MegafonManagerFactory();
            case ProvidersEnum::TELPHIN:
                return new TelphinFactory();
            case ProvidersEnum::UISCOM:
                return new UIScomFactory();
            case ProvidersEnum::MANGO:
                return new MangoFactory();
            case ProvidersEnum::YANDEX:
                return new YandexFactory();
            case ProvidersEnum::DOMRU:
                return new DomruManagerFactory();
            case ProvidersEnum::WESTCALL_SPB:
                return new WestCallSPBManagerFactory();
            case ProvidersEnum::MCN:
                return new MCNFactory();
            case ProvidersEnum::ROSTELECOM:
                return new RostelecomFactory();
            case ProvidersEnum::SIPUNI:
                return new SipuniFactory();
            default :
                throw new \Exception("Unknown voip");
        }
    }
}