<?php
namespace SPVoipIntegration\sipuni\notifications;

class SipuniNotifyCall extends SipuniNotification {
    
    protected $fieldsMapping = array(
        'sourceuuid' => 'call_id', 
        'callstatus' => 'callstatus',
        'user' => 'user',
        'sp_called_from_number' => 'src_num',
        'sp_called_to_number' => 'dst_num',
        'sp_voip_provider' => 'sp_voip_provider',
        'customernumber' => 'customernumber',
        'direction' => 'direction',
        'src_type' => 'src_type',
        'dst_type' => 'dst_type',
        'starttime' => 'starttime'
    );
    
    protected function prepareNotificationModel() {
        $this->set('callstatus', 'ringing');
        $this->set('starttime', date('Y-m-d H:i:s'));
        if($this->get('src_type') == '2') {
            if($this->get('dst_type') == '2') {
                $this->set('direction', 'inbound');
                $this->user_number = $this->get('short_src_num');
                $this->set('src_num', $this->get('short_src_num'));
                $this->set('dst_num', $this->get('short_dst_num'));
            }
            else {
                 $this->set('direction', 'outbound');
                 $this->set('customernumber', $this->get('dst_num'));
                 $this->set('src_num', $this->get('short_src_num'));
                 $this->user_number = $this->get('short_src_num');
                 
            }
        }
        else {
            $this->set('direction', 'inbound');
            $this->set('customernumber', $this->get('src_num'));
            $this->set('dst_num', $this->get('short_dst_num'));
            $this->user_number = $this->get('short_dst_num');
            $this->set('sourceuuid', $this->get('call_id') . $this->get('short_dst_num'));
        }
        
        parent::prepareNotificationModel();  
    }
    
    protected function canCreatePBXRecord() {
        return true;
    }
    
    protected function getCustomerPhoneNumber() {
        return $this->get('customernumber');
    }

}
