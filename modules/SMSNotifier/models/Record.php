<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/modules/SMSNotifier/SMSNotifier.php');

class SMSNotifier_Record_Model extends Vtiger_Record_Model {

	public static function SendSMS($message, $toNumbers, $currentUserId, $recordIds, $moduleName) {
		return SMSNotifier::sendsms($message, $toNumbers, $currentUserId, $recordIds, $moduleName);
	}

	public function checkStatus() {
		$statusDetails = SMSNotifier::smsquery($this->get('id'));
                
        // SalesPlatform.ru begin
//		$statusColor = $this->getColorForStatus($statusDetails[0]['status']);
		$statusColor = $this->getColorForStatus($statusDetails->status);

//		$this->setData($statusDetails[0]);
		$this->setData($statusDetails);
        // SalesPlatform.ru end

        // SalesPlatform.ru begin
        return $statusDetails;
		//return $this;
        // SalesPlatform.ru end
	}

	public function getCheckStatusUrl() {
		return "index.php?module=".$this->getModuleName()."&view=CheckStatus&record=".$this->getId();
	}

	public function getColorForStatus($smsStatus) {
		if ($smsStatus == 'Processing') {
			$statusColor = '#FFFCDF';
		} elseif ($smsStatus == 'Dispatched') {
			$statusColor = '#E8FFCF';
		} elseif ($smsStatus == 'Failed') {
			$statusColor = '#FFE2AF';
		} else {
			$statusColor = '#FFFFFF';
		}
		return $statusColor;
	}

    // SalesPlatform.ru begin
    public static function getBackgroundColorForStatus($smsStatus) {
        if ($smsStatus == 'Processing') {
            $statusColor = '#FFFCDF';
        } elseif ($smsStatus == 'Dispatched') {
            $statusColor = '#E8FFCF';
        } elseif ($smsStatus == 'Failed') {
            $statusColor = '#FFE2AF';
        } elseif ($smsStatus == 'Delivered') {
            $statusColor = '#25b42f';
        } else {
            $statusColor = '#FFFFFF';
        }
        return $statusColor;
    }
    // SalesPlatform.ru end
}