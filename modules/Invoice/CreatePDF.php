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
include_once 'modules/Invoice/InvoicePDFController.php';
// SalesPlatform.ru begin Create SP pdf templates
global $currentModule, $root_directory;
//global $currentModule;
// SalesPlatform.ru end

// SalesPlatform.ru begin Create SP pdf templates
$controllerClassName = "SalesPlatform_" . $currentModule . "PDFController";
$pdfTemplates = new SPPDFTemplates_Module_Model();
$templateId = null;
$availableTemplates = $pdfTemplates->getModuleTemplates($currentModule);
foreach($availableTemplates as $template) {
    if($template->getName() == 'Счет') {
        $templateId = $template->getId();
        break;
    }
}
$controller = new $controllerClassName($currentModule, $templateId);
//$controller = new Vtiger_InvoicePDFController($currentModule);
// SalesPlatform.ru end

$controller->loadRecord(vtlib_purify($_REQUEST['record']));
$invoice_no = getModuleSequenceNumber($currentModule,vtlib_purify($_REQUEST['record']));
$translatedmodname= vtranslate($currentModule,$currentModule);
if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'file') {
	$id = vtlib_purify($_REQUEST['record']);
    // SalesPlatform.ru begin Create SP pdf templates
	$filepath = $root_directory.'/test/product/'.$id.'_'.$translatedmodname.'_'.$invoice_no.'.pdf';
    //$filepath='test/product/'.$id.'_'.$translatedmodname.'_'.$invoice_no.'.pdf';
    // SalesPlatform.ru end
	$controller->Output($filepath,'F'); //added file name to make it work in IE, also forces the download giving the user the option to save
} else {
	$controller->Output($translatedmodname.'_'.$invoice_no.'.pdf', 'D');//added file name to make it work in IE, also forces the download giving the user the option to save
	exit();
}

?>
