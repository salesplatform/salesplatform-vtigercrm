<?php

namespace SPVoipIntegration\yandex\notifications;

use SPVoipIntegration\yandex\YandexFactory;
use SPVoipIntegration\loggers\Logger;

class OutgoingCallCompleted extends AbstractYandexNotification{    
    
    private $pbxModel = null;
    
    protected $fieldsMapping = array(
        'endtime' => 'endtime',
        'billduration' => 'billduration',
        'totalduration' => 'totalduration',
        'callstatus' => 'callstatus'
    );
    
    public function process() {
        $this->pbxModel = $this->findCallModel();
        if ($this->pbxModel == null) {
            return;
        }
        $this->pbxModel = $this->doCompletedActions($this->pbxModel);
        $apiManager = YandexFactory::getCallApiManager();
        $recordUUID = $this->pbxModel->get('sourceuuid');
        $idInfo = explode('_', $recordUUID);
        try {
            $recordLink = $apiManager->getRecordLink($idInfo[1]);
            if ($recordLink) {
                $this->pbxModel->set('recordingurl', $recordLink);
                $this->downloadRecord($recordLink);
            }
        } catch(\Exception $ex) {
            Logger::log('Download record error', $ex);
        }
        $this->pbxModel->save();
    }
    
    protected function canCreatePBXRecord() {
        return false;
    }        
    
    protected function findCallModel() {
        $db = \PearDatabase::getInstance();
        $pbxModel = null;
        $callId = $this->get('Body')['Id'];
        $query = "SELECT pbxmanagerid FROM vtiger_pbxmanager "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_pbxmanager.pbxmanagerid "
                . "WHERE vtiger_crmentity.deleted=0 AND vtiger_pbxmanager.callstatus IN (?,?) AND "
                . "vtiger_pbxmanager.sourceuuid LIKE '%" . AbstractYandexNotification::SOURCE_ID_PREFIX . $callId . "%'";
        $result = $db->pquery($query, array('ringing', 'in-progress'));
        
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $pbxModel = \Vtiger_Record_Model::getInstanceById($resRow['pbxmanagerid']);
        }
        return $pbxModel;
    }
    
    protected function downloadRecord($link) {
        $audioContent = file_get_contents($link);
        if($audioContent === false) {
            return;
        }
        
        $filePath = $this->generateSoundFileName();
        $status = file_put_contents($filePath, $audioContent);
        if($status === false) {
            return;
        }
        
        $this->pbxModel->set('recordingurl', $filePath);
        $this->pbxModel->set('sp_is_local_cached', 1);
    }

    protected function prepareNotificationModel() {
        
    }
    
    private function generateSoundFileName() {
        return "storage/{$this->pbxModel->get('sourceuuid')}.mp3";
    }

}
