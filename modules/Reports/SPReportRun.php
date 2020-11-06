<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/Reports/ReportRun.php';

abstract class AbstractCustomReportModel {
    
    /**
     * Reports models cache
     * @var Reports_Record_Model 
     */
    private static $reportsInstancies = array();
    
    private $runtimeSqlFilter;
    
    /**
     *
     * @var ViewTypeDetails 
     */
    protected $viewTypeDetails;
    
    /**
     * Current report
     * @var Reports_Record_Model
     */
    protected $vtigerReportModel;
    
    /**
     * Primare report modules fields structure in vtiger format
     * @var array 
     */
    protected $primaryModuleRecordStructure;
    
    public function __construct($reportModel) {
        $this->viewTypeDetails = new ViewTypeDetails($this->getDefaultViewType());
        $this->vtigerReportModel = $reportModel;
        $this->beforeStartInitializate();
        
        $this->checkFilter();
        $this->initilizatePrimaryModuleRecordStructure();
        $this->initializateVirtualRecordStructure();
    }
    
    
    /**
     * Generates sql condition based on selected filters
     * @param type $advanceFilter
     * @return string
     */
    public static function generateAdvSql($advanceFilter) {
        $reportRecordModel = new Reports_Record_Model();
        $reportRecordModel->set('advancedFilter', $advanceFilter);
        $filterSQL = $reportRecordModel->getAdvancedFilterSQL();
        
        return $filterSQL;
    }
    
    /**
     * Returns report data
     */
    public final function getData($outputFormat = 'PDF') {
        $output = array();
        $data = $this->getCalculationData($outputFormat);
        if($this->getViewTypeName() === Reports_CustomReportTypes_Model::TABLE) {
            $compozedData = array();
            $labels = $this->getLabels($outputFormat);
            foreach($data as $rowIndex => $row) {
                $labeledRow = array();
                foreach($row as $rowIndex => $columnValue) {
                    $labeledRow[$labels[$rowIndex]] = $columnValue;
                } 
                
                array_push($compozedData, $labeledRow);
            }
            $output['data'] = $compozedData;
        } else {
            $output = $data;
        }

        return $output;
    }
    
    
    protected abstract function getCalculationData($outputFormat = 'PDF');
    
    protected abstract function getLabels($outputFormat = 'PDF');
    
    /**
     * Returns rows count of report
     * @return int
     */
    public function getCount() {
        $result = $this->getData();
        $count = 0;
        if(array_key_exists('data', $result)) {
            $count = count($result['data']);
        }
        
        return $count;
    }
    
    /**
     * Summary calculations data which need to display
     * 
     * @return array
     */
    public function getReportCalulationData() {
        return array();
    }
    
    /**
     * Name of javascript function, which controls ui actions
     * @return string
     */
    public function getCustomUIControllerName() {
        return 'Custom_Reports_Conroller';
    }
    
    public function getContentsTpl() {
        return 'sp_custom_reports/CustomReportContents.tpl';
    }
    
    public function getHeaderTpl() {
        return 'sp_custom_reports/CustomReportHeader.tpl';
    }
    
    /**
     * Returns data which need to print on same action
     * @return string
     */
    public function getPrintData() {
        $result = $this->getData();

        $viewer = new Vtiger_Viewer();
        $viewer->assign('COLUMN_NAMES', array_keys(reset($result['data'])));
        $viewer->assign('DATA', $result['data']);
        $viewer->assign('CUSTOM_REPORT', $this);
        
        $printOutput = array();
        $printOutput[] = $viewer->view('sp_custom_reports/SPCustomReportDefaultPrint.tpl', $this->vtigerReportModel->getModuleName(), true);
        $printOutput[] = $this->getCount();
        return $printOutput;
    }
    
    /**
     * Totals summary on print mode
     * @return string
     */
    public function getTotalPrintData() {
        return '';
    }
    
    /**
     * If report has last column as link to crm entity - return true. This means
     * that lats column will be ignored in print, csv, excel import
     * @return boolean
     */
    public function hasLastLinkColumn() {
        return false;
    }
    
    /**
     * Return true if available in ui to add filters
     * @return boolean
     */
    public function canAddFilters() {
        return true;
    }
    
    /**
     * Set view plot type of report
     * @param ViewTypeDetails $viewTypeDetails
     */
    public function setViewTypeDetails($viewTypeDetails) {
        $this->viewTypeDetails = $viewTypeDetails;
    }
    
    /**
     * Returns current view plot type name
     * @return string
     */
    public function getViewTypeName() {
        return $this->viewTypeDetails->getName();
    }
    
    /**
     * Return all information about plot type - which fields aggregate, group, plot by
     * @return ViewTypeDetails
     */
    public function getViewTypeDetails() {
        return $this->viewTypeDetails;
    }
    
    /**
     * Return primary module records tructure in vtiger format
     * @return array
     */
    public function getPrimaryModuleRecordStructure() {
        return $this->primaryModuleRecordStructure;
    }
    
    /**
     * Comparators which available for filters
     * 
     * @return array
     */
    public function getFiltersComparatorsRules() {
        return  array(
            'V' => array('e','n','s','ew','c','k','y','ny'),
			'N' => array('e','n','l','g','m','h', 'y','ny'),
			'T' => array('e','n','l','g','m','h','bw','b','a','y','ny'),
			'I' => array('e','n','l','g','m','h','y','ny'),
			'C' => array('e','n','y','ny'),
			'D' => array('e','n','bw','b','a','y','ny'),
			'DT' => array('e','n','bw','b','a','y','ny'),
			'NN' => array('e','n','l','g','m','h','y','ny'),
			'E' => array('e','n','s','ew','c','k','y','ny')
        );
    }
    
