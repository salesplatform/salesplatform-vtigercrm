<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Workflows_Field_Model extends Vtiger_Field_Model {

	/**
	 * Function to get all the supported advanced filter operations
	 * @return <Array>
	 */
	public static function getAdvancedFilterOptions() {
		return array(
            // SalesPlatform.ru begin
            'is' => vtranslate('LBL_EQUALS'),
			'contains' => vtranslate('LBL_CONTAINS'),
			'does not contain' => vtranslate('LBL_DOES_NOT_CONTAIN'),
			'starts with' => vtranslate('LBL_STARTS_WITH'),
			'ends with' => vtranslate('LBL_ENDS_WITH'),
			'has changed to' => vtranslate('LBL_HAS_CHANGED_TO'),
			'is empty' => vtranslate('LBL_IS_EMPTY'),
			'is not empty' => vtranslate('LBL_IS_NOT_EMPTY'),
			'less than' => vtranslate('LBL_LESS_THAN'),
			'greater than' => vtranslate('LBL_GREATER_THAN'),
			'does not equal' => vtranslate('LBL_NOT_EQUAL_TO'),
			'less than or equal to' => vtranslate('LBL_LESS_THAN_OR_EQUAL'),
			'greater than or equal to' => vtranslate('LBL_GREATER_OR_EQUAL'),
			'has changed' => vtranslate('LBL_CHANGED'),
			'before' => vtranslate('LBL_BEFORE'),
			'after' => vtranslate('LBL_AFTER'),
			'between' => vtranslate('LBL_BETWEEN'),
			'is added' => vtranslate('LBL_IS_ADDED'),
            'is not' => vtranslate('LBL_NOT_EQUAL_TO'),
            'equal to' => vtranslate('LBL_EQUALS'),
            'is today' => vtranslate('LBL_IS_TODAY'), 
            'less than days ago' => vtranslate('LBL_LESS_THAN_DAYS_AGO'), 
            'more than days ago' => vtranslate('LBL_MORE_THAN_DAYS_AGO'), 
            //SalesPlatform.ru begin Localisation fix
            'less than hours before' => vtranslate('LBL_LESS_THAN_HOURS_BEFORE'),
            'less than hours later'  => vtranslate('LBL_LESS_THAN_HOURS_LATER'),
            'more than hours before' => vtranslate('LBL_MORE_THAN_HOURS_BEFORE'),
            'more than hours later'  => vtranslate('LBL_MORE_THAN_HOURS_LATER'),
            //SalesPlatform.ru end Localisation fix
            'in less than' => vtranslate('LBL_LESS_THAN'), 
            'in more than' => vtranslate('LBL_GREATER_THAN'),
			'days ago' => vtranslate('LBL_DAYS_AGO'), 
            'days later' => vtranslate('LBL_DAYS_LATER'),
            'has changed from' => vtranslate('LBL_HAS_CHANGED_FROM'),
//			'is' => 'is',
//			'contains' => 'contains',
//			'does not contain' => 'does not contain',
//			'starts with' => 'starts with',
//			'ends with' => 'ends with',
//			'has changed' => 'has changed',
//			'has changed to' => 'has changed to',
//			'is empty' => 'is empty',
//			'is not empty' => 'is not empty',
//			'less than' => 'less than',
//			'greater than' => 'greater than',
//			'does not equal' => 'does not equal',
//			'less than or equal to' => 'less than or equal to',
//			'greater than or equal to' => 'greater than or equal to',
//			'has changed' => 'has changed',
//			'before' => 'before',
//			'after' => 'after',
//			'between' => 'between',
//			'is added' => 'is added',
            // SalesPlatform.ru end
			'less than days ago' => 'LBL_LESS_THAN_DAYS_AGO',
			'less than days later' => 'LBL_LESS_THAN_DAYS_LATER',
			'more than days ago' => 'LBL_MORE_THAN_DAYS_AGO',
			'more than days later' => 'LBL_MORE_THAN_DAYS_LATER',
			'days ago' => 'LBL_DAYS_AGO',
			'days later' => 'LBL_DAYS_LATER',
			'in less than' => 'LBL_IN_LESS_THAN',
			'in more than' => 'LBL_IN_MORE_THAN',
		);
	}

	/**
	 * Function to get the advanced filter option names by Field type
	 * @return <Array>
	 */
	public static function getAdvancedFilterOpsByFieldType() {
		return array(
			'string' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'salutation' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'text' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'url' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'email' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'phone' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
			'integer' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'has changed'),
			'double' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'has changed'),
			'currency' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'has changed', 'is not empty'),
			'picklist' => array('is', 'is not', 'starts with', 'ends with', 'contains', 'does not contain', 'has changed', 'has changed to', 'has changed from', 'is empty', 'is not empty'),
			'multipicklist' => array('is', 'is not','contains','does not contain', 'has changed', 'has changed to'),
			'datetime' => array('is', 'is not', 'has changed', 'before', 'after', 'is today', 'is tomorrow', 'is yesterday', 'less than hours before', 'less than hours later',
				'more than hours before', 'more than hours later', 'less than days ago', 'less than days later', 'more than days ago', 'more than days later', 'days ago', 'days later', 'is empty', 'is not empty'),
			'time' => array('is', 'is not', 'has changed', 'is not empty'),
			'date' => array('is', 'is not', 'has changed', 'between', 'before', 'after', 'is today', 'is tomorrow', 'is yesterday', 'less than days ago', 'more than days ago', 'less than days later',
				'more than days later', 'in less than', 'in more than', 'days ago', 'days later', 'is empty', 'is not empty'),
			'boolean' => array('is', 'is not', 'has changed'),
			'reference' => array('is empty', 'is not empty', 'has changed'),
			'multireference'=>array('has changed'),
			'owner' => array('has changed','is','is not'),
			'ownergroup' => array('has changed','is','is not'),
			'recurrence' => array('is', 'is not', 'has changed'),
			'comment' => array('is added'),
			'image' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'is empty', 'is not empty'),
			'percentage' => array('equal to', 'less than', 'greater than', 'does not equal', 'less than or equal to', 'greater than or equal to', 'has changed', 'is not empty'),
			'currencyList' => array('is', 'is not', 'has changed', 'has changed to', 'has changed from'),
            'SPTextArea' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
            'file' => array('is', 'is not', 'contains', 'does not contain', 'starts with', 'ends with', 'has changed', 'is empty', 'is not empty'),
		);
	}

	/**
	 * Function to get comment field which will useful in creating conditions
	 * @param <Vtiger_Module_Model> $moduleModel
	 * @return <Vtiger_Field_Model>
	 */
	public static function getCommentFieldForFilterConditions($moduleModel) {
		$commentField = new Vtiger_Field_Model();
		$commentField->set('name', '_VT_add_comment');
		$commentField->set('label', 'Comment');
		$commentField->setModule($moduleModel);
		$commentField->fieldDataType = 'comment';

		return $commentField;
	}

	/**
	 * Function to get comment fields list which are useful in tasks
	 * @param <Vtiger_Module_Model> $moduleModel
	 * @return <Array> list of Field models <Vtiger_Field_Model>
	 */
	public static function getCommentFieldsListForTasks($moduleModel) {
		$commentsFieldsInfo = array('lastComment' => 'Last Comment', 'last5Comments' => 'Last 5 Comments', 'allComments' => 'All Comments');

		$commentFieldModelsList = array();
		foreach ($commentsFieldsInfo as $fieldName => $fieldLabel) {
			$commentField = new Vtiger_Field_Model();
			$commentField->setModule($moduleModel);
			$commentField->set('name', $fieldName);
			$commentField->set('label', $fieldLabel);
			$commentFieldModelsList[$fieldName] = $commentField;
		}
		return $commentFieldModelsList;
	}
}
