<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Vtiger_SPTextArea_UIType extends Vtiger_Base_UIType {
    
    public function getDisplayValue($value) {
        $fieldModel = $this->get('field');
        if($fieldModel != null && $fieldModel->isCKEEnabled()) {
            return decode_html($value);
        }
        
		return nl2br($value);
	}
    
    /**
     * Function to get the Template name for the current UI Type Object
     * @return <String> - Template Name
     */
    public function getTemplateName() {
        return 'uitypes/Text.tpl';
    } 
}

