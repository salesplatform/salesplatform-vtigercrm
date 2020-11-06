<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'vtlib/Vtiger/PDF/models/Model.php';
include_once 'vtlib/Vtiger/PDF/PDFGenerator.php';
include_once 'data/CRMEntity.php';
include_once 'includes/SalesPlatform/PDF/viewers/SPHeaderViewer.php';
include_once 'includes/SalesPlatform/PDF/viewers/SPFooterViewer.php';
include_once 'includes/SalesPlatform/PDF/viewers/SPContentViewer.php';
include_once 'vtlib/Vtiger/PDF/viewers/PagerViewer.php';

class SalesPlatform_PDF_SPPDFController {

	protected $module;
	protected $focus = null;
	protected $template;
	protected $pageOrientation;
	protected $headerSize;
	protected $footerSize;
	protected $documentModel = null;
        protected $company; 

	function __construct($module, $templateid) {
		$this->moduleName = $module;
		$this->template = $this->loadTemplate($templateid);
	}
	
	function loadTemplate($templateid) {
	    global $adb;

	    $templates_result = $adb->pquery("select * from sp_templates where templateid=$templateid", array());
	    if($templates_result) {
		if($adb->num_rows($templates_result) > 0)
                {
		    $this->pageOrientation = $adb->query_result($templates_result,0,'page_orientation');
		    $this->headerSize = $adb->query_result($templates_result,0,'header_size');
		    $this->footerSize = $adb->query_result($templates_result,0,'footer_size');
                    $this->company = $adb->query_result($templates_result,0,'spcompany');

		    return html_entity_decode($adb->query_result($templates_result,0,'template'), ENT_QUOTES, 'UTF-8');
                }
	    }
	    
	    return '';
	}

	function loadRecord($id) {
		global $current_user;
		$this->focus = $focus = CRMEntity::getInstance($this->moduleName);
		$focus->retrieve_entity_info($id,$this->moduleName);
		$focus->apply_field_security($this->moduleName);
		$focus->id = $id;
	}

	function getPDFGenerator() {
		return new Vtiger_PDF_Generator();
	}

	function getContentViewer() {
            $contentViewer = new SalesPlatform_PDF_SPContentViewer($this->template, $this->pageOrientation);
            $contentViewer->setDocumentModel($this->buildDocumentModel());
            $contentViewer->setContentModels(array(new Vtiger_PDF_Model()));
            $contentViewer->setSummaryModel(new Vtiger_PDF_Model());
            $contentViewer->setLabelModel($this->buildContentLabelModel());
            $contentViewer->setWatermarkModel($this->buildWatermarkModel());
            return $contentViewer;
	}

	function getHeaderViewer() {
            $headerViewer = new SalesPlatform_PDF_SPHeaderViewer($this->template, $this->headerSize);
            $headerViewer->setModel($this->buildDocumentModel());
            return $headerViewer;
	}

	function getFooterViewer() {
            $footerViewer = new SalesPlatform_PDF_SPFooterViewer($this->template, $this->footerSize);
            $footerViewer->setModel($this->buildFooterModel());
            $footerViewer->setOnLastPage();
           
            return $footerViewer;
	}

	function getPagerViewer() {
            $pagerViewer = new Vtiger_PDF_PagerViewer();
            $pagerViewer->setModel($this->buildPagermodel());
            return $pagerViewer;
	}

	function Output($filename, $type) {
		if(is_null($this->focus)) return;

		$pdfgenerator = $this->getPDFGenerator();
        // SalesPlatform.ru begin Comment pagination 
		//$pdfgenerator->setPagerViewer($this->getPagerViewer());
        // SalesPlatform.ru end
		$pdfgenerator->setHeaderViewer($this->getHeaderViewer());
		$pdfgenerator->setFooterViewer($this->getFooterViewer());
		$pdfgenerator->setContentViewer($this->getContentViewer());
        
        $pdfgenerator->generate($filename, $type);
	}


	// Helper methods
	function buildFooterModel() {
		$footerModel = new Vtiger_PDF_Model();
		return $footerModel;
	}

