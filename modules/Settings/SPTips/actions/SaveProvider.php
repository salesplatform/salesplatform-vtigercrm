<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPTips_SaveProvider_Action extends Settings_Vtiger_Index_Action {
    
    const providersTable = 'sp_tips_providers';
    
    public function process (Vtiger_Request $request) {
        $recordId = $request->get('record'); 
        $provider = Settings_SPTips_Provider_Model::getInstanceById($recordId);
        if($provider != null) {
            foreach($provider->getSettingsFields() as $fieldName) {
                if($request->has($fieldName)) {
                    $provider->setSetting($fieldName, $request->get($fieldName));
                }
            }
            
            $provider->save();
        }
        
        header("Location: index.php?module=SPTips&view=Index&parent=Settings");
    }

}