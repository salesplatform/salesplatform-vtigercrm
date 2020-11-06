<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class Settings_SPSocialConnector_Module_Model extends Settings_Vtiger_Module_Model{
    
    private static $SETTINGS_REQUIRED_PARAMETERS = array(
        'vk_app_id' => 'text', 
        'vk_app_secret' => 'text',
        'vk_access_token' => 'text',
        'tw_app_key' => 'text',
        'tw_app_secret' => 'text', 
        );
    
    /**
	 * Function to get the module model
	 * @return string
	 */
    public static function getCleanInstance(){
        return new self;
    }
    
    /**
	 * Function to get the ListView Component Name
	 * @return string
	 */
    public function getDefaultViewName() {
		return 'Index';
	}
    
	/**
	 * Function to get the EditView Component Name
	 * @return string
	 */
	public function getEditViewName(){
		return 'Edit';
	}
    
    /**
	 * Function to get the Module Name
	 * @return string
	 */
    public function getModuleName(){
        return "SPSocialConnector";
    }
    
     public function getParentName() {
        return parent::getParentName();
    }
    
    public function getModule($raw=true) {
		$moduleName = Settings_SPSocialConnector_Module_Model::getModuleName();
		if(!$raw) {
			$parentModule = Settings_SPSocialConnector_Module_Model::getParentName();
			if(!empty($parentModule)) {
				$moduleName = $parentModule.':'.$moduleName;
			}
		}
		return $moduleName;
	}
    
    public function getMenuItem() {
        $menuItem = Settings_Vtiger_MenuItem_Model::getInstance('LBL_SOCIALCONNECTOR_SETTINGS');
        return $menuItem;
    }
    
    /**
    * Function to get the url for default view of the module
    * @return <string> - url
    */
    public function getDefaultUrl() {
            return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getDefaultViewName();
    }

    public function getDetailViewUrl() {
        $menuItem = $this->getMenuItem();
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getDefaultViewName().'&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
    }


   /**
    * Function to get the url for Edit view of the module
    * @return <string> - url
    */
    public function getEditViewUrl() {
            $menuItem = $this->getMenuItem();
            return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getEditViewName().'&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
    }
    
    /**
     * Function to get Settings edit view params
     * returns <array>
     */
    public function getSettingsParameters() {
        return self::$SETTINGS_REQUIRED_PARAMETERS;
    }
    
}
