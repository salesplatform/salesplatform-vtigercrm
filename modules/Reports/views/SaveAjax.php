<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_SaveAjax_View extends Vtiger_IndexAjax_View {

	public function requiresPermission(\Vtiger_Request $request) {
		$permissions = parent::requiresPermission($request);
		$permissions[] = array('module_parameter' => 'module', 'action' => 'DetailView', 'record_parameter' => 'record');
		return $permissions;
	}
	
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$record = $request->get('record');
		$reportModel = Reports_Record_Model::getInstanceById($record);

		$reportModel->setModule('Reports');

		$reportModel->set('advancedFilter', $request->get('advanced_filter'));

		$page = $request->get('page');
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', Reports_Detail_View::REPORT_LIMIT);
        
        //SalesPlatform.ru begin
        $customReport = null;
        if(AbstractCustomReportModel::isCustomReport($reportModel)) {
            $customReport = AbstractCustomReportModel::getInstance($reportModel);
            $viewTypeDetails = new ViewTypeDetails($request->get('displayType'),
                $request->get('groupBy'),
                $request->get('agregateBy')
            );
            $viewTypeDetails->setCustomControlData($request->get('customControls', array()));
            $customReport->setViewTypeDetails($viewTypeDetails);
        }
        //SalesPlatform.ru end
        
        //SalesPlatform.ru begin 
        try {
        //SalesPlatform.ru end
		if ($mode === 'save') {
			$reportModel->saveAdvancedFilters();
			$reportData = $reportModel->getReportData($pagingModel);
			$data = $reportData['data'];
		} else if ($mode === 'generate') {
			$reportData = $reportModel->generateData($pagingModel);
			$data = $reportData['data'];
		}
		$calculation = $reportModel->generateCalculationData();
        //SalesPlatform.ru begin 
        } catch(Exception $e) {
            if(AbstractCustomReportModel::isCustomReport($reportModel)) {
                $response = new Vtiger_Response();
                $response->setError($e->getMessage());
                $response->emit();
                return;
            }
            
            throw $e;
        }
        //SalesPlatform.ru end
        
        //SalesPlatform.ru begin
        if(AbstractCustomReportModel::isCustomReport($reportModel)) {
            $response = new Vtiger_Response();
            $content = null;
            if($request->get('displayType') == 'table') {
                $viewer->assign('PRIMARY_MODULE', $reportModel->getPrimaryModule());
                $viewer->assign('CALCULATION_FIELDS', $calculation);
                $viewer->assign('DATA', $reportData);
                $viewer->assign('RECORD_ID', $record);
                $viewer->assign('PAGING_MODEL', $pagingModel);
                $viewer->assign('MODULE', $moduleName);
                $viewer->assign('NEW_COUNT',$reportData['count']);
                $viewer->assign('REPORT_RUN_INSTANCE', ReportRun::getInstance($record));
                $viewer->assign('CUSTOM_REPORT', $customReport);
                $content = $viewer->view($customReport->getContentsTpl(), $moduleName, true);
            } else {
                $content = $reportData;
            }
            
            $response->setResult(array('content' => $content));
            $response->emit();
            return;
        }
        //SalesPlatform.ru end 
        
		$viewer->assign('PRIMARY_MODULE', $reportModel->getPrimaryModule());
		$viewer->assign('CALCULATION_FIELDS', $calculation);
		$viewer->assign('DATA', $data);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('NEW_COUNT',$reportData['count']);
		$viewer->assign('REPORT_RUN_INSTANCE', ReportRun::getInstance($record));
		$viewer->assign('REPORT_MODEL', $reportModel);
		$viewer->view('ReportContents.tpl', $moduleName);
	}

	public function validateRequest(Vtiger_Request $request) {
		$request->validateWriteAccess();
	}

}
