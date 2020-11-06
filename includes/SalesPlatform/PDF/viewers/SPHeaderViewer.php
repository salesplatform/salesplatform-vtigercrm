<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/PDF/viewers/HeaderViewer.php';
include_once 'includes/Aste/Template.php';                                                                                                                                                 
include_once 'includes/Aste/Block.php';
include_once 'includes/Aste/Block/Parser.php';
include_once 'includes/Aste/Exception.php';
include_once 'includes/SalesPlatform/PDF/viewers/SPContentViewer.php';

class SalesPlatform_PDF_SPHeaderViewer extends Vtiger_PDF_HeaderViewer {

	protected $template;
	protected $height;

	function __construct($template, $height) {
	    $this->template = $template;
	    $this->height = $height;
	}

	function totalHeight($parent) {
		return $this->height;
	}
	
	function display($parent) {
		$pdf = $parent->getPDF();
		$headerFrame = $parent->getHeaderFrame();
		if($this->model) {

			try {
			    $template = new Aste_Template($this->template);
			    $header = $template->getBlock('header');
			    
    			    foreach($this->model->keys() as $key) {
    				$header->setVar($key, $this->model->get($key));
    			    }
    			    
			    $content = $header->fetch();
                            $content = SalesPlatform_PDF_SPContentViewer::setBarcodes($content, $this->model, $pdf);
			    $pdf->writeHTMLCell($headerFrame->w, $headerFrame->h,$headerFrame->x, $headerFrame->y, $content);
			} catch(Aste_Exception $e) {
			}

			// Add the border cell at the end
			// This is required to reset Y position for next write
			$pdf->MultiCell($headerFrame->w, $headerFrame->h-$headerFrame->y, "", 0, 'L', 0, 1, $headerFrame->x, $headerFrame->y);
		}	
		
	}
	
}