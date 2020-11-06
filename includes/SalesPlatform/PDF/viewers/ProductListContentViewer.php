<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'includes/SalesPlatform/PDF/viewers/SPContentViewer.php';

class SalesPlatform_PDF_ProductListDocumentContentViewer extends SalesPlatform_PDF_SPContentViewer {

	function display($parent) {

		$models = $this->contentModels;

		$totalModels = count($models);
                $totalGoods = 0;
                $totalServices = 0;
		$pdf = $parent->getPDF();
		$pdf->setPageOrientation($this->orientation);
        //SalesPlatform.ru begin
		//$pdf->SetAutoPageBreak(true, 10);
		//SalesPlatform.ru end
		$parent->createPage();
		$contentFrame = $parent->getContentFrame();
		
		try {
           
		    $template = new Aste_Template($this->template);

		    $table_head = $template->getBlock('table_head');
		    $content = $table_head->fetch();

		    for ($index = 0; $index < $totalModels; ++$index) {
			$model = $models[$index];
			
			$contentHeight = 1;

			try {
                            $table_row = $template->getBlock('table_row', true);
                    	    foreach($this->documentModel->keys() as $key) {
                        	$table_row->setVar($key, $this->documentModel->get($key));
                    	    }
                            foreach($model->keys() as $key) {
                                $table_row->setVar($key, $model->get($key));
                            }
                            $content .= $table_row->fetch();
                        }catch(Aste_Exception $e) {
                        }

                        try {
                            if($model->get('entityType') == 'Products') {
                                $table_row = $template->getBlock('goods_row', true);
                    		foreach($this->documentModel->keys() as $key) {
                        	    $table_row->setVar($key, $this->documentModel->get($key));
                    		}
                                foreach($model->keys() as $key) {
                                    $table_row->setVar($key, $model->get($key));
                                }
                                $content .= $table_row->fetch();
                                $totalGoods++;
                            }
                        }catch(Aste_Exception $e) {
                        }
			
                        try {
                            if($model->get('entityType') == 'Services') {
                                $table_row = $template->getBlock('services_row', true);
                    		foreach($this->documentModel->keys() as $key) {
                        	    $table_row->setVar($key, $this->documentModel->get($key));
                    		}
                                foreach($model->keys() as $key) {
                                    $table_row->setVar($key, $model->get($key));
                                }
                                $content .= $table_row->fetch();
                                $totalServices++;
                            }
                        }catch(Aste_Exception $e) {
                        }
		    }
		
		
		    $summary = $template->getBlock('summary');
                    foreach($this->documentModel->keys() as $key) {
                        $summary->setVar($key, $this->documentModel->get($key));
                    }
		    foreach($this->contentSummaryModel->keys() as $key) {
    		        $summary->setVar($key, $this->contentSummaryModel->get($key));
    		    }
    		    $summary->setVar('summaryTotalItems', $totalModels);
    		    $summary->setVar('summaryTotalGoods', $totalGoods);
    		    $summary->setVar('summaryTotalServices', $totalServices);
		    $content .= $summary->fetch();

		    $ending = $template->getBlock('ending');
		    foreach($this->documentModel->keys() as $key) {
    		        $ending->setVar($key, $this->documentModel->get($key));
    		    }
		    $content .= $ending->fetch();
                    
                    //Salesplatform.ru begin Barcodes insertion 
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
                        if ($tag2barcode == 'summaryTotalItems') { 
                            $info4barcode = $totalModels; 
                        } 
                        elseif ($tag2barcode == 'summaryTotalGoods') { 
                            $info4barcode = $totalGoods; 
                        } 
                        elseif ($tag2barcode == 'summaryTotalServices') { 
                            $info4barcode = $totalServices; 
                        } 
                        elseif (in_array($tag2barcode, $this->documentModel->keys())) { 
                            $info4barcode = $this->documentModel->get($tag2barcode); 
                        } 
                        elseif (in_array($tag2barcode, $this->contentSummaryModel->keys())) { 
                            $info4barcode = $this->contentSummaryModel->get($tag2barcode); 
                        }
                        
                        if ($matches[demension][$key] == '1d'){
                            $param = $pdf->serializeTCPDFtagParameters(array($info4barcode, $matches[standart][$key], '', '', '',  18, 0.4, $style1d, 'N')); 
                        }
                        elseif ($matches[demension][$key] == '2d') {
                            $param = $pdf->serializeTCPDFtagParameters(array($info4barcode, $matches[standart][$key], '', '', '', 20, $style2d, 'N')); 
                        }
                        $content = str_replace($matches[0][$key], '<tcpdf method="write'.strtoupper($matches[demension][$key]).'Barcode" params="'.$param.'" />', $content); 
                        
                    } 
                    //Salesplatform.ru end Barcodes insertion
                    
		    $pdf->writeHTMLCell(0, 0, $contentFrame->x, $contentFrame->y, $content);
		} catch(Aste_Exception $e) {
		}

	}

}