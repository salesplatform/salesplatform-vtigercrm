<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'vtlib/Vtiger/PDF/viewers/ContentViewer.php';
include_once 'includes/Aste/Template.php';                                                                                                                                                 
include_once 'includes/Aste/Block.php';
include_once 'includes/Aste/Block/Parser.php';
include_once 'includes/Aste/Exception.php';

class SalesPlatform_PDF_SPContentViewer extends Vtiger_PDF_ContentViewer {

	protected $template;
	
	protected $orientation;
	
	protected $documentModel;

	function __construct($template, $orientation) {
	    $this->template = $template;
	    $this->orientation = $orientation;
	}
	
	function initDisplay($parent) {
	}
	
	function setDocumentModel($model) {
	    $this->documentModel = $model;
	}
        
	//Barcodes insertion 
        function setBarcodes($content, $model, $pdf) {
                    $style1d = array( 
                        'position' => 'S', 
                        'align' => 'C', 
                        'stretch' => false, 
                        'fitwidth' => true, 
                        'cellfitalign' => '', 
                        'border' => true, 
                        'hpadding' => 'auto', 
                        'vpadding' => 'auto', 
                        'fgcolor' => array(0,0,0), 
                        'bgcolor' => array(255,255,255), 
                        'text' => true, 
                        'font' => 'helvetica', 
                        'fontsize' => 8, 
                        'stretchtext' => 4 
                    ); 
                    $style2d = array(
                        'border' => 2,
                        'vpadding' => 'auto',
                        'hpadding' => 'auto',
                        'fgcolor' => array(0,0,0),
                        'bgcolor' => array(255,255,255),
                        'module_width' => 1, 
                        'module_height' => 1
                    );
                    
                    preg_match_all('/{\S(?<tag>.+?)_barcode(?<demension>.+?):(?<standart>.+?)}/', $content, $matches); 
                    $elements = $matches[tag]; 
                    foreach($elements as $key => $tag2barcode) { 
                        if (in_array($tag2barcode, $model->keys())) { 
                            $info4barcode = $model->get($tag2barcode); 
                        } 

                        if ($matches[demension][$key] == '1d'){
                            $param = $pdf->serializeTCPDFtagParameters(array($info4barcode, $matches[standart][$key], '', '', '',  18, 0.4, $style1d, 'N')); 
                        }
                        elseif ($matches[demension][$key] == '2d') {
                            $param = $pdf->serializeTCPDFtagParameters(array($info4barcode, $matches[standart][$key], '', '', '', 20, $style2d, 'N')); 
                        }
                        $content = str_replace($matches[0][$key], '<tcpdf method="write'.strtoupper($matches[demension][$key]).'Barcode" params="'.$param.'" />', $content); 
                        
                    } 
            return $content;
        }
        
	function display($parent) {
            $pdf = $parent->getPDF();
            $pdf->setPageOrientation($this->orientation);
            $pdf->SetAutoPageBreak(true, 10);

            $parent->createPage();
            $contentFrame = $parent->getContentFrame();
            if($this->documentModel) {

                    try {
                        $template = new Aste_Template($this->template);
                        $header = $template->getBlock('content');

                        foreach($this->documentModel->keys() as $key) {
                            $header->setVar($key, $this->documentModel->get($key));
                        }

                        $content = $header->fetch();
                        $content = $this->setBarcodes($content, $this->documentModel, $pdf);
                        $pdf->writeHTMLCell($contentFrame->w, $contentFrame->h,$contentFrame->x, $contentFrame->y, $content);
                    } catch(Aste_Exception $e) {
                    }

                    // Add the border cell at the end
                    // This is required to reset Y position for next write
                    $pdf->MultiCell($contentFrame->w, $contentFrame->h-$contentFrame->y, "", 0, 'L', 0, 1, $contentFrame->x, $contentFrame->y);
            }
	}

}