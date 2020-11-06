<?php
namespace SPVoipIntegration\gravitel\notifications;

class GravitelEventNotification extends AbstractGraviltelNotification {
    
    private $dataMapping = array(
        'user' => 'user', 
        'callstatus' => 'callstatus',
        'sourceuuid' => 'sourceuuid',
    );
    

    protected function getNotificationDataMapping() {
        return $this->dataMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('sourceuuid', $this->getSourceUUId());
        
        $userModel = $this->getAssignedUser();
        if($userModel != null) {
            $this->set('user', $userModel->getId());
        }
        
        $direction = $this->getDirection();
        if($direction != null) {
            $this->dataMapping['direction'] = 'direction';
            $this->set('direction', $direction);
        }
        
        $status = $this->getStatus();
        if($status != null) {
            $this->set('callstatus', $status);            
        }
        $type = $this->getType();
        if ($type === GravitelEventType::INCOMING || $type === GravitelEventType::OUTGOING) {
            $this->dataMapping['customernumber'] = 'phone';
        }
        
        $this->processDates();
    }
    
    private function getStatus() {
        $type = $this->getType();
        if($type === GravitelEventType::INCOMING || $type === GravitelEventType::OUTGOING) {
            return 'ringing';
        }
        
        if($type === GravitelEventType::ACCEPTED) {
            return 'in-progress';
        }
        
        if($type === GravitelEventType::COMPLETED) {
            return 'completed';
        }
        
        if($type === GravitelEventType::CANCELLED) {
            return 'no-answer';
        }
        
        return null;
    }
    
    private function getDirection() {
        $type = $this->getType();
        if($type === GravitelEventType::INCOMING) {
            return 'inbound';
        }
        
        if($type === GravitelEventType::OUTGOING) {
            return 'outbound';
        }
        
        return null;
    }
    
    
    private function processDates() {
        $type = $this->getType();
        if($type === GravitelEventType::INCOMING || $type === GravitelEventType::OUTGOING) {
            $this->dataMapping['starttime'] = 'starttime';
            $this->dataMapping['sp_voip_provider'] = 'sp_voip_provider';
            
            $this->set('starttime', date("Y-m-d H:i:s"));
            $this->set('sp_voip_provider', $this->getProviderName());
        }
        
        if($type === GravitelEventType::ACCEPTED) {
            $currentTime = time();
            $startTime = $this->getStartTime();
            if($startTime && ($currentTime - $startTime) > 0) {
                $this->dataMapping['totalduration'] = 'totalduration';
                $this->set('totalduration', $currentTime - $startTime);
            }
        }
        
        if($type === GravitelEventType::COMPLETED || $type === GravitelEventType::CANCELLED) {
            $currentTime = time();
            
            $this->dataMapping['endtime'] = 'endtime';
            $this->set('endtime', date('Y-m-d H:i:s', $currentTime));
            
            $startTime = $this->getStartTime();
            $oldTotalDuration = $this->getTotalDuration();                        
            if($startTime && ($currentTime - $startTime) > 0) {
                $newTotalDuration = $currentTime - $startTime;
                $this->dataMapping['totalduration'] = 'totalduration';
                $this->set('totalduration', $newTotalDuration);
                if ($oldTotalDuration !== null && $type === GravitelEventType::COMPLETED) {
                    $billDuration = $newTotalDuration - $oldTotalDuration;
                    if ($billDuration > 0) {                    
                        $this->dataMapping['billduration'] = 'billduration';
                        $this->set('billduration', $billDuration);
                    }
                }
            }
        }
    }
    
    private function getTotalDuration() {
        $totalDuration = null;
        if($this->pbxManagerModel != null) {
            $totalDuration = $this->pbxManagerModel->get('totalduration');            
        }
        return $totalDuration;
    }
    
    private function getStartTime() {
        $startTime = null;
        if($this->pbxManagerModel != null) {
            $startDateTime = $this->pbxManagerModel->get('starttime');
            if(!empty($startDateTime)) {
                $startTime = strtotime($startDateTime);
            }
        }
        
        return $startTime;
    }
}
