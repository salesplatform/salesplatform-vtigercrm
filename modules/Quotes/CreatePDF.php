<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
// SalesPlatform.ru begin Create SP pdf templates
include_once 'modules/Quotes/QuotesPDFController.php';
//include_once 'modules/Quotes/QuotePDFController.php';
global $currentModule, $root_directory;

$controllerClassName = "SalesPlatform_" . $currentModule . "PDFController";
$pdfTemplates = new SPPDFTemplates_Module_Model();
$templateId = null;
$availableTemplates = $pdfTemplates->getModuleTemplates($currentModule);
foreach($availableTemplates as $template) {
    if($template->getName() == 'Предложение') {
        $templateId = $template->getId();
        break;
    }
}
$translatedModuleName = vtranslate($currentModule,$currentModule);
$controller = new $controllerClassName($currentModule, $templateId);
//$controller = new Vtiger_QuotePDFController($currentModule);
// SalesPlatform.ru end

$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$quote_no = getModuleSequenceNumber($currentModule,vtlib_purify($_REQUEST['record']));
if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$quote_id = vtlib_purify($_REQUEST['record']);
    // SalesPlatform.ru begin Create SP pdf templates
	$filepath = $root_directory.'/test/product/'.$quote_id.'_'.$translatedModuleName.'_'.$quote_no.'.pdf';
    //$filepath='test/product/'.$quote_id.'_Quotes_'.$quote_no.'.pdf';
    // SalesPlatform.ru end
	//added file name to make it work in IE, also forces the download giving the user the option to save
	$controller->Output($filepath,'F');
} else {
	//added file name to make it work in IE, also forces the download giving the user the option to save
    // SalesPlatform.ru begin Create SP pdf templates
	$controller->Output($translatedModuleName.'_'.$quote_no.'.pdf', 'D');
    //$controller->Output('Quotes_'.$quote_no.'.pdf', 'D');
    // SalesPlatform.ru end
	exit();
}

?>
