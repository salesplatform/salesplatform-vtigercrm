<?php

namespace SPVoipIntegration\mango\notifications;

class MangoNotifyCall extends MangoNotification {
    protected $fieldsMapping = array(
        'sourceuuid' => 'sourceuuid',
        'callstatus' => 'call_state',
        'user' => 'sp_user',
        'sp_called_from_number' => 'sp_called_from_number',
        'sp_called_to_number' => 'sp_called_to_number',
        'sp_call_status_code' => 'disconnect_reason',
        'sp_voip_provider' => 'sp_voip_provider',
        'customernumber' => 'customernumber',
        'direction' => 'direction',
        'starttime' => 'starttime',
        'endtime' => 'endtime',
        'totalduration' => 'totalduration',
        'sp_call_status_code' => 'disconnect_reason'
    );

    protected function prepareNotificationModel() {
        $to = (array) $this->get('to');
        $from = (array) $this->get('from');
        switch ($this->get('call_state')) {
            case 'Appeared':
                $this->set('call_state', 'ringing');
                $time = date('Y-m-d H:i:s');
                $this->set('starttime', $time);
                break;
            case 'Connected':
                $this->set('call_state', 'in progress');
                unset($this->fieldsMapping['starttime']);
                break;
            case 'OnHold':
                $this->set('call_state', 'on hold');
                unset($this->fieldsMapping['starttime']);
                break;
            case 'Disconnected':
                $state = $this->pbxManagerModel->get('callstatus');
                if ($state == 'in progress' || $state == 'on hold') {
                    $this->set('call_state', 'completed');
                } else {
                    $this->set('call_state', 'no-response');
                }
                $time = date('Y-m-d H:i:s');
                unset($this->fieldsMapping['starttime']);
                $createdtime = $this->pbxManagerModel->get('starttime');
                if ($createdtime !== FALSE) {
                    $this->set('totalduration', strtotime($time) - strtotime($createdtime));
                }
                $this->set('endtime', $time);
                break;
        }

        if (array_key_exists('extension', $this->get('to'))) {
            $this->set('sp_called_to_number', $to['extension']);
            $this->sp_user = $to['extension'];
            $this->set('sourceuuid', $this->get('entry_id') . $to['extension']);
        } else {
            $this->set('sp_called_to_number', $to['number']);
            $this->set('customernumber', $to['number']);
            $this->set('sourceuuid', $this->get('entry_id') . $from['extension']);
        }
        if (array_key_exists('extension', $this->get('from'))) {
            $this->set('sp_called_from_number', $from['extension']);
            $this->sp_user = $from['extension'];
            if (array_key_exists('extension', $this->get('to'))) {
                $this->set('direction', 'internal');
            } else {
                $this->set('direction', 'outbound');
            }
        } else {
            $this->set('sp_called_from_number', $from['number']);
            $this->set('customernumber', $from['number']);
            $this->set('direction', 'inbound');
        }
        parent::prepareNotificationModel();
    }

    protected function canCreatePBXRecord() {
        return strpos($this->get('json'),'Appeared') !== false;
    }

    protected function getCustomerPhoneNumber() {
        return $this->get('customernumber');
    }

}
