<?php

class Settings_SPVoipIntegration_Module_Model extends Settings_Vtiger_Module_Model{
    
    public static function getCleanInstance(){
        return new self;
    }
    
    public function getDefaultViewName() {
		return 'Index';
	}
    
    public function getModuleName(){
        return "SPVoipIntegration";
    }    
    
    public function getMenuItem() {
        $menuItem = Settings_Vtiger_MenuItem_Model::getInstance('VoipIntegration');
        return $menuItem;
    }
    
    public function getDetailViewUrl() {
        $menuItem = $this->getMenuItem();
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getDefaultViewName().'&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
    }
    
    public function getEditViewUrl() {
        $menuItem = $this->getMenuItem();
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view=Edit'.'&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
    }
}