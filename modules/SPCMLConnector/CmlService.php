<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/SPCMLConnector/AbstractProduct.php';

/**
 * Representation of service described in CommerceML.
 */
class CmlService extends AbstractProduct {  
    
    /**
     * Return representation of the product as array - REST API needed.
     * @return array
     */
    public function toRestDescription() {
        $restDescription['servicename'] = $this->name;
        $restDescription['discontinued'] = 1;           //active service
        $restDescription['service_usageunit'] = $this->unitName;
        $restDescription['unit_price'] = $this->price;
        $restDescription['qty'] = $this->stockCount;
        $restDescription['currency_id'] = $this->currencyId;
        $restDescription['conversion_rate'] = $this->conversionRate;
        $restDescription['one_s_id'] = $this->oneEsIdentifier;
        
        return $restDescription;
    }

}


?>