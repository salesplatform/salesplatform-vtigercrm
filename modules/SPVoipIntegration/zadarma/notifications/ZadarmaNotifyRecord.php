<?php
namespace SPVoipIntegration\zadarma\notifications;

use SPVoipIntegration\loggers\Logger;

class ZadarmaNotifyRecord extends ZadarmaNotification {    
    
    public function getValidationString() {
        return $this->get('pbx_call_id') . $this->get('call_id_with_rec');
    }

    public function process() {
        $pbxModel = $this->findCallByRecordedId();
        if ($pbxModel != null) {
            $pbxModel->set('mode', 'edit');
            try {
                $zadarmaFactory = new \SPVoipIntegration\zadarma\ZadarmaFactory;
                $apiManager = $zadarmaFactory->getCallApiManager();
                $audioLink = $apiManager->getRecordLink($this->get('call_id_with_rec'));

                $soundFileContent = file_get_contents($audioLink);
                if($soundFileContent === false) {
                    throw new \Exception('Cant get audou data');
                }
                $filePath = $this->generateSoundFileName();
                $status = file_put_contents($filePath, $soundFileContent);
                if ($status === false) {
                    throw new \Exception('Cant save audio file');
                }

                $pbxModel->set('recordingurl', $filePath);
                $pbxModel->set('sp_is_local_cached', 1);
                $pbxModel->save();
            } catch (\Exception $ex) {
                Logger::log('Error on save audiofile', $ex);
            }
        }
    }

    protected function canCreatePBXRecord() {
        return false;
    }

    protected function getCustomerPhoneNumber() {
        return '';
    }
    
    private function generateSoundFileName() {
        return "storage/{$this->get('call_id_with_rec')}.mp3";
    }
    
    private function findCallByRecordedId() {
        $db = \PearDatabase::getInstance();
        $pbxModel = null;
        $result = $db->pquery("SELECT pbxmanagerid FROM vtiger_pbxmanager "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_pbxmanager.pbxmanagerid "
                . "WHERE vtiger_crmentity.deleted=0 AND vtiger_pbxmanager.sp_recorded_call_id=?", array($this->get('call_id_with_rec')));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $pbxModel = \Vtiger_Record_Model::getInstanceById($resRow['pbxmanagerid']);
        }
        return $pbxModel;
    }

}