    /**
     * Additional information which will be serialized in JSON and transmissed to ui.
     * Needs to additional controls in UI
     * @return array
     */
    public function getCustomReportControlData() {
        return array();
    }
    
    /**
     * Returns available charts plot types
     * @return array
     */
    public function getChartsViewControlData() {
        return array();
    }
    
    /**
     * Additional fields whic may be needed to custom logic
     * @return Vtiger_Field_Model[]
     */
    public function getCustomControlFields() {
        return array();
    }
    
    public function getJsScripts() {
        return array(
            '~/libraries/jquery/jqplot/jquery.jqplot.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.barRenderer.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.pointLabels.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.highlighter.min.js',
            '~/libraries/jquery/jqplot/plugins/jqplot.pieRenderer.min.js',
            'modules.Vtiger.resources.dashboards.Widget',
            'modules.Reports.resources.SPCustomReports'
        );
    }
    
    public function getCssScripts() {
        return array(
			'~/libraries/jquery/jqplot/jquery.jqplot.min.css',
		);
    }
    
    public function setRunTimeSqlFilter($filterSql) {
        $this->runtimeSqlFilter = $filterSql;
    }
    
    /**
     * Return concrece instance of custom report model implementation
     * 
     * @param Reports_Record_Model $reportModel
     * @return AbstractCustomReportModel
     */
    public static function getInstance($reportModel) {
        $customReportClass = $reportModel->getReportType();
        if(!array_key_exists($customReportClass, self::$reportsInstancies)) {
            require_once "modules/Reports/sp_custom_reports/" . $customReportClass . ".inc.php";
            self::$reportsInstancies[$customReportClass] = new $customReportClass($reportModel);
        }
                
        return self::$reportsInstancies[$customReportClass];
    }
    
    public static function delete($reportModel) {
        $db = PearDatabase::getInstance();
        $db->pquery("DELETE FROM sp_custom_reports WHERE reporttype=?", array($reportModel->getReportType()));
    }
    
    /**
     * Return true if report is custom
     * 
     * @param Reports_Record_Model $reportModel
     * @return boolean
     */
    public static function isCustomReport($reportModel) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT count(*) AS reports_count FROM sp_custom_reports WHERE reporttype=?", array($reportModel->getReportType()));
        $resultRow = $db->fetchByAssoc($result);
        return ($resultRow['reports_count'] != 0);
    }
    
    /**
     * List of filters, which cant't be removed from ui
     * @return array
     */
    public function getBlockedFiltersNames() {
        return array();
    }
    
    /**
     * Default view plot type
     * @return string
     */
    protected function getDefaultViewType() {
        return Reports_CustomReportTypes_Model::TABLE;
    }
    
    /**
     * Some actions, which need to execute before report calculations will be started
     */
    protected function beforeStartInitializate() {}
    
    /**
     * Returns conditions of report which must all match for selecton
     * 
     * @return type
     */
    protected function getFiltersConditions() {
        $filterConditions = $this->vtigerReportModel->get('advancedFilter');

        /* Only and conditions */
        return $filterConditions;
    }
    
    protected function setFilterCondition($conditionIndex, $conditionBody) {
        $filterConditions = $this->vtigerReportModel->get('advancedFilter');
        $filterConditions[$conditionIndex] = $conditionBody;
        $this->vtigerReportModel->set('advancedFilter', $filterConditions);
    }
    
    protected final function getFilterSql() {
        $sqlFilter = $this->runtimeSqlFilter;
        if($sqlFilter == null) {
            $sqlFilter = $this->vtigerReportModel->getAdvancedFilterSQL();
        }
        return $sqlFilter;
    }
    
    protected function checkFilter() {
        $filterConditions = $this->vtigerReportModel->get('advancedFilter');
        if($filterConditions == null) {
            $filterConditions = $this->vtigerReportModel->transformToNewAdvancedFilter();
            $this->vtigerReportModel->set('advancedFilter', $filterConditions);
        }
    }
    
    /**
     * ini$reportModeltilizates base module record structure for fileters
     */
    protected function initilizatePrimaryModuleRecordStructure() {
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($this->vtigerReportModel);
        $this->primaryModuleRecordStructure = $recordStructureInstance->getPrimaryModuleRecordStructure();
    }
    
    protected function initializateVirtualRecordStructure() {
        foreach($this->getVirtualFiltersFields() as $field) {
            $this->primaryModuleRecordStructure['VIRTUAL'][$field->getName()] = $field; 
        }
    }
    
    /**
     * Return fields list, which are not real for module structire but need to be as filters.
     * This is virtual filetr and you need to custom handle of its values 
     * 
     * @see VirtualPicklistField
     * @return Vtiger_Field_Model[]
     */
    protected function getVirtualFiltersFields() {
        return array();
    }
}

class ViewTypeDetails {
    
    private $name;
    private $groupField;
    private $agregateField;
    private $customControlData;
    
    public function __construct($name, $groupField = null, $agregateFields = null) {
        $this->name = $name;
        $this->groupField = $groupField;
        $this->agregateField = $agregateFields;
    }
    
    function getName() {
        return $this->name;
    }

    function getGroupField() {
        return $this->groupField;
    }

    function getAgregateFields() {
        return $this->agregateField;
    }

    function getCustomControlData() {
        return $this->customControlData;
    }

    function setCustomControlData($customControlData) {
        $this->customControlData = $customControlData;
    }


}

class VirtualPicklistField extends Vtiger_Field_Model {
    
    private $picklistValues = array();
    
    public function setPicklistValues($values) {
        $this->picklistValues = $values;
    }
    
    public function getPicklistValues() {
       return $this->picklistValues;
    }
}

?>