    function buildDocumentModel() {

        global $adb;

        $model = new Vtiger_PDF_Model();

        if ( isset ($this->focus->column_fields["spcompany"]) && $this->focus->column_fields["spcompany"] != '') {
            $selfcompany = html_entity_decode($this->focus->column_fields["spcompany"], ENT_QUOTES, 'UTF-8');
        } else {
            $selfcompany = "Default";
        }

        // Company information
        $result = $adb->pquery("SELECT * FROM vtiger_organizationdetails WHERE company=?", array($selfcompany));
        $num_rows = $adb->num_rows($result);
        if($num_rows) {
            $resultrow = $adb->fetch_array($result);

            $model->set('orgAddress', $adb->query_result($result,0,"address"));
            $model->set('orgCity', $adb->query_result($result,0,"city"));
            $model->set('orgState', $adb->query_result($result,0,"state"));
            $model->set('orgCountry', $adb->query_result($result,0,"country"));
            $model->set('orgCode', $adb->query_result($result,0,"code"));

            $model->set('orgBillingAddress', implode(', ',
                array($adb->query_result($result,0,"code"),
                    $adb->query_result($result,0,"city"),
                    $adb->query_result($result,0,"address"))));

            $model->set('orgPhone', $adb->query_result($result,0,"phone"));
            $model->set('orgFax', $adb->query_result($result,0,"fax"));
            $model->set('orgWebsite', $adb->query_result($result,0,"website"));
            $model->set('orgInn', $adb->query_result($result,0,"inn"));
            $model->set('orgKpp', $adb->query_result($result,0,"kpp"));
            $model->set('orgBankAccount', $adb->query_result($result,0,"bankaccount"));
            $model->set('orgBankName', $adb->query_result($result,0,'bankname'));
            $model->set('orgBankId', $adb->query_result($result,0,'bankid'));
            $model->set('orgCorrAccount', $adb->query_result($result,0,'corraccount'));
            $model->set('orgOKPO', $adb->query_result($result,0,"okpo"));

            if($adb->query_result($result,0,'director')) {
                $model->set('orgDirector', $adb->query_result($result,0,'director'));
            } else {
                $model->set('orgDirector', str_repeat('_', 15));
            }
            if($adb->query_result($result,0,'bookkeeper')) {
                $model->set('orgBookkeeper', $adb->query_result($result,0,'bookkeeper'));
            } else {
                $model->set('orgBookkeeper', str_repeat('_', 15));
            }
            if($adb->query_result($result,0,'entrepreneur')) {
                $model->set('orgEntrepreneur', $adb->query_result($result,0,'entrepreneur'));
            } else {
                $model->set('orgEntrepreneur', str_repeat('_', 15));
            }
            if($adb->query_result($result,0,'entrepreneurreg')) {
                $model->set('orgEntrepreneurreg', $adb->query_result($result,0,'entrepreneurreg'));
            } else {
                $model->set('orgEntrepreneurreg', str_repeat('_', 50));
            }

            $model->set('orgLogo', '<img src="test/logo/'.$resultrow['logoname'].'" />');
            $model->set('orgLogoPath', 'test/logo/'.$resultrow['logoname']);
            $model->set('orgName', decode_html($resultrow['organizationname']));
        }

        $model->set('billingAddress', $this->buildHeaderBillingAddress());
        $model->set('shippingAddress', $this->buildHeaderShippingAddress());

        // Add owner info into model
        if(isset($this->focus->column_fields['record_id']) && $this->focus->column_fields['record_id'] != '') {
            $ownerArr = getRecordOwnerId($this->focus->column_fields['record_id']);
            if(isset($ownerArr['Users'])) {
                $userEntity = new Users();
                $userEntity->retrieve_entity_info($ownerArr['Users'], 'Users');
                $this->generateEntityModel($userEntity, 'Users', 'owner_', $model);
            }
            if(isset($ownerArr['Groups'])) {
                $groupInstance = Settings_Groups_Record_Model::getInstance($ownerArr['Groups']);
                $model->set('owner_groupid', $groupInstance->getId());
                $model->set('owner_groupname', $groupInstance->getName());
                $model->set('owner_description', $groupInstance->getDescription());
            }
        }

        return $model;
    }

	function focusColumnValues($names, $delimeter="\n") {
		if(!is_array($names)) {
			$names = array($names);
		}
		$values = array();
		foreach($names as $name) {
			$value = $this->focusColumnValue($name, false);
			if($value !== false) {
				$values[] = $value;
			}
		}
		return $this->joinValues($values, $delimeter);
	}

