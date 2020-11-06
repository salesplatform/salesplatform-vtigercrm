<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'includes/SalesPlatform/PDF/SPPDFController.php';
include_once 'vtlib/Vtiger/PDF/models/Model.php';
include_once 'includes/SalesPlatform/PDF/viewers/ProductListHeaderViewer.php';
include_once 'includes/SalesPlatform/PDF/viewers/ProductListFooterViewer.php';
include_once 'includes/SalesPlatform/PDF/viewers/ProductListContentViewer.php';
include_once 'vtlib/Vtiger/PDF/PDFGenerator.php';
include_once 'data/CRMEntity.php';

class SalesPlatform_PDF_ProductListDocumentPDFController extends
        SalesPlatform_PDF_SPPDFController {

        function loadRecord($id) {
                parent::loadRecord($id);
		$this->associated_products = getAssociatedProducts($this->moduleName,$this->focus);
	}
        
	function getContentViewer() {
		$contentViewer = new SalesPlatform_PDF_ProductListDocumentContentViewer($this->template, $this->pageOrientation);
		$contentViewer->setDocumentModel($this->buildDocumentModel());
		$contentViewer->setContentModels($this->buildContentModels());
		$contentViewer->setSummaryModel($this->buildSummaryModel());
		$contentViewer->setLabelModel($this->buildContentLabelModel());
		$contentViewer->setWatermarkModel($this->buildWatermarkModel());
		return $contentViewer;
	}

	function getHeaderViewer() {
		$headerViewer = new SalesPlatform_PDF_ProductListDocumentHeaderViewer($this->template, $this->headerSize);
		$headerViewer->setModel($this->buildDocumentModel());
		return $headerViewer;
	}

	function getFooterViewer() {
		$footerViewer = new SalesPlatform_PDF_ProductListDocumentFooterViewer($this->template, $this->footerSize);
        //SalesPlatform.ru begin 
		$footerViewer->setModel($this->buildDocumentModel());
        //$footerViewer->setModel($this->buildFooterModel());
        //SalesPlatform.ru begin end
		$footerViewer->setLabelModel($this->buildFooterLabelModel());
		$footerViewer->setOnLastPage();
                
		return $footerViewer;
	}

	function Output($filename, $type) {
            parent::Output($filename, $type);
	}


	// Helper methods
	
	function buildContentModels() {
		$associated_products = $this->associated_products;
                $final_details = $associated_products[1]['final_details'];
                $contentModels = array();
		$productLineItemIndex = 0;
		$totaltaxes = 0;
		$totaltaxesGoods = 0;
		$totaltaxesServices = 0;
                $goodsNumber = 0;
                $serviceNumber = 0;
		foreach($associated_products as $productLineItem) {
			++$productLineItemIndex;

			$contentModel = new Vtiger_PDF_Model();

			$discountPercentage  = 0.00;
			$total_tax_percent = 0.00;
			$producttotal_taxes = 0.00;
			$quantity = ''; $listPrice = ''; $discount = ''; $taxable_total = '';
			$tax_amount = ''; $producttotal = '';


			$quantity	= $productLineItem["qty{$productLineItemIndex}"];
			$usageunit	= $productLineItem["usageunit{$productLineItemIndex}"];

                        if($usageunit =='') {
                            $usageunit = "-";
                        }

			$listPrice	= $productLineItem["listPrice{$productLineItemIndex}"];
			$discount	= $productLineItem["discountTotal{$productLineItemIndex}"];
			$taxable_total = $quantity * $listPrice - $discount;
			if($discount > 0 && $quantity > 0) {
			    $priceWithDiscount = $listPrice - $discount / $quantity;
			} else {
			    $priceWithDiscount = $listPrice;
			}
			$producttotal = $taxable_total;
			$priceWithTax = $priceWithDiscount;
			if($this->focus->column_fields["hdnTaxType"] == "individual") {
				for($tax_count=0;$tax_count<count($productLineItem['taxes']);$tax_count++) {
					$tax_percent = $productLineItem['taxes'][$tax_count]['percentage'];
					$total_tax_percent += $tax_percent;
                                        // SalesPlatform.ru begin Fixed problem with rounding
					$tax_amount = ((round($taxable_total,2)*$tax_percent)/100);
					$producttotal_taxes += round($tax_amount,2);  
                                        //$tax_amount = (($taxable_total*$tax_percent)/100);
                                        //$producttotal_taxes += $tax_amount;
                                        // SalesPlatform.ru end
					$priceWithTax += (($priceWithDiscount * $tax_percent)/100);
				}
                        } else {
                            // Recalculate tax when group mode is enabled
                            $group_tax_details = $final_details['taxes'];
                            $group_total_tax_percent = '0.00';
                            for($i=0;$i<count($group_tax_details);$i++) {
                                    $group_total_tax_percent += $group_tax_details[$i]['percentage'];
                            }
                            $total_tax_percent += $group_total_tax_percent;
                            if($this->focus->column_fields["hdnTaxType"] == "group_tax_inc") {
                                $tax_amount = $taxable_total*$group_total_tax_percent/(100.0+$group_total_tax_percent);
                                $priceWithDiscount -= $priceWithTax*$group_total_tax_percent/(100.0+$group_total_tax_percent);
                            } else {
                                $tax_amount = (($taxable_total*$group_total_tax_percent)/100);
                                $priceWithTax += (($priceWithDiscount * $group_total_tax_percent)/100);
                            }
                            $producttotal_taxes += $tax_amount;
                        }
			if($this->focus->column_fields["hdnTaxType"] != "group_tax_inc") {
                            $producttotal = $taxable_total+$producttotal_taxes;
                        } else {
                            $taxable_total -= $producttotal_taxes;
                        }
			$tax = $producttotal_taxes;
			$totaltaxes += $tax;
			$discountPercentage = $productLineItem["discount_percent{$productLineItemIndex}"];
			$productName = decode_html($productLineItem["productName{$productLineItemIndex}"]);
			//get the sub product
            $subProducts = $productLineItem["subProductArray{$productLineItemIndex}"];
            if($subProducts != ''){
				foreach($subProducts as $subProduct) {
					$productName .="\n"." - ".decode_html($subProduct);
                    }
			}
			$contentModel->set('entityType', $productLineItem["entityType{$productLineItemIndex}"]);
                        if($productLineItem["entityType{$productLineItemIndex}"] == 'Products') {
                            ++$goodsNumber;
                            $contentModel->set('goodsNumber', $goodsNumber);
                            $totaltaxesGoods += $tax;
                        }
                        else if($productLineItem["entityType{$productLineItemIndex}"] == 'Services') {
                            ++$serviceNumber;
                            $contentModel->set('serviceNumber', $serviceNumber);
                            $totaltaxesServices += $tax;
                        }

        		$contentModel->set('productNumber', $productLineItemIndex);
            		$contentModel->set('productName', $productName);
			$contentModel->set('productCode', $productLineItem["hdnProductcode{$productLineItemIndex}"]);
			$contentModel->set('productQuantity', $this->formatNumber($quantity, 3));
			$contentModel->set('productQuantityInt', $this->formatNumber($quantity, 0));
			$contentModel->set('productUnits', getTranslatedString($usageunit, 'Products'));
			$contentModel->set('productUnitsCode', $productLineItem["unitCode{$productLineItemIndex}"]);
			$contentModel->set('productPrice',     $this->formatPrice($priceWithDiscount));
			$contentModel->set('productPriceWithTax', $this->formatPrice($priceWithTax));
			$contentModel->set('productDiscount',  $this->formatPrice($discount)."\n ($discountPercentage%)");
			$contentModel->set('productNetTotal',  $this->formatPrice($taxable_total));
			$contentModel->set('productTax',       $this->formatPrice($tax));
			$contentModel->set('productTaxPercent', $total_tax_percent);
			$contentModel->set('productTotal',     $this->formatPrice($producttotal));
			$contentModel->set('productDescription',   nl2br($productLineItem["productDescription{$productLineItemIndex}"]));
			$contentModel->set('productComment',   nl2br($productLineItem["comment{$productLineItemIndex}"]));
			$contentModel->set('manufCountry', $productLineItem["manufCountry{$productLineItemIndex}"]);
			$contentModel->set('manufCountryCode', $productLineItem["manufCountryCode{$productLineItemIndex}"]);
			$contentModel->set('customsId', $productLineItem["customsId{$productLineItemIndex}"]);
            //SalesPlatform.ru begin
            $contentModel->set('internatonalCode', $productLineItem["internatonalCode{$productLineItemIndex}"]);
            //SalesPlatform.ru end
                        $productImagePath = $productLineItem["attachmentId{$productLineItemIndex}"] > 0 ? $productLineItem["attachmentPath{$productLineItemIndex}"].$productLineItem["attachmentId{$productLineItemIndex}"].'_'.$productLineItem["attachmentName{$productLineItemIndex}"] : false;
                        $contentModel->set('productImage', $productImagePath ? '<img src="'.$productImagePath.'" />' : '');
                        $contentModel->set('productImagePath', $productImagePath ? $productImagePath : 'themes/images/blank.gif');

			$contentModels[] = $contentModel;
		}
		$this->totaltaxes = $totaltaxes; //will be used to add it to the net total
		$this->totaltaxesGoods = $totaltaxesGoods;
		$this->totaltaxesServices = $totaltaxesServices;

		return $contentModels;
	}


	function buildSummaryModel() {

                if(isset($this->focus->column_fields['currency_id'])) {
                    $currencyInfo = getCurrencyInfo($this->focus->column_fields['currency_id']);
                    $currency = $currencyInfo['code'];
                } else {
                    $currency = 'RUB';
                }

		$associated_products = $this->associated_products;
		$final_details = $associated_products[1]['final_details'];

		$summaryModel = new Vtiger_PDF_Model();

		$netTotal = $netTotalGoods = $netTotalServices = $discount = $handlingCharges =  $handlingTaxes = 0;
		$adjustment = $grandTotal = 0;

		$productLineItemIndex = 0;
		$sh_tax_percent = 0;
		foreach($associated_products as $productLineItem) {
			++$productLineItemIndex;
			$netTotal += $productLineItem["netPrice{$productLineItemIndex}"];

                        if($productLineItem["entityType{$productLineItemIndex}"] == 'Products') {
                            $netTotalGoods += $productLineItem["netPrice{$productLineItemIndex}"];
                        }
                        if($productLineItem["entityType{$productLineItemIndex}"] == 'Services') {
                            $netTotalServices += $productLineItem["netPrice{$productLineItemIndex}"];
                        }
		}

		$summaryModel->set("summaryNetTotal", $this->formatPrice($netTotal));
		$summaryModel->set("summaryNetTotalGoods", $this->formatPrice($netTotalGoods));
		$summaryModel->set("summaryNetTotalServices", $this->formatPrice($netTotalServices));
		
		$discount_amount = $final_details["discount_amount_final"];
		$discount_percent = $final_details["discount_percentage_final"];

		$discount = 0.0;
		$discountGoods = 0.0;
		$discountServices = 0.0;
		if(!empty($discount_amount)) {
			$discount = $discount_amount;
			$discountGoods = $discount_amount;
			$discountServices = $discount_amount;
		}
		if(!empty($discount_percent)) {
			$discount = (($discount_percent*$final_details["hdnSubTotal"])/100);
			$discountGoods = (($discount_percent*$netTotalGoods)/100);
			$discountServices = (($discount_percent*$netTotalServices)/100);
		}
		$summaryModel->set("summaryDiscount", $this->formatPrice($discount));
		$summaryModel->set("summaryDiscountGoods", $this->formatPrice($discountGoods));
		$summaryModel->set("summaryDiscountServices", $this->formatPrice($discountServices));
		
		$group_total_tax_percent = '0.00';
                $overall_tax = 0;
                $overall_tax_goods = 0;
                $overall_tax_services = 0;
		//To calculate the group tax amount
		if($final_details['taxtype'] == 'group') {
			$group_tax_details = $final_details['taxes'];
			foreach($group_tax_details as $taxDetail) {
				$group_total_tax_percent += $taxDetail['percentage'];
			}
			$summaryModel->set("summaryTax", $this->formatPrice($final_details['tax_totalamount']));
			$summaryModel->set("summaryTaxLiteral", $this->num2str($final_details['tax_totalamount'], false, $currency));
			$summaryModel->set("summaryTaxPercent", $this->formatPrice($group_total_tax_percent));
                        $overall_tax += $final_details['tax_totalamount'];

                        $summaryModel->set("summaryTaxGoods", $this->formatPrice(($netTotalGoods - $discountGoods) * $group_total_tax_percent / 100.0));
			$summaryModel->set("summaryTaxGoodsLiteral", $this->num2str(($netTotalGoods - $discountGoods) * $group_total_tax_percent / 100.0, false, $currency));
			$summaryModel->set("summaryTaxGoodsPercent", $group_total_tax_percent);
                        $overall_tax_goods += ($netTotalGoods - $discountGoods) * $group_total_tax_percent / 100.0;

                        $summaryModel->set("summaryTaxServices", $this->formatPrice(($netTotalServices - $discountServices) * $group_total_tax_percent / 100.0));
			$summaryModel->set("summaryTaxServicesLiteral", $this->num2str(($netTotalServices - $discountServices) * $group_total_tax_percent / 100.0, false, $currency));
			$summaryModel->set("summaryTaxServicesPercent", $group_total_tax_percent);
                        $overall_tax_services += ($netTotalServices - $discountServices) * $group_total_tax_percent / 100.0;
		}
		else if($final_details['taxtype'] == 'group_tax_inc') {
			$group_tax_details = $final_details['taxes'];
			foreach($group_tax_details as $taxDetail) {
				$group_total_tax_percent += $taxDetail['percentage'];
			}
			$summaryModel->set("summaryTax", $this->formatPrice($final_details['tax_totalamount']));
			$summaryModel->set("summaryTaxLiteral", $this->num2str($final_details['tax_totalamount'], false, $currency));
			$summaryModel->set("summaryTaxPercent", $this->formatPrice($group_total_tax_percent));
                        $overall_tax += $final_details['tax_totalamount'];
                        $summaryModel->set("summaryNetTotal", $this->formatPrice($netTotal - $final_details['tax_totalamount']));

                        $summaryModel->set("summaryTaxGoods", $this->formatPrice(($netTotalGoods - $discountGoods) * $group_total_tax_percent / (100.0 + $group_total_tax_percent)));
			$summaryModel->set("summaryTaxGoodsLiteral", $this->num2str(($netTotalGoods - $discountGoods) * $group_total_tax_percent / (100.0 + $group_total_tax_percent), false, $currency));
			$summaryModel->set("summaryTaxGoodsPercent", $group_total_tax_percent);
                        $overall_tax_goods += ($netTotalGoods - $discountGoods) * $group_total_tax_percent / (100.0 + $group_total_tax_percent);
                        $summaryModel->set("summaryNetTotalGoods", $this->formatPrice($netTotalGoods - ($netTotalGoods - $discountGoods) * $group_total_tax_percent / (100.0 + $group_total_tax_percent)));

                        $summaryModel->set("summaryTaxServices", $this->formatPrice(($netTotalServices - $discountServices) * $group_total_tax_percent / (100.0 + $group_total_tax_percent)));
			$summaryModel->set("summaryTaxServicesLiteral", $this->num2str(($netTotalServices - $discountServices) * $group_total_tax_percent / (100.0 + $group_total_tax_percent), false, $currency));
			$summaryModel->set("summaryTaxServicesPercent", $group_total_tax_percent);
                        $overall_tax_services += ($netTotalServices - $discountServices) * $group_total_tax_percent / (100.0 + $group_total_tax_percent);
                        $summaryModel->set("summaryNetTotalServices", $this->formatPrice($netTotalServices - ($netTotalServices - $discountServices) * $group_total_tax_percent / (100.0 + $group_total_tax_percent)));
		}
		else {
		    $summaryModel->set("summaryTax", $this->formatPrice($this->totaltaxes));
    		    $summaryModel->set("summaryTaxLiteral", $this->num2str($this->totaltaxes, false, $currency));
		    if($netTotal > 0) {
			$summaryModel->set("summaryTaxPercent", $this->formatPrice($this->totaltaxes / $netTotal * 100));
		    }
		    else {
			$summaryModel->set("summaryTaxPercent", 0);
		    }
                    $overall_tax += $this->totaltaxes;

		    $summaryModel->set("summaryTaxGoods", $this->formatPrice($this->totaltaxesGoods));
    		    $summaryModel->set("summaryTaxGoodsLiteral", $this->num2str($this->totaltaxesGoods, false, $currency));
		    if($netTotalGoods > 0) {
			$summaryModel->set("summaryTaxGoodsPercent", $this->totaltaxesGoods / $netTotalGoods * 100);
		    }
		    else {
			$summaryModel->set("summaryTaxGoodsPercent", 0);
		    }
                    $overall_tax_goods += $this->totaltaxesGoods;

                    $summaryModel->set("summaryTaxServices", $this->formatPrice($this->totaltaxesServices));
    		    $summaryModel->set("summaryTaxServicesLiteral", $this->num2str($this->totaltaxesServices, false, $currency));
		    if($netTotalServices > 0) {
			$summaryModel->set("summaryTaxServicesPercent", $this->totaltaxesServices / $netTotalServices * 100);
		    }
		    else {
			$summaryModel->set("summaryTaxServicesPercent", 0);
		    }
                    $overall_tax_services += $this->totaltaxesServices;
                }
		//Shipping & Handling taxes
		$sh_tax_details = $final_details['sh_taxes'];
                foreach($sh_tax_details as $taxDetail) {
			$sh_tax_percent = $sh_tax_percent + $taxDetail['percentage'];
		}
		//obtain the Currency Symbol
		$currencySymbol = $this->buildCurrencySymbol();
		
		$summaryModel->set("summaryShipping", $this->formatPrice($final_details['shipping_handling_charge']));
		$summaryModel->set("summaryShippingTax", $this->formatPrice($final_details['shtax_totalamount']));
		$summaryModel->set("summaryShippingTaxPercent", $sh_tax_percent);
		$summaryModel->set("summaryAdjustment", $this->formatPrice($final_details['adjustment']));
		$summaryModel->set("summaryGrandTotal", $this->formatPrice($final_details['grandTotal'])); // TODO add currency string
		$summaryModel->set("summaryGrandTotalLiteral", $this->num2str($final_details['grandTotal'], false, $currency));

                $overall_tax += $final_details['shtax_totalamount'];
                $overall_tax_goods += $final_details['shtax_totalamount'];
		$summaryModel->set("summaryOverallTax", $this->formatPrice(round($overall_tax)));
		$summaryModel->set("summaryOverallTaxLiteral", $this->num2str(round($overall_tax), false, $currency));
		$summaryModel->set("summaryOverallTaxGoods", $this->formatPrice(round($overall_tax_goods)));
		$summaryModel->set("summaryOverallTaxGoodsLiteral", $this->num2str(round($overall_tax_goods), false, $currency));
		$summaryModel->set("summaryOverallTaxServices", $this->formatPrice(round($overall_tax_services)));
		$summaryModel->set("summaryOverallTaxServicesLiteral", $this->num2str(round($overall_tax_services), false, $currency));
		
		if($final_details['taxtype'] == 'group_tax_inc') {
                    $summaryModel->set("summaryGrandTotalGoods", $this->formatPrice($netTotalGoods - $discountGoods + $final_details['shipping_handling_charge'] + $final_details['adjustment']));
                    $summaryModel->set("summaryGrandTotalGoodsLiteral", $this->num2str($netTotalGoods - $discountGoods + $final_details['shipping_handling_charge'] + $final_details['adjustment'], false, $currency));

                    $summaryModel->set("summaryGrandTotalServices", $this->formatPrice($netTotalServices - $discountServices + $final_details['adjustment']));
                    $summaryModel->set("summaryGrandTotalServicesLiteral", $this->num2str($netTotalServices - $discountServices + $final_details['adjustment'], false, $currency));
                } else {
                    $summaryModel->set("summaryGrandTotalGoods", $this->formatPrice($netTotalGoods - $discountGoods + $overall_tax_goods + $final_details['shipping_handling_charge'] + $final_details['adjustment']));
                    $summaryModel->set("summaryGrandTotalGoodsLiteral", $this->num2str($netTotalGoods - $discountGoods + $overall_tax_goods + $final_details['shipping_handling_charge'] + $final_details['adjustment'], false, $currency));

                    $summaryModel->set("summaryGrandTotalServices", $this->formatPrice($netTotalServices - $discountServices + $overall_tax_services + $final_details['adjustment']));
                    $summaryModel->set("summaryGrandTotalServicesLiteral", $this->num2str($netTotalServices - $discountServices + $overall_tax_services + $final_details['adjustment'], false, $currency));
                }
                
		return $summaryModel;
	}



}
?>