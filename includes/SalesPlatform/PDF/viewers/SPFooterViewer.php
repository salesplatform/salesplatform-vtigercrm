<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/PDF/viewers/FooterViewer.php';
include_once 'includes/Aste/Template.php';                                                                                                                                                 
include_once 'includes/Aste/Block.php';
include_once 'includes/Aste/Block/Parser.php';
include_once 'includes/Aste/Exception.php';

class SalesPlatform_PDF_SPFooterViewer extends Vtiger_PDF_FooterViewer {

	protected $template;
	protected $height;

	function __construct($template, $height) {
	    $this->template = $template;
	    $this->height = $height;
	}

	function totalHeight($parent) {
		return $this->height;
	}
    //SalesPlatform.ru begin 
    public function getContent() {
        $content = '';
        if($this->model) {		
			try {
                $template = new Aste_Template($this->template);
			    $footer = $template->getBlock('footer');
                //SalesPlatform.ru begin 
                foreach($this->model->keys() as $key) {
    				 $footer->setVar($key, $this->model->get($key));
    			}
                //SalesPlatform.ru end
                $content = $footer->fetch();
			} catch(Aste_Exception $e) {
			}
		}       
        return $content;
    }
    //SalesPlatform.ru end
	function display($parent) {

		$pdf = $parent->getPDF();
		$footerFrame = $parent->getFooterFrame();

		if($this->model) {
		
			try {
    			    $template = new Aste_Template($this->template);
			    $footer = $template->getBlock('footer');
                //SalesPlatform.ru begin 
                foreach($this->model->keys() as $key) {
    				 $footer->setVar($key, $this->model->get($key));
    			}
                
    			    $content = $footer->fetch();
                //$pdf->writeHTMLCell($footerFrame->w, $footerFrame->h,$footerFrame->x, $footerFrame->y, $content);
			} catch(Aste_Exception $e) {
			}
            //$pdf->MultiCell($footerFrame->w, $footerFrame->h-$footerFrame->y, "", 0, 'L', 0, 1, $footerFrame->x, $footerFrame->y);
            //SalesPlatform.ru end
		}	

	}
}
?>