        function focusColumnValue($key, $defvalue='') {
		$focus = $this->focus;
		if(isset($focus->column_fields[$key])) {
			return $focus->column_fields[$key];
		}
		return $defvalue;
	}

        function joinValues($values, $delimeter= "\n") {
		$valueString = '';
		foreach($values as $value) {
			if(empty($value)) continue;
			$valueString .= $value . $delimeter;
		}
		return rtrim($valueString, $delimeter);
	}

	function formatNumber($value, $decimal=3) {
                if ($value === "") $value = 0;
		return number_format($value, $decimal, ',', ' ');
	}

	function formatPrice($value, $decimal=2) {
                if ($value === "") $value = 0;
		return number_format($value, $decimal, ',', ' ');
	}

	function formatDate($value) {
		return getDisplayDate($value);
	}

      /**
       * Сумма прописью
       * @author runcore
       */
      function num2str($inn, $stripkop=false, $currency='RUB') {

        global $current_language, $default_language, $sp_pdf_language;
        if(empty($sp_pdf_language)) {
            if(empty($current_language)) {
                $lang = $default_language;
            } else {
                $lang = $current_language;
            }
        } else {
            $lang = $sp_pdf_language;
        }

        require 'includes/SalesPlatform/PDF/CurrencyForms.php';

        $nol = $sp_numeric_forms[$lang]['0'];
        $str[100]= $sp_numeric_forms[$lang]['100'];
        $str[11] = $sp_numeric_forms[$lang]['11'];
        $str[10] = $sp_numeric_forms[$lang]['10'];
        $sex = $sp_numeric_forms[$lang]['1'];

        $forms = array(
           $sp_currency_forms[$lang][$currency][1],
           $sp_currency_forms[$lang][$currency][0],
           $sp_numeric_forms[$lang]['10^3'],
           $sp_numeric_forms[$lang]['10^6'],
           $sp_numeric_forms[$lang]['10^9'],
           $sp_numeric_forms[$lang]['10^12'],
       );
       $out = $tmp = array();
       // Поехали!
       $tmp = explode('.', str_replace(',','.', $inn));
       if ($tmp[0] === "") $tmp[0] = 0;
       $rub = number_format($tmp[0], 0,'','-');
       if ($rub== 0) $out[] = $nol;
       // нормализация копеек
       $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
       $segments = explode('-', $rub);
       $offset = sizeof($segments);
       if ((int)$rub== 0) { // если 0 рублей
           $o[] = $nol;
           $o[] = $this->morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
       }
       else {
           foreach ($segments as $k=>$lev) {
               $sexi= (int) $forms[$offset][3]; // определяем род
               $ri = (int) $lev; // текущий сегмент
               if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                   $offset--;
                   continue;
               }
               // нормализация
               $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
               // получаем циферки для анализа
               $r1 = (int)substr($ri, 0,1); //первая цифра
               $r2 = (int)substr($ri,1,1); //вторая
               $r3 = (int)substr($ri,2,1); //третья
               $r22= (int)$r2.$r3; //вторая и третья
               // разгребаем порядки
               if ($ri>99) $o[] = $str[100][$r1]; // Сотни
               if ($r22>20) {// >20
                   $o[] = $str[10][$r2];
                   $o[] = $sex[ $sexi ][$r3];
               }
               else { // <=20
                   if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                   elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
               }
               // Рубли
               $o[] = $this->morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
               $offset--;
           }
       }
       // Копейки
       if (!$stripkop) {
           $o[] = $kop;
           $o[] = $this->morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
       }
       if($lang == 'ru_ru') {
            return $this->rus_ucfirst(preg_replace("/\s{2,}/",' ',implode(' ',$o)));
       } else {
            return ucfirst(preg_replace("/\s{2,}/",' ',implode(' ',$o)));
       }
       
   }
   
   function rus_ucfirst($string) {
   
    $tbl = array('а' => 'А',
    		 'б' => 'Б',
    		 'в' => 'В',
    		 'г' => 'Г',
    		 'д' => 'Д',
    		 'е' => 'Е',
    		 'ё' => 'Ё',
    		 'ж' => 'Ж',
    		 'з' => 'З',
    		 'и' => 'И',
    		 'й' => 'Й',
    		 'к' => 'К',
    		 'л' => 'Л',
    		 'м' => 'М',
    		 'н' => 'Н',
    		 'о' => 'О',
    		 'п' => 'П',
    		 'р' => 'Р',
    		 'с' => 'С',
    		 'т' => 'Т',
    		 'у' => 'У',
    		 'ф' => 'Ф',
    		 'х' => 'Х',
    		 'ц' => 'Ц',
    		 'ч' => 'Ч',
    		 'ш' => 'Ш',
    		 'щ' => 'Щ',
    		 'ъ' => 'Ъ',
    		 'ы' => 'Ы',
    		 'ь' => 'Ь',
    		 'э' => 'Э',
    		 'ю' => 'Ю',
    		 'я' => 'Я');
    		 
	return substr_replace($string, $tbl[substr($string,0,2)], 0, 2);
   }
    
   /**
       * Склоняем словоформу
       */
   function morph($n, $f1, $f2, $f5) {
       $n = abs($n) % 100;
       $n1= $n % 10;
       if ($n>10 && $n<20) return $f5;
       if ($n1>1 && $n1<5) return $f2;
       if ($n1==1) return $f1;
       return $f5;
   }

    function literalDate($date){
        global $current_language, $default_language, $sp_pdf_language;
        if(empty($sp_pdf_language)) {
            if(empty($current_language)) {
                $lang = $default_language;
            } else {
                $lang = $current_language;
            }
        } else {
            $lang = $sp_pdf_language;
        }

        require 'includes/SalesPlatform/PDF/CurrencyForms.php';

        $date=explode("-", $date);
        $m = $sp_date_forms[$lang][$date[1] - 1];
	
	return $date[2].' '.$m.' '.$date[0];
    }

    function shortDate($date){
	if(!empty($date)) {
	    $date=explode("-", $date);
	    return $date[2].'.'.$date[1].'.'.$date[0];
	} else {
	    return '';
	}
    }
    
    private function mmyyyyDate($date){ 
 	$date=explode("-", $date); 
 	return $date[1].'/'.$date[0].'г.'; 
    }
    
    protected function generateEntityModel($entity, $module, $prefix, $model) {
	// Get only active field information
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        
        if(isset($this->focus->column_fields['currency_id'])) {
            $currencyInfo = getCurrencyInfo($this->focus->column_fields['currency_id']);
            $currency = $currencyInfo['code'];
        } else {
            $currency = 'RUB';
        }

	if($cachedModuleFields) {
	    foreach($cachedModuleFields as $fieldname=>$fieldinfo) {
		$fieldname = $fieldinfo['fieldname'];
		$type = explode('~', $fieldinfo['typeofdata']);
		$uitype = $fieldinfo['uitype'];
		
		if($uitype == 10 && !empty($entity->column_fields[$fieldname])) {
		    $entityid = $entity->column_fields[$fieldname];
		    $entityType = getSalesEntityType($entityid);
		    $recordName = array_values(getEntityName($entityType, $entityid));
		    $recordName = $recordName[0];
		    $model->set($prefix.$fieldname, $recordName);
		} else if($uitype == 117) {
		    $currencyInfo = getCurrencyInfo($entity->column_fields[$fieldname]);
		    $model->set($prefix.$fieldname, getTranslatedString($currencyInfo['name'], $module));
		//SalesPlatform.ru begin 
        } else if ($uitype == 33) {
            $fieldValue = str_replace(' |##| ', ', ', $entity->column_fields[$fieldname]); 
            $model->set($prefix.$fieldname, $fieldValue);
        } 
        //SalesPlatform.ru end
        else {
		
		    switch($type[0]) {
		    case 'N':
		    case 'NN': $model->set($prefix.$fieldname, $this->formatPrice($entity->column_fields[$fieldname]));
                           $model->set($prefix.$fieldname.'_literal', $this->num2str($entity->column_fields[$fieldname], false, $currency));
                           $model->set(strtoupper($prefix).strtoupper(str_replace(" ","",$fieldinfo['fieldlabel'])).'_LITERAL', $model->get($prefix.$fieldname.'_literal'));
                           $model->set(getTranslatedString(strtoupper($prefix), $module).str_replace(" ","",getTranslatedString($fieldinfo['fieldlabel'], $module)).getTranslatedString('_literal'), $model->get($prefix.$fieldname.'_literal'));
			   break;
		    case 'D': $model->set($prefix.$fieldname, $this->literalDate($entity->column_fields[$fieldname]));
			  $model->set($prefix.$fieldname.'_short', $this->shortDate($entity->column_fields[$fieldname]));
                          $model->set($prefix.$fieldname.'_mmyyyy', $this->mmyyyyDate($entity->column_fields[$fieldname])); 
                          $model->set(strtoupper($prefix).strtoupper(str_replace(" ","",$fieldinfo['fieldlabel'])).'_SHORT', $model->get($prefix.$fieldname.'_short'));
                          $model->set(getTranslatedString(strtoupper($prefix), $module).str_replace(" ","",getTranslatedString($fieldinfo['fieldlabel'], $module)).getTranslatedString('_short'), $model->get($prefix.$fieldname.'_short'));
			  break;
		    case 'C': if($entity->column_fields[$fieldname] == 0) {
			    $model->set($prefix.$fieldname, 'Нет');
			  } else {
			    $model->set($prefix.$fieldname, 'Да');
			  }
			  break;
		    case 'V':  $model->set($prefix.$fieldname, nl2br($entity->column_fields[$fieldname]));
                           $model->set($prefix.$fieldname.'_translated', nl2br(getTranslatedString($entity->column_fields[$fieldname], $module)));
			   break;
		    default: $model->set($prefix.$fieldname, $entity->column_fields[$fieldname]);
			   break;
		    }
		}
                // Add human-readable variables
                $model->set(strtoupper($prefix).strtoupper(str_replace(" ","",$fieldinfo['fieldlabel'])), $model->get($prefix.$fieldname));
                $model->set(getTranslatedString(strtoupper($prefix), $module).str_replace(" ","",getTranslatedString($fieldinfo['fieldlabel'], $module)), $model->get($prefix.$fieldname));
	    }
	}
    }

    function buildContentLabelModel() {
            $labelModel = new Vtiger_PDF_Model();
            return $labelModel;
}

    function buildFooterLabelModel() {
            $labelModel = new Vtiger_PDF_Model();
            return $labelModel;
    }

    function buildPagerModel() {
            $footerModel = new Vtiger_PDF_Model();
            $footerModel->set('format', '-%s-');
            return $footerModel;
    }

    function getWatermarkContent() {
            return '';
    }

    function buildWatermarkModel() {
            $watermarkModel = new Vtiger_PDF_Model();
            $watermarkModel->set('content', $this->getWatermarkContent());
            return $watermarkModel;
    }

    function buildHeaderBillingAddress() {
            return $this->focusColumnValues(array('bill_code','bill_country','bill_city','bill_street','bill_pobox'), ', ');
    }

    function buildHeaderShippingAddress() {
            return $this->focusColumnValues(array('ship_code','ship_country','ship_city','ship_street','ship_pobox'), ', ');
    }

    function buildCurrencySymbol() {
            global $adb;
            $currencyId = $this->focus->column_fields['currency_id'];
            if(!empty($currencyId)) {
                    $result = $adb->pquery("SELECT currency_symbol FROM vtiger_currency_info WHERE id=?", array($currencyId));
                    return decode_html($adb->query_result($result,0,'currency_symbol'));
            }
            return false;
    }

    function generateRelatedListModels($model) {
        global $adb;

        $lists = $adb->pquery("select vtiger_tab.name as relmodulename, vtiger_relatedlists.name as listtype from vtiger_relatedlists inner join vtiger_tab on vtiger_tab.tabid=vtiger_relatedlists.related_tabid where vtiger_relatedlists.tabid=? and vtiger_relatedlists.name in ('get_related_list', 'get_dependents_list')",
                array(getTabid($this->moduleName)));

        for($i = 0; $i < $adb->num_rows($lists); $i++) {
            $relmodulename = $adb->query_result($lists, $i, 'relmodulename');
            $listtype = $adb->query_result($lists, $i, 'listtype');

            if($listtype == 'get_related_list') {
                $listrecords = $adb->pquery('select vtiger_crmentityrel.relcrmid from vtiger_crmentityrel inner join vtiger_crmentity on (vtiger_crmentity.crmid=vtiger_crmentityrel.crmid and vtiger_crmentity.deleted=0) where vtiger_crmentityrel.crmid=? and vtiger_crmentityrel.relmodule=?',
                        array($this->focus->id, $relmodulename));

                for($j = 0; $j < 10; $j++) {
                    $entity = CRMEntity::getInstance($relmodulename);
                    if($j < $adb->num_rows($listrecords)) {
                        $relcrmid = $adb->query_result($listrecords, $j, 'relcrmid');
                        $entity->retrieve_entity_info($relcrmid, $relmodulename);
                    }
                    $this->generateEntityModel($entity, $relmodulename, 'Related'.$relmodulename.($j+1).'_', $model);
                }
            } else if($listtype == 'get_dependents_list') {
		$dependentFieldSql = $adb->pquery("SELECT tabid, fieldname, columnname FROM vtiger_field WHERE uitype='10' AND" .
				" fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE relmodule=? AND module=?)", array($this->moduleName, $relmodulename));
		$numOfFields = $adb->num_rows($dependentFieldSql);
		if ($numOfFields > 0) {
			$dependentColumn = $adb->query_result($dependentFieldSql, 0, 'columnname');
			$dependentField = $adb->query_result($dependentFieldSql, 0, 'fieldname');

                        $thisModule = $this->focus;
                        $other = CRMEntity::getInstance($relmodulename);

                        vtlib_setup_modulevars($this->moduleName, $this->focus);
                        vtlib_setup_modulevars($relmodulename, $other);

			$query = "SELECT vtiger_crmentity.*";

			$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
														'last_name' => 'vtiger_users.last_name'), 'Users');
			$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";

			$more_relation = '';
			if (!empty($other->related_tables)) {
				foreach ($other->related_tables as $tname => $relmap) {
					$query .= ", $tname.*";

					// Setup the default JOIN conditions if not specified
					if (empty($relmap[1]))
						$relmap[1] = $other->table_name;
					if (empty($relmap[2]))
						$relmap[2] = $relmap[0];
					$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
				}
			}

			$query .= " FROM $other->table_name";
			$query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
			$query .= " INNER  JOIN $thisModule->table_name   ON $thisModule->table_name.$thisModule->table_index = $other->table_name.$dependentColumn";
			$query .= $more_relation;
			$query .= " LEFT  JOIN vtiger_users        ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= " LEFT  JOIN vtiger_groups       ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

			$query .= " WHERE vtiger_crmentity.deleted = 0 AND $thisModule->table_name.$thisModule->table_index = $thisModule->id";

                        if($relmodulename != 'Faq' && $relmodulename != 'PriceBook'
                                        && $relmodulename != 'Users') {
                                global $current_user;
                                $secQuery = getNonAdminAccessControlQuery($relmodulename, $current_user);
                                if(strlen($secQuery) > 1) {
                                        $query = appendFromClauseToQuery($query, $secQuery);
                                }
                        }

                        $listrecords = $adb->pquery($query, array());

                        for($j = 0; $j < 10; $j++) {
                            $entity = CRMEntity::getInstance($relmodulename);
                            if($j < $adb->num_rows($listrecords)) {
                                $relcrmid = $adb->query_result($listrecords, $j, 'crmid');
                                $entity->retrieve_entity_info($relcrmid, $relmodulename);
                            }
                            $this->generateEntityModel($entity, $relmodulename, 'Related'.$relmodulename.($j+1).'_', $model);
                        }

                }
            }
        }
    }

    function generateUi10Models($model) {
        global $adb;

        $ui10fields = $adb->pquery("select fieldid,fieldname from vtiger_field where tabid=? and uitype=10 and presence in (0,2)",
                array(getTabid($this->moduleName)));

        for($i = 0; $i < $adb->num_rows($ui10fields); $i++) {
            $fieldid = $adb->query_result($ui10fields, $i, 'fieldid');
            $fieldname = $adb->query_result($ui10fields, $i, 'fieldname');
            $relmodules = $adb->pquery('select relmodule from vtiger_fieldmodulerel where fieldid=?',
                    array($fieldid));

            for($j = 0; $j < $adb->num_rows($relmodules); $j++) {
                $relmodule = $adb->query_result($relmodules, $j, 'relmodule');
                $entity = CRMEntity::getInstance($relmodule);

                if($this->focusColumnValue($fieldname)) {
                    if(getSalesEntityType($this->focusColumnValue($fieldname)) == $relmodule) {
                        $entity->retrieve_entity_info($this->focusColumnValue($fieldname), $relmodule);
                    }
                }

                $this->generateEntityModel($entity, $relmodule, strtolower($relmodule).'_', $model);
            }
        }
    }
}
?>