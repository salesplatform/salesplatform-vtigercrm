<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'include/events/VTEventHandler.inc';

class Settings_Search_RecordSearchLabelUpdater_Handler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb;

		if ($eventName == 'vtiger.entity.aftersave') {
            $module = $data->getModuleName();
            if($module != "Users"){
                $labelInfo = self::computeCRMRecordLabelsForSearch($module, $data->getId(),true);
				if (count($labelInfo) > 0) {
					$label = decode_html($labelInfo[$data->getId()]['name']);
					$search = decode_html($labelInfo[$data->getId()]['search']);
					if ($adb->num_rows($adb->pquery('SELECT * FROM berli_globalsearch_data where gscrmid =?', array($data->getId())))==0) {
							$adb->pquery('INSERT INTO `berli_globalsearch_data` (`gscrmid`, `searchlabel`) VALUES (?,?)', array($data->getId(),''));
					}
                    //SalesPlatform.ru begin #3955
					$adb->pquery('UPDATE berli_globalsearch_data INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = berli_globalsearch_data.gscrmid SET searchlabel=? WHERE crmid=?', array($search, $data->getId()));
                    //$adb->pquery('UPDATE berli_globalsearch_data INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = berli_globalsearch_data.gscrmid SET label=?,searchlabel=? WHERE crmid=?', array($search, $search, $data->getId()));
                    //SalesPlatform.ru end
				}
            }
		}
	}

	public function computeCRMRecordLabelsForSearch($module, $ids) {
		$log = vglobal('log');
		$log->debug("Entering Settings_Search_Handlers_Model::computeCRMRecordLabelsForSearch() method ...");
		$adb = PearDatabase::getInstance();
		if (!is_array($ids))
			$ids = array($ids);

		if ($module == 'Events') {
			$module = 'Calendar';
		}

		if ($module) {
			$entityDisplay = array();
			if ($ids) {
				if ($module == 'Groups') {
					$metainfo = array('tablename' => 'vtiger_groups', 'entityidfield' => 'groupid', 'fieldname' => 'groupname');
					/* } else if ($module == 'DocumentFolders') { 
					  $metainfo = array('tablename' => 'vtiger_attachmentsfolder','entityidfield' => 'folderid','fieldname' => 'foldername'); */
				} 
				else {
					$metainfo = Vtiger_Functions::getEntityModuleInfo($module);
				}
				$modulename = $metainfo['modulename'];
				$table = $metainfo['tablename'];
				$idcolumn = $metainfo['entityidfield'];
				$columns_name = explode(',', $metainfo['fieldname']);

				$primary = CRMEntity::getInstance($modulename);
				$moduleothertables = $primary->tab_name_index;
				$moduleothertables = array_diff($moduleothertables, array('crmid'));
				$otherquery ='';
				foreach ($moduleothertables as $othertable => $otherindex) {
					if (isset($moduleothertables)) {
                                            // SalesPlatform.ru begin
						//$otherquery .= " LEFT JOIN $othertable ON $othertable.$otherindex=$table.$idcolumn";
                                            $otherquery .= " INNER JOIN $othertable ON $othertable.$otherindex=$table.$idcolumn";
                                            // SalesPlatform.ru end
					} 
					else {
						$otherquery .= '';
					}
				}
				$sqlquery ="SELECT searchcolumn FROM  berli_globalsearch_settings LEFT JOIN vtiger_entityname ON vtiger_entityname.tabid = berli_globalsearch_settings.gstabid ";
				$sqlquery .= $otherquery;
				$sqlquery .= " WHERE vtiger_entityname.modulename = '".$modulename."' ";

				$columns_search = $adb->pquery($sqlquery, array());
				$columns_search = $columns_search->fields;
				$columns_search = explode(',', $columns_search['searchcolumn']);
				$columns = array_unique(array_merge($columns_name, $columns_search));

				$moduleothertableslim = $moduleothertables;
				unset($moduleothertableslim[$table], $moduleothertableslim['vtiger_crmentity']);

				foreach ($moduleothertableslim as $othertable => $otherindex) {
					if (isset($moduleothertableslim)) {
						$otherqueryslim .= " LEFT JOIN $othertable ON $othertable.$otherindex=$table.$idcolumn";
					} 
					else {
						$otherqueryslim .= '';
					}
				}

				$full_idcolumn = $table.'.'.$idcolumn;
				$sql = sprintf('SELECT ' . implode(',', array_filter($columns)) . ', %s AS id FROM %s %s WHERE %s IN (%s)', $full_idcolumn, $table, $otherqueryslim, $full_idcolumn, generateQuestionMarks($ids));
				$result = $adb->pquery($sql, $ids);

				$moduleInfo = Vtiger_Functions::getModuleFieldInfos($module);
				$moduleInfoExtend = [];
				if (count($moduleInfo) > 0) {
					foreach ($moduleInfo as $field => $fieldInfo) {
						$moduleInfoExtend[$fieldInfo['columnname']] = $fieldInfo;
					}
				}

				for ($i = 0; $i < $adb->num_rows($result); $i++) {
					$row = $adb->raw_query_result_rowdata($result, $i);
					$label_name = array();
					$label_search = array();
					foreach ($columns_search as $columnName) {
						if ($moduleInfoExtend && in_array($moduleInfoExtend[$columnName]['uitype'], array(10, 51,73,76, 75, 81))) {
							if ($row[$columnName] > 0) {
								//get module of the related record if exists
								$setype = 'SELECT setype FROM vtiger_crmentity WHERE crmid = ?';
								$setype_result = $adb->pquery($setype, array($row[$columnName]));
								$entityinfo = 'SELECT tablename, fieldname, entityidfield FROM vtiger_entityname WHERE modulename = ?';
								$entityinfo_result = $adb->pquery($entityinfo, array($adb->query_result($setype_result, 0, "setype")));
								$label_query = "Select ".$adb->query_result($entityinfo_result, 0, "fieldname")." from ".$adb->query_result($entityinfo_result, 0, "tablename")." where ".$adb->query_result($entityinfo_result, 0, "entityidfield")." =?";
								$label_result = $adb->pquery($label_query, array($row[$columnName]));
								$label_name[$columnName] = $adb->query_result($label_result, 0, $adb->query_result($entityinfo_result, 0, "fieldname"));
							}
						}
						else {
							$label_search[] = $row[$columnName];
						}
					}
					$entityDisplay[$row['id']] = array('name' => implode(' |', array_filter($label_name)), 'search' => implode(' |', array_filter($label_search)));
				}
			}
			return $entityDisplay;
		}
		$log->debug("Exiting Settings_Search_Handlers_Model::computeCRMRecordLabelsForSearch() method ...");
	}
}