<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

if(defined('VTIGER_UPGRADE')) {
    
	global $adb;

	$query = 'SELECT DISTINCT profileid FROM vtiger_profile2utility';
	$result = $adb->pquery($query, array());

	$tabIdsList = array(getTabid('ProjectMilestone'), getTabid('PriceBooks'));
	$actionIdPerms = array(5 => 1, 6 => 1, 10 => 1);

	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$profileId = $adb->query_result($result, $i, 'profileid');

		foreach ($tabIdsList as $tabId) {
			foreach ($actionIdPerms as $actionId => $permission) {
				$isExist = $adb->pquery('SELECT 1 FROM vtiger_profile2utility WHERE profileid=? AND tabid=? AND activityid=?', array($profileId, $tabId, $actionId));
				if ($adb->num_rows($isExist)) {
					$query = 'UPDATE vtiger_profile2utility SET permission=? WHERE profileid=? AND tabid=? AND activityid=?';
				} else {
					$query = 'INSERT INTO vtiger_profile2utility(permission, profileid, tabid, activityid) VALUES (?, ?, ?, ?)';
				}
				Migration_Index_View::ExecuteQuery($query, array($permission, $profileId, $tabId, $actionId));
			}
		}
	}
}

chdir(dirname(__FILE__) . '/../../../');
require_once 'includes/main/WebUI.php';

$pickListFieldName = 'no_of_currency_decimals'; 
$moduleModel = Settings_Picklist_Module_Model::getInstance('Users'); 
$fieldModel = Vtiger_Field_Model::getInstance($pickListFieldName, $moduleModel); 

if ($fieldModel) { 
    $moduleModel->addPickListValues($fieldModel, 0); 
    $moduleModel->addPickListValues($fieldModel, 1); 
    
    $pickListValues = Vtiger_Util_Helper::getPickListValues($pickListFieldName); 
    $moduleModel->updateSequence($pickListFieldName, $pickListValues); 
} 

// SalesPlatform.ru begin

global $adb;

//Unlink unwanted resources
$unWanted = array(
    "modules/Project/BURAK_Gantt.class.php",
    "modules/Settings/EmailTemplates/views/ListUI5.php",
    "modules/Vtiger/resources/validator/EmailValidator.js"
);

for($i = 0; $i <= count($unWanted); $i++){
    if(file_exists($unWanted[$i])){
        unlink($unWanted[$i]);
    }
}

// Begin Multiple organizations
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_organizationdetails ADD company varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());
Migration_Index_View::ExecuteQuery("UPDATE vtiger_organizationdetails SET company='Default'", array());

Migration_Index_View::ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_spcompany (
  spcompanyid int(19) NOT NULL AUTO_INCREMENT,
  spcompany varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  presence int(1) NOT NULL DEFAULT '1',
  picklist_valueid int(19) NOT NULL DEFAULT '0',
  sortorderid int(1) DEFAULT NULL,
  color varchar(10) DEFAULT NULL,
  PRIMARY KEY (spcompanyid),
  UNIQUE KEY sp_spcompany_idx (spcompany)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci", array());

$query = "SELECT id+1 as id FROM vtiger_picklistvalues_seq";
$result = $adb->pquery($query, array());
$max_picklistvalue_id = $adb->query_result($result, 0 ,'id');
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_spcompany VALUES (?,?,?,?,?,?)", array(1, 'Default', 1, $max_picklistvalue_id, 1, NULL));
Migration_Index_View::ExecuteQuery("update vtiger_picklistvalues_seq set id = ?", array($max_picklistvalue_id));

Migration_Index_View::ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_spcompany_seq (
  id int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8", array());

Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_spcompany_seq VALUES (1)", array());

Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_invoice ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());

$columnname = "spcompany";
/* Тип интерфейса */
$uitype = 16;
$fieldname = "spcompany";
$fieldlabel = "Self Company";

/* Номер изменяемого модуля */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'Invoice'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');

/* Вычисление номера блока с заданным лейблом и номером вкладки */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_INVOICE_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');

/* Вычисление следующего порядкового номера в таблице vtiger_field,
   чтобы вставить новый ряд в эту таблицу (хранит все поля всех модулей) */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');

/* Вычисление следующего порядкового номера поля в блоке */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;

/* Создание полей модуля */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
    array($tab_id, $field_id, $columnname, "vtiger_invoice", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
   Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
    array($tab_id, $field_id, $columnname, "vtiger_invoice", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0)); 
}
/* Регистрация полей модуля */
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_quotes ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());

/* Номер изменяемого модуля */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'Quotes'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');

/* Вычисление номера блока с заданным лейблом и номером вкладки */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_QUOTE_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');

/* Вычисление следующего порядкового номера в таблице vtiger_field,
   чтобы вставить новый ряд в эту таблицу (хранит все поля всех модулей) */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');

/* Вычисление следующего порядкового номера поля в блоке */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;

/* Создание полей модуля */
if (Install_Utils_Model::checkHeaderColumn()) {
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
    array($tab_id, $field_id, $columnname, "vtiger_quotes", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
    array($tab_id, $field_id, $columnname, "vtiger_quotes", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));

}
/* Регистрация полей модуля */
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_salesorder ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT 'Default'", array());

/* Номер изменяемого модуля */
$query = "SELECT tabid FROM vtiger_tab WHERE name = 'SalesOrder'";
$result = $adb->pquery($query, array());
$tab_id = $adb->query_result($result, 0 ,'tabid');

/* Вычисление номера блока с заданным лейблом и номером вкладки */
$query = "SELECT blockid FROM vtiger_blocks WHERE blocklabel='LBL_SO_INFORMATION' AND tabid=?";
$result = $adb->pquery($query, array($tab_id));
$block_id = $adb->query_result($result, 0 ,'blockid');

/* Вычисление следующего порядкового номера в таблице vtiger_field,
   чтобы вставить новый ряд в эту таблицу (хранит все поля всех модулей) */
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field_seq SET id=id+1", array());
$query = "SELECT id FROM vtiger_field_seq";
$result = $adb->pquery($query, array());
$field_id = $adb->query_result($result, 0 ,'id');

/* Вычисление следующего порядкового номера поля в блоке */
$query = "SELECT MAX(sequence) AS sequence FROM vtiger_field WHERE block = ? AND tabid = ?";
$result = $adb->pquery($query, array($block_id, $tab_id));
$field_seq = $adb->query_result($result, 0 ,'sequence') + 1;

/* Создание полей модуля */
if (Install_Utils_Model::checkHeaderColumn()) {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_salesorder", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0, NULL));
} else {
    Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_field VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array($tab_id, $field_id, $columnname, "vtiger_salesorder", 1, $uitype, $fieldname, $fieldlabel, 1, 0, 0, 100, $field_seq, $block_id, 1, "V~O", 1, NULL, "BAS", 1, NULL, 0));
}
/* Регистрация полей модуля */
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_profile2field SELECT profileid, $tab_id, $field_id, 0, 1 FROM vtiger_profile", array());
Migration_Index_View::ExecuteQuery("INSERT INTO vtiger_def_org_field VALUES($tab_id, $field_id, 0, 1)", array());

Migration_Index_View::ExecuteQuery("alter table sp_templates add spcompany varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'All'", array());
Migration_Index_View::ExecuteQuery("update sp_templates set spcompany='All'", array());

if(defined('VTIGER_UPGRADE')) {
    Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_modentity_num ADD spcompany varchar(200) COLLATE utf8_unicode_ci DEFAULT ''", array());
}
// End

// Begin Update pdf templates
if(defined('INSTALLATION_MODE')) {
    $invoiceTemplate = '{header}

<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>

<p style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</p>

<div style="font-weight: bold; text-align: center">Образец заполнения платежного поручения</div>

<table border="1" cellpadding="2">
<tr>
  <td width="140">ИНН {$orgInn}</td><td width="140">КПП {$orgKpp}</td><td rowspan="2" width="50"><br/><br/>Сч. №</td><td rowspan="2" width="200"><br/><br/>{$orgBankAccount}</td>
</tr>
<tr>
<td colspan="2" width="280"><span style="font-size: 8pt">Получатель</span><br/>{$orgName}</td>
</tr>
<tr>
<td colspan="2" rowspan="2" width="280"><span style="font-size: 8pt">Банк получателя</span><br/>{$orgBankName}</td>
<td width="50">БИК</td>
<td rowspan="2" width="200">{$orgBankId}<br/>{$orgCorrAccount}</td>
</tr>
<tr>
<td width="50">Сч. №</td>
</tr>
</table>
<br/>
<h1 style="text-align: center">СЧЕТ № {$invoice_no} от {$invoice_invoicedate}</h1>
<br/><br/>
<table border="0">
<tr>
<td width="100">Плательщик:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
<tr>
<td width="100">Грузополучатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
</table>

{/header}

{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
    <tr style="text-align: center; font-weight: bold">
	<td width="30">№</td>
      <td width="260">Наименование<br/>товара</td>
      <td width="65">Единица<br/>изме-<br/>рения</td>
      <td width="35">Коли-<br/>чество</td>
	<td width="70">Цена</td>
	<td width="70">Сумма</td>
	</tr>
{/table_head}

{table_row}
    <tr>
	<td width="30">{$productNumber}</td>
      <td width="260">{$productName} {$productComment}</td>
	<td width="65" style="text-align: center">{$productUnits}</td>
	<td width="35" style="text-align: right">{$productQuantityInt}</td>
	<td width="70" style="text-align: right">{$productPriceWithTax}</td>
	<td width="70" style="text-align: right">{$productTotal}</td>
    </tr>
{/table_row}

{summary}
</table>
<table border="0" style="font-size: 8pt;font-weight: bold">
    <tr>
      <td width="450">
        <table border="0" cellpadding="2">
          <tr><td width="450" style="text-align: right">Итого:</td></tr>
          <tr><td width="450" style="text-align: right">В т.ч. НДС:</td></tr>
          <tr><td width="450" style="text-align: right">Всего к оплате:</td></tr>
        </table>
      </td>
      <td width="70">
        <table border="1" cellpadding="2">
          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>
          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>
          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>
        </table>
      </td>
  </tr>
</table>

<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>

{/summary}

{ending}
<br/>
    <p>Руководитель предприятия  __________________ ( {$orgDirector} ) <br/>
    <br/>
    Главный бухгалтер  __________________ ( {$orgBookkeeper} )
    </p>
{/ending}';

    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Счет'", array($invoiceTemplate));

    $nakladnaya = '{header}
<h1 style="font-size: 14pt">Расходная накладная № {$salesorder_no}</h1>
<hr/>
<p><br></p>
<table border="0" style="font-size: 9pt">
<tr>
<td width="80">Поставщик:</td><td width="450"><span style="font-weight: bold">{$orgName}</span></td>
</tr>
<tr>
<td width="80">Покупатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
</table>
{/header}

{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
    <tr style="text-align: center; font-weight: bold">
	<td width="30" rowspan="2">№</td>
	<td width="200" rowspan="2">Товар</td>
	<td width="50" rowspan="2" colspan="2">Мест</td>
	<td width="60" rowspan="2" colspan="2">Количество</td>
	<td width="60" rowspan="2">Цена</td>
	<td width="60" rowspan="2">Сумма</td>
	<td width="70">Номер ГТД</td>
    </tr>
    <tr style="text-align: center; font-weight: bold">
	<td width="70">Страна<br/>происхождения</td>
    </tr>
{/table_head}

{table_row}
    <tr>
	<td width="30" rowspan="2">{$productNumber}</td>
	<td width="200" rowspan="2">{$productName}</td>
	<td width="25" rowspan="2"></td>
	<td width="25" rowspan="2">шт.</td>
	<td width="30" rowspan="2" style="text-align: right">{$productQuantityInt}</td>
	<td width="30" rowspan="2">{$productUnits}</td>
	<td width="60" rowspan="2" style="text-align: right">{$productPrice}</td>
	<td width="60" rowspan="2" style="text-align: right">{$productNetTotal}</td>
	<td width="70">{$customsId}</td>
    </tr>
    <tr>
	<td width="70">{$manufCountry}</td>
    </tr>
{/table_row}

{summary}
</table>
<p></p>
<table border="0" style="font-weight: bold">
    <tr>
	<td width="400" style="text-align: right">Итого:</td>
	<td width="60" style="text-align: right">{$summaryNetTotal}</td>
    </tr>
    <tr>
	<td width="400" style="text-align: right">Сумма НДС:</td>
	<td width="60" style="text-align: right">{$summaryTax}</td>
    </tr>
</table>

<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>

{/summary}

{ending}
    <hr size="2">
    <p><br></p>
    <table border="0">
    <tr>
	<td>Отпустил  __________ </td><td>Получил  __________ </td>
    </tr>
    </table>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Накладная'", array($nakladnaya));

    $quote = '{header}

<p align="right">{$orgLogo}</p>
<p style="font-weight: bold">
{$orgName}<br/>
ИНН {$orgInn}<br/>
КПП {$orgKpp}<br/>
{$orgBillingAddress}<br/>
Тел.: {$orgPhone}<br/>
Факс: {$orgFax}<br/>
{$orgWebsite}
</p>

<h1>Коммерческое предложение № {$quote_no}</h1>
<p>Действительно до: {$quote_validtill}</p>
<hr size="2">
<p><br></p>
<p style="font-weight: bold">
{$account_accountname}<br/>
{$billingAddress}
</p>
{/header}

{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
    <tr style="text-align: center; font-weight: bold">
	<td width="30">№</td>
	<td width="260">Товары (работы, услуги)</td>
	<td width="70">Ед.</td>
	<td width="30">Кол-во</td>
	<td width="70">Цена</td>
	<td width="70">Сумма</td>
	</tr>
{/table_head}

{table_row}
    <tr>
	<td width="30">{$productNumber}</td>
	<td width="260">{$productName}</td>
	<td width="70">{$productUnits}</td>
	<td width="30" style="text-align: right">{$productQuantity}</td>
	<td width="70" style="text-align: right">{$productPrice}</td>
	<td width="70" style="text-align: right">{$productNetTotal}</td>
    </tr>
{/table_row}

{summary}
</table>
<p></p>
<table border="0">
    <tr>
	<td width="460" style="text-align: right">Итого:</td>
	<td width="70" style="text-align: right">{$summaryNetTotal}</td>
    </tr>
    <tr>
	<td width="460" style="text-align: right">Сумма НДС:</td>
	<td width="70" style="text-align: right">{$summaryTax}</td>
    </tr>
</table>

<p style="font-weight: bold">
Всего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )
</p>

{/summary}

{ending}
    <hr size="2">
    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>
    </p>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Предложение'", array($quote));

    $purchaseOrder = '{header}
<h1 style="font-size: 14pt">Заказ на закупку № {$purchaseorder_no}</h1>
<hr>
<p><br></p>
<table border="0" style="font-size: 9pt">
<tr>
<td width="80">Поставщик:</td><td width="450"><span style="font-weight: bold">{$vendor_vendorname}</span></td>
</tr>
<tr>
<td width="80">Покупатель:</td><td width="450"><span style="font-weight: bold">{$orgName}</span></td>
</tr>
</table>
{/header}
{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
<tr style="text-align: center; font-weight: bold">
<td width="30">№</td>
<td width="200">Товар</td>
<td width="60" colspan="2">Количество</td>
<td width="60">Цена</td>
<td width="60">Сумма</td>
</tr>
{/table_head}
{table_row}
<tr>
<td width="30">{$productNumber}</td>
<td width="200">{$productName}</td>
<td width="30" style="text-align: right">{$productQuantityInt}</td>
<td width="30">{$productUnits}</td>
<td width="60" style="text-align: right">{$productPrice}</td>
<td width="60" style="text-align: right">{$productNetTotal}</td>
</tr>
{/table_row}
{summary}
</table>
<p></p>
<table border="0" style="font-weight: bold">
<tr>
<td width="350" style="text-align: right">Итого:</td>
<td width="60" style="text-align: right">{$summaryNetTotal}</td>
</tr>
<tr>
<td width="350" style="text-align: right">Сумма НДС:</td>
<td width="60" style="text-align: right">{$summaryTax}</td>
</tr>
</table>
<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>
{/summary}
{ending}
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Заказ на закупку'", array($purchaseOrder));

    $act = '{header}
<div style="font-weight: bold;text-decoration: underline">{$orgName}</div>
<div style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</div>


<h1 style="text-align: center">Акт № {$act_no} от {$act_actdate} г.</h1>

<p>Заказчик: {$account_accountname}</p>

{/header}
{content}{/content}
{table_head}

<table border="1" style="font-size: 8pt" cellpadding="2">
 <tr style="text-align: center; font-weight: bold">
 <td width="30">№</td>
 <td width="220">Наименование работы (услуги)</td>
 <td width="70">Ед. изм.</td>
 <td width="70">Количество</td>
 <td width="70">Цена</td>
 <td width="70">Сумма</td>
 </tr>

{/table_head}

{services_row}
 <tr>
 <td width="30">{$serviceNumber}</td>
 <td width="220">{$productName}</td>
 <td width="70" style="text-align: center">{$productUnits}</td>
 <td width="70" style="text-align: right">{$productQuantity}</td>
 <td width="70" style="text-align: right">{$productPrice}</td>
 <td width="70" style="text-align: right">{$productNetTotal}</td>
 </tr>
{/services_row}

{summary}
</table>
<table border="0" style="font-weight: bold">
 <tr>
 <td width="457" style="text-align: right">Итого:</td>
   <td width="76" style="text-align: right"><table border="1"><tr><td>{$summaryNetTotalServices}</td></tr></table></td>
 </tr>
 <tr>
 <td width="457" style="text-align: right">Без налога (НДС).</td>
 <td width="76" style="text-align: center"><table border="1"><tr><td>-</td></tr></table></td>
 </tr>
 <tr>
 <td width="457" style="text-align: right">Всего (с учетом НДС):</td>
 <td width="76" style="text-align: right"><table border="1"><tr><td>{$summaryGrandTotalServices}</td></tr></table></td>
 </tr>
</table>

<p style="font-style: italic">
    Всего оказано услуг на сумму: {$summaryGrandTotalServicesLiteral}
</p>
<br/>
<p>
Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.
</p>

{/summary}

{ending}
<table border="0">
 <tr>
   <td align="center" colspan="2">
    Исполнитель: _________ ({$orgDirector})<br/>
   </td>
   <td align="center" colspan="2">
    Заказчик: _________ (____________)
</td>
  </tr>
  <tr>
    <td align="right"><span style="font-size: 6pt">подпись</span></td>
    <td align="left"><br/><br/>М.П.</td>
    <td align="right"><span style="font-size: 6pt">подпись</span></td>
    <td align="left"><br/><br/>М.П.</td>
  </tr>
</table>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Акт'", array($act));

    $actNDS = '{header}
<div style="font-weight: bold;text-decoration: underline">{$orgName}</div>
<div style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</div>


<h1 style="text-align: center">Акт № {$act_no} от {$act_actdate} г.</h1>

<p>Заказчик: {$account_accountname}</p>

{/header}
{content}{/content}
{table_head}

<table border="1" style="font-size: 8pt" cellpadding="2">
 <tr style="text-align: center; font-weight: bold">
 <td width="30">№</td>
 <td width="220">Наименование работы (услуги)</td>
 <td width="70">Ед. изм.</td>
 <td width="70">Количество</td>
 <td width="70">Цена</td>
 <td width="70">Сумма</td>
 </tr>

{/table_head}

{services_row}
 <tr>
 <td width="30">{$serviceNumber}</td>
 <td width="220">{$productName}</td>
 <td width="70" style="text-align: center">{$productUnits}</td>
 <td width="70" style="text-align: right">{$productQuantity}</td>
 <td width="70" style="text-align: right">{$productPriceWithTax}</td>
 <td width="70" style="text-align: right">{$productTotal}</td>
 </tr>
{/services_row}

{summary}
</table>
<table border="0" style="font-weight: bold">
 <tr>
 <td width="457" style="text-align: right">Итого:</td>
   <td width="76" style="text-align: right"><table border="1"><tr><td>{$summaryGrandTotalServices}</td></tr></table></td>
 </tr>
 <tr>
 <td width="457" style="text-align: right">В т.ч. НДС:</td>
 <td width="76" style="text-align: right"><table border="1"><tr><td>{$summaryTaxServices}</td></tr></table></td>
 </tr>
 <tr>
 <td width="457" style="text-align: right">Всего (с учетом НДС):</td>
 <td width="76" style="text-align: right"><table border="1"><tr><td>{$summaryGrandTotalServices}</td></tr></table></td>
 </tr>
</table>

<p style="font-style: italic">
Всего оказано услуг на сумму: {$summaryGrandTotalServicesLiteral}
</p>
<br/>
<p>
Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.
</p>

{/summary}

{ending}
<table border="0">
 <tr>
   <td align="center" colspan="2">
     Исполнитель: _________ ({$orgDirector})<br/>
   </td>
   <td align="center" colspan="2">
     Заказчик: _________ (____________)
  </td>
  </tr>
  <tr>
    <td align="right"><span style="font-size: 6pt">подпись</span></td>
    <td align="left"><br/><br/>М.П.</td>
    <td align="right"><span style="font-size: 6pt">подпись</span></td>
    <td align="left"><br/><br/>М.П.</td>
  </tr>
</table>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Акт с НДС'", array($actNDS));

    $factura = '{header}

<p style="font-size: 6pt; text-align: right; margin: 0; padding: 0">Приложение №1<br/>
к постановлению Правительства Российской Федерации<br/>
от 26 декабря 2011 г. № 1137
</p>
<h1 style="font-size: 12pt; margin: 0; padding: 0">Счет-фактура № {$consignment_no} от {$consignment_consignmentdate} </h1>
<h1 style="font-size: 12pt; margin: 0; padding: 0">Исправление № -- от -- </h1>
<p style="font-size: 8pt; margin: 0; padding: 0">Продавец: {$orgName} <br/>
Адрес: {$orgBillingAddress} <br/>
ИНН/КПП продавца: {$orgInn}/{$orgKpp} <br/>
Грузоотправитель и его адрес: -- <br/>
Грузополучатель и его адрес: {$account_accountname}, {$billingAddress} <br/>
К платежно-расчетному документу № {$invoice_invoice_no} от {$invoice_invoicedate} <br/>
Покупатель: {$account_accountname} <br/>
Адрес:	{$billingAddress} <br/>
ИНН / КПП покупателя: {$account_inn}/{$account_kpp}<br/>
Валюта: наименование, код Российский рубль, 643</p>
{/header}

{table_head}
<table border="1" style="font-size: 6pt" cellpadding="2">
    <tr style="text-align: center">
	<td width="150" valign="middle" rowspan="2">Наименование товара (описание<br/>выполненных работ, оказанных услуг),<br/>имущественного права</td>
	<td width="77" valign="middle" colspan="2">Единица<br/>измерения</td>
        <td width="45" valign="middle" rowspan="2">Коли-<br/>чество<br/>(объем)</td>
	<td width="55" valign="middle" rowspan="2">Цена (тариф)<br/>за единицу<br/>измерения</td>
	<td width="80" valign="middle" rowspan="2">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, без налога -<br/>всего</td>
	<td width="35" valign="middle" rowspan="2">В том<br/>числе<br/>сумма<br/>акциза</td>
	<td width="55" valign="middle" rowspan="2">Налоговая<br/>ставка</td>
        <td width="55" valign="middle" rowspan="2">Сумма<br/>налога<br/>предъяв-<br/>ляемая<br/>покупателю</td>
	<td width="70" valign="middle" rowspan="2">Стоимость товаров<br/>(работ, услуг),<br/>имущественных<br/>прав, с налогом -<br/>всего</td>
	<td width="85" valign="middle" colspan="2">Страна<br/>происхождения товара</td>
	<td width="75" valign="middle" rowspan="2">Номер<br/>таможенной<br/>декларации</td>
	</tr>
        <tr style="text-align: center">
	  <td width="27" valign="middle">код</td>
          <td width="50" valign="middle">условное<br/>обозначение<br/>(национальное)</td>
	  <td width="35" valign="middle">цифровой<br/>код</td>
	  <td width="50" valign="middle">краткое<br/>наименование</td>
	</tr>
    <tr style="text-align: center">
	<td width="150">1</td>
	<td width="27">2</td>
	<td width="50">2а</td>
	<td width="45">3</td>
	<td width="55">4</td>
	<td width="80">5</td>
	<td width="35">6</td>
	<td width="55">7</td>
	<td width="55">8</td>
	<td width="70">9</td>
	<td width="35">10</td>
	<td width="50">10а</td>
	<td width="75">11</td>
    </tr>
{/table_head}

{table_row}
    <tr>
	<td width="150" style="padding: 3px">{$productName}</td>
	<td width="27" style="text-align: center;padding: 3px">{$productUnitsCode}</td>
	<td width="50" style="text-align: center;padding: 3px">{$productUnits}</td>
	<td width="45" style="text-align: right;padding: 3px">{$productQuantity}</td>
	<td width="55" style="text-align: right;padding: 3px">{$productPrice}</td>
	<td width="80" style="text-align: right;padding: 3px">{$productNetTotal}</td>
	<td width="35" style="text-align: center;padding: 3px">--</td>
	<td width="55" style="text-align: center;padding: 3px">{$productTaxPercent}%</td>
	<td width="55" style="text-align: right;padding: 3px">{$productTax}</td>
	<td width="70" style="text-align: right;padding: 3px">{$productTotal}</td>
	<td width="35" style="text-align: center;padding-left: 3px">{$manufCountryCode}</td>
	<td width="50" style="text-align: center;padding-left: 3px">{$manufCountry}</td>
	<td width="75" style="text-align: center;padding: 3px">{$customsId}</td>
    </tr>
{/table_row}

{summary}
    <tr>
	<td width="327" colspan="7"><span style="font-weight: bold">Всего к оплате</span></td>
	<td width="80" style="text-align: right;padding: 3px"><span style="font-weight: bold">{$summaryNetTotal}</span></td>
	<td width="90" colspan="2" style="text-align: center"><span style="font-weight: bold">X</span></td>
	<td width="55" style="text-align: right"><span style="font-weight: bold">{$summaryTax}</span></td>
	<td width="70" style="text-align: right"><span style="font-weight: bold">{$summaryGrandTotal}</span></td>
    </tr>
</table>
{/summary}

{ending}
<p></p>
<table border="0" style="font-size: 8pt">
<tr>
    <td width="200" style="text-align: right">Руководитель организации<br/>или иное уполномоченное лицо</td>
    <td width="80" style="text-align: center"> __________ </td>
    <td width="80" style="text-align: center"> <span style="text-decoration: underline">{$orgDirector}</span></td>
    <td style="text-align: right">Главный бухгалтер<br/>или иное уполномоченное лицо</td>
    <td width="80" style="text-align: center">  __________ </td>
    <td width="80" style="text-align: center"> <span style="text-decoration: underline">{$orgBookkeeper}</span></td>
</tr>
<tr style="font-size: 6pt; text-align: center">
    <td width="200"></td><td width="80"> (подпись) </td><td width="80"> (ФИО)</td><td></td><td width="80"> (подпись)</td><td width="80"> (ФИО)</td>
</tr>
<tr>
<td colspan="6"><p></p></td>
</tr>
<tr>
    <td width="200" style="text-align: right">Индивидуальный предприниматель </td>
    <td width="80" style="text-align: center"> __________ </td>
    <td width="80" style="text-align: center"> <span style="text-decoration: underline">{$orgEntrepreneur}</span> </td>
    <td colspan="3" style="text-align: center"> <span style="text-decoration: underline">{$orgEntrepreneurreg}</span> </td>
</tr>
<tr style="font-size: 6pt; text-align: center">
    <td width="200"></td><td width="80"> (подпись) </td><td width="80"> (ФИО) </td><td colspan="3"> (реквизиты свидетельства о государственной <br/>регистрации индивидуального предпринимателя)</td>
</tr>
</table>
<p></p>
<p style="font-size: 6pt">Примечание 1. Первый экземпляр счета-фактуры, составленного на бумажном носителе - покупателю, второй экземпляр - продавцу.<br/>
  2. При составлении организацией счета-фактуры в электронном виде показатель "Главный бухгалтер (подпись) (ФИО)" не формируется.
</p>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Счет-фактура'", array($factura));
// End

    $torg = '{header}
<!-- Table marking up header, goods table and footer -->
<table>
<tr><td colspan="2">

  <table>
    <tr>
      <td colspan="3" rowspan="2" style="width:700px">
      </td>
      <td style="text-align:right; font-size:x-small; width:80px">
        Унифицированная форма № ТОРГ-12
        Утверждена постановлением Госкомстата России от 25.12.98 № 132
      </td>
    </tr>
    <tr>
      <td style="width:80px">
        <table border="1">
          <tr><td style="text-align:center">Коды</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Грузоотправитель</td>
      <td style="font-size:x-small; font-weight:bold; width:520px">{$orgName}, {$orgBillingAddress}, тел. : {$orgPhone}, р/с {$orgBankAccount}, {$orgBankName}, БИК {$orgBankId}, к/с {$orgCorrAccount}</td>
      <td rowspan="8" style="width:100px">
        <table style="text-align:right; font-size:small">
          <tr><td>Форма по ОКУД</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td></td></tr>
          <tr><td>Вид деятельности по ОКДП</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td rowspan="4">
            <table border="1">
              <tr><td>номер</td></tr>
              <tr><td>дата</td></tr>
              <tr><td>номер</td></tr>
              <tr><td>дата</td></tr>
            </table>
            <table>
              <tr><td>Вид операции</td></tr>
            </table>
            </td></tr>
        </table>
      </td>
      <td rowspan="8" style="width:80px">
        <table border="2" style="text-align:center; font-size:small">
          <tr><td style="font-weight:bold">0330212</td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Структурное подразделение</td>
      <td style="font-size:x-small; width:400px"></td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Грузополучатель</td>
      <td style="font-size:x-small; width:400px">{$account_accountname}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Поставщик</td>
      <td style="font-size:x-small; width:400px">{$orgName}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Плательщик</td>
      <td style="font-size:x-small; width:400px">{$account_accountname}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Основание</td>
      <td style="font-size:x-small; width:400px"></td>
    </tr>
    <tr>
      <td style="width:80px"></td>
      <td style="text-align: center; font-size:xx-small; width:400px">наименование документа (договор, контракт, заказ-наряд)</td>
    </tr>
  </table>

</td></tr>
<tr>
  <td colspan="2" style="text-align:center; width:600px">
    <!-- Markup inside heading elements -->
    <table cellspacing="5" style="font-size:x-small">
      <tr>
        <td></td>
        <td rowspan="2">
          <table border="1" style="text-align:center">
            <tr>
              <td>Номер документа</td>
              <td>Дата составления</td>
            </tr>
            <tr>
              <td>{$consignment_goods_consignment_no}</td>
              <td>{$consignment_consignmentdate_short}</td>
            </tr>
          </table>
        </td>
        <td>Транспортная накладная</td>
      </tr>
      <tr>
        <td style="font-weight:bold; text-align:right">ТОВАРНАЯ НАКЛАДНАЯ</td>
        <td></td>
      </tr>
    </table>
  </td>
</tr>
<tr><td>
<!-- Whitespace between header and goods table -->
</td></tr>
{/header}
{table_head}
<tr><td colspan="2">

  <!-- Goods table -->
  <table border="1" style="font-size:x-small; text-align:center; width:780px">
    <tr style="text-align:center">
      <td style="width:30px">Номер<br />по<br />порядку</td>
      <td colspan="2" style="width:210px">Товар</td>
      <td colspan="2" style="width:80px">Единица измерения</td>
      <td style="width:40px">Вид упаковки</td>
      <td colspan="2" style="width:80px">Количество</td>
      <td style="width:50px">Масса брутто</td>
      <td style="width:50px">Количество<br />(масса нетто)</td>
      <td style="width:50px">Цена, руб. коп.</td>
      <td style="width:50px">Сумма без учёта НДС, руб. коп.</td>
      <td colspan="2" style="width:60px">НДС</td>
      <td style="width:80px">Сумма с учётом НДС, руб. коп.</td>
    </tr>
    <tr>
      <td style="width:30px"></td>
      <td style="width:180px">наименование, характеристика, сорт, артикул товара</td>
      <td style="width:30px">код</td>
      <td style="width:50px">наименование</td>
      <td style="width:30px">код по ОКЕИ</td>
      <td style="width:40px"></td>
      <td style="width:40px">в одном<br />месте</td>
      <td style="width:40px">мест,<br />штук</td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:30px">ставка, %</td>
      <td style="width:30px">сумма, руб. коп.</td>
      <td style="width:80px"></td>
    </tr>
    <tr>
      <td style="width:30px">1</td>
      <td style="width:180px">2</td>
      <td style="width:30px">3</td>
      <td style="width:50px">4</td>
      <td style="width:30px">5</td>
      <td style="width:40px">6</td>
      <td style="width:40px">7</td>
      <td style="width:40px">8</td>
      <td style="width:50px">9</td>
      <td style="width:50px">10</td>
      <td style="width:50px">11</td>
      <td style="width:50px">12</td>
      <td style="width:30px">13</td>
      <td style="width:30px">14</td>
      <td style="width:80px">15</td>
    </tr>
{/table_head}
{goods_row}
    <tr>
      <td style="width:30px">{$goodsNumber}</td>
      <td style="width:180px">{$productName}</td>
      <td style="width:30px">{$productCode}</td>
      <td style="width:50px">{$productUnits}</td>
      <td style="width:30px">{$productUnitsCode}</td>
      <td style="width:40px"></td>
      <td style="width:40px"></td>
      <td style="width:40px">{$productQuantityInt}</td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px">{$productPrice}</td>
      <td style="width:50px">{$productNetTotal}</td>
      <td style="width:30px">{$productTaxPercent}</td>
      <td style="width:30px">{$productTax}</td>
      <td style="width:80px">{$productTotal}</td>
    </tr>
{/goods_row}
{summary}
  </table>

</td></tr>
<!-- Total subtable -->
<tr style="font-size:x-small">
  <!-- Whitespace in the left -->
  <td style="width:403px">

    <table>
      <tr>
        <td colspan="7" style="text-align:right">Итого:</td>
      </tr>
      <tr>
        <td colspan="7" style="text-align:right">Всего по накладной:</td>
      </tr>
    </table>

  </td>
  <!-- Total subtable itself -->
  <td>

    <table border="1" style="text-align:center">
      <tr>
        <td style="width:40px"></td>
        <td style="width:50px"></td>
        <td style="width:50px"></td>
        <td style="width:50px">X</td>
        <td style="width:50px">{$summaryNetTotalGoods}</td>
        <td style="width:30px">X</td>
        <td style="width:30px">{$summaryTaxGoods}</td>
        <td style="width:80px">{$summaryGrandTotalGoods}</td>
      </tr>
      <tr>
        <td style="width:40px"></td>
        <td style="width:50px"></td>
        <td style="width:50px"></td>
        <td style="width:50px">X</td>
        <td style="width:50px">{$summaryNetTotalGoods}</td>
        <td style="width:30px">X</td>
        <td style="width:30px">{$summaryTaxGoods}</td>
        <td style="width:80px">{$summaryGrandTotalGoods}</td>
      </tr>
    </table>

  </td>
</tr>
<tr style="page-break-before: always"><td>
<!-- Whitespace -->
</td></tr>
<tr><td colspan="2" style="width:780px">

  <table style="font-size:x-small">
    <tr>
      <td rowspan="2" style="width:250px">Товарная накладная имеет приложение на </td>
      <td style="text-align: center; width:350px">______________________________________________________________________</td>
      <td rowspan="2" style="width:80px"> листах</td>
    </tr>
    <tr>
      <td style="text-align:center; width:350px">прописью</td>
    </tr>
    <tr>
      <td rowspan="2" style="width:250px">и содержит </td>
      <td style="text-align: center; width:350px">______________________________________________________________________</td>
      <td rowspan="2" style="width:80px"> порядковых номеров записей</td>
    </tr>
    <tr>
      <td style="text-align:center; width:350px">прописью</td>
    </tr>
  </table>

</td></tr>
<tr><td colspan="2" style="text-align:center; width:780px">

  <table style="font-size:x-small">
    <tr>
      <td colspan="2" rowspan="2" style="width:200px"></td>
      <td rowspan="2" style="text-align:right; width:190px">Масса груза (нетто)</td>
      <td style="text-align:center; width:390px">______________________________________________________________________</td>
    </tr>
    <tr>
      <td style="text-align:center; width:390px">прописью</td>
    </tr>
    <tr>
      <td rowspan="2" style="text-align:right; width:100px">Всего мест</td>
      <td rowspan="2" style="width:100px">{$summaryTotalGoods}</td>
      <td rowspan="2" style="text-align:right; width:190px">Масса груза (брутто)</td>
      <td style="text-align:center; width:390px">______________________________________________________________________</td>
    </tr>
    <tr>
      <td style="text-align:center; width:390px">прописью</td>
    </tr>
    <tr>
      <td colspan="3" style="width:390px">
        <table>
          <tr>
            <td rowspan="2" style="width:170px">Приложение (паспорта, сертификаты и т.п.) на </td>
            <td style="text-align:center; width:170px">__________________________________________</td>
            <td rowspan="2" style="width:50px"> листах</td>
          </tr>
          <tr>
            <td style="text-align:center; width:170px">прописью</td>
          </tr>
        </table>
      </td>
      <td></td>
    </tr>
  </table>

</td></tr>
<tr><td>
</td></tr>
<!-- Footer (sent/received, stamps, etc.) -->
<tr style="font-size:small">
  <td style="text-align:center; width:390px">
    <!-- "Sent" part on the left -->
    <table>
      <tr><td>
        Всего отпущено на сумму {$summaryGrandTotalGoodsLiteral}<br />
      </td></tr>
{/summary}
{ending}
      <tr><td>
        <table>
          <tr>
            <td rowspan="2">Отпуск груза разрешил</td>
            <td>Директор</td>
            <td>_____________________</td>
            <td>{$orgDirector}</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="2" style="font-weight:bold">Главный (старший) бухгалтер</td>
            <td>_____________________</td>
            <td>{$orgBookkeeper}</td>
          </tr>
          <tr style="font-size:x-small">
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <tr>
            <td rowspan="2">Отпуск груза произвёл</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        М.П. "   " _____________ 20   года
      </td></tr>
    </table>
  </td>
  <!-- "Received" part on the right -->
  <td style="text-align:center; width:390px">
    <table>
      <tr><td>
        По доверенности № _______ от   "   " _____________ 20   года
      </td></tr>
      <tr><td>
        выданной ________________________________________________
      </td></tr>
      <tr><td style="text-align:center; font-size:x-small">
        кем, кому (организация, место работы, должность, фамилия, и. о.)
      </td></tr>
      <tr><td>
        <table>
          <tr>
            <td rowspan="2">Груз принял</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <!-- Whitespace to aline with left-side clauses -->
          <tr>
            <td></td>
          </tr>
          <tr>
            <td rowspan="2">Груз получил<br />грузополучатель</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        М.П. "   " _____________ 20   года
      </td></tr>
    </table>
  </td>
</tr>

</table>
{/ending}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'ТОРГ-12'", array($torg));

    $order = '{content}

<!-- Таблица, разделяющая ордер, перфорацию и квитанцию -->
<table border=0>
  <tr>

    <td style="width: 360">
      <!--  Таблица, упорядочивающая элементы ордера -->
      <table border=0>

        <tr style="text-align: right; font-size: 4pt;"><td>
          <p>Унифицированная форма КО-1<br />Утверждена постановлением Госкомстата России от 18.08.98 № 88<br/></p>
        </td></tr>

        <tr><td>
          <!-- Название организации и подразделения, коды по ОКУД и ОКПО -->
          <table border="0">

            <tr style="text-align: center; font-size: 6pt;">
              <td></td>
              <td></td>
              <td>
                <table border="1">
                  <tr><td>Коды</td></tr>
                </table>
              </td>
            </tr>

            <tr style="font-size: 8pt;">
              <td style="text-align: center;"></td>
              <td style="text-align: right;">Форма по ОКУД</td>
              <td rowspan="3" style="text-align: center;">
                <!-- Таблица, создающая жирную рамку -->
                <table border="2">
                  <tr>
                    <td style="text-align: center;">
                      <!-- Таблица, создающая внутреннюю разметку -->
                      <table border="1">
                        <tr><td>310001</td></tr>
                        <tr><td>{$orgOKPO}</td></tr>
                        <tr><td></td></tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>

            </tr>

            <tr>

              <td>
                <table>
                  <tr><td style="font-size: 6pt;">{$orgName}</td></tr>
                  <tr><td style="font-size: 6pt;">_________________________________</td></tr>
                  <tr><td style="font-size: 4pt;">организация</td></tr>
                </table>
              </td>

              <td style="text-align: right; font-size: 8pt;">по ОКПО</td>

            </tr>

            <tr>

              <td colspan="2">
                <table>
                  <tr><td style="font-size: 6pt;"></td></tr>
                  <tr><td style="font-size: 6pt;">_____________________________________________________</td></tr>
                  <tr><td style="font-size: 4pt;">подразделение</td></tr>
                </table>
              </td>

            </tr>

          </table>

        </td></tr>

        <tr><td>
          <!-- Номер документа, дата составления -->
          <table border="0">

            <tr>

              <td align="center" valign="middle" rowspan="2" style="font-weight: bold; font-size: 8pt;">ПРИХОДНЫЙ КАССОВЫЙ ОРДЕР</td>

              <td>
                <!-- Номер документа, дата составления -->
                <table border=1 style="text-align: center; font-size: 6pt;">
                  <tr>
                    <td>Номер документа</td>
                    <td>Дата составления</td>
                  </tr>
                </table>
              </td>

            </tr>

            <tr>
              <!-- Номер документа, дата составления -->
              <td>
                <table border="2" style="text-align: center; font-size: 6pt;">
                  <tr>
                    <td>{$payment_doc_no}</td>
                    <td>{$payment_pay_date}</td>
                  </tr>
                </table>
              </td>
            </tr>

          </table>
          <br/>
        </td></tr>

        <tr><td>
          <table border="1">

            <tr align="center" valign="middle" style="font-size: 6pt;">
              <td rowspan="2">Дебет</td>
              <td colspan="4">Кредит</td>
              <td rowspan="2">Сумма</td>
              <td rowspan="2">Код целевого назначения</td>
              <td rowspan="2"></td>
            </tr>

            <tr align="center" valign="middle" style="font-size: 4pt;">
              <td></td>
              <td>код<br />структурного<br />подразделения</td>
              <td>корреспондирующий<br />счет,<br />субсчет</td>
              <td>код<br />аналитического<br />учета</td>
            </tr>

            <tr align="center" valign="middle" style="font-size: 6pt;">
              <td>{$payment_debit}</td>
              <td></td>
              <td></td>
              <td>{$payment_coracc_subacc}</td>
              <td>{$payment_analytics_code}</td>
              <td>{$payment_amount}</td>
              <td>{$payment_target_code}</td>
              <td></td>
            </tr>

          </table>

        </td></tr>

        <tr><td>
          <p style="font-size: 6pt;"><br />Принято от:<br />{$payment_payer}</p>
          <p style="font-size: 6pt;">Основание:<br />{$payment_pay_details}</p>
          <p style="font-size: 6pt;">Сумма:<br />{$payment_amount_literal}</p>
          <p style="font-size: 6pt;">В том числе: НДС (Без НДС)</p>
          <p style="font-size: 6pt;">Приложение:</p>

          <table border="0">

            <tr style="font-size: 6pt;">
              <td rowspan="2" style="width: 70;"><b>Главный бухгалтер</b></td>
              <td align="center" style="width: 70;">_____________</td>
              <td align="center" style="width: 100;">{$orgBookkeeper}</td>
            </tr>

            <tr align="center" style="font-size: 4pt;">
              <td style="width: 70;">подпись</td>
              <td style="width: 100;">расшифровка подписи</td>
            </tr>

            <tr style="font-size: 6pt;">
              <td rowspan="2" style="width: 70;"><b>Получил кассир</b></td>
              <td align="center" style="width: 70;">_____________</td>
              <td align="center" style="width: 100;"></td>
            </tr>

            <tr align="center" style="font-size: 4pt;">
              <td style="width: 70;">подпись</td>
              <td style="width: 100;">расшифровка подписи</td>
            </tr>

          </table>

        </td></tr>
      </table> <!-- Таблица ордера -->

    </td>

    <td style="width: 20">
      <!-- Перфорация -->
      <img src="test/logo/perforation.gif" />
    </td>

    <td style="width: 200">
      <!-- Квитанция -->
      <!-- Внешняя таблица. Упорядочивает элементы квитанции -->
      <table border="0">

        <tr><td>
          <table border="0">
            <tr><td align="left" style="font-size: 6pt;">{$orgName}</td></tr>
            <tr><td align="left" style="font-size: 6pt;">_________________</td></tr>
            <tr><td align="left" style="font-size: 4pt;">организация</td></tr>
          </table>
        </td></tr>

        <tr><td>
          <p align="left" style="font-size: 6pt;"><b><br />КВИТАНЦИЯ</b></p>
        </td></tr>

        <tr><td>
          <!-- к ПКО от -->
          <table border="0" style="font-size: 6pt;">

            <tr>
              <td align="right" style="width: 30">к ПКО №</td>
              <td align="center" style="width: 120">{$payment_doc_no}</td>
            </tr>

            <tr>
              <td align="right" style="width: 30"></td>
              <td align="center" style="width: 120">_____________</td>
            </tr>

            <tr>
              <td align="right" style="width: 30">от</td>
              <td align="center" style="width: 120">{$payment_pay_date}</td>
            </tr>

            <tr>
              <td align="left" style="width: 30"></td>
              <td align="center" style="width: 120">_____________</td>
            </tr>

          </table>
        </td></tr>

        <tr style="font-size: 6pt;"><td>
          <p><br />Принято от<br />{$payment_payer}</p>
          <p><br />Основание<br />{$payment_pay_details}</p>
        </td></tr>

        <tr><td>
          <table>

            <tr align="left" style="font-size: 6pt;">
              <td rowspan="3">Сумма</td>
              <td><b>{$payment_amount}</b></td>
            </tr>

            <tr align="left" style="font-size: 6pt;">
              <td>____________________</td>
            </tr>

            <tr>
              <td align="left" style="font-size: 4pt;"><b>цифрами</b></td>
            </tr>

          </table>
        </td></tr>

        <tr style="font-size: 6pt;"><td>
          <p><br />{$payment_amount_literal}</p>
          <p><br />В том числе<br />НДС (Без НДС)</p>
        </td></tr>

        <tr align="right" style="font-size: 6pt;"><td style="width: 150;">
          <table>
            <tr><td>
              <b>{$payment_pay_date}</b>
            </td></tr>
            <tr><td>
              <b>_______________</b>
            </td></tr>
          </table>
          <p align="left"><b>М.П. (штампа)<br /></b></p>
        </td></tr>

        <tr><td>

          <table>
            <tr align="left" style="font-size: 6pt;"><td colspan="2">
              <b>Главный бухгалтер</b>
            </td></tr>
            <tr align="center" style="font-size: 6pt; width: 200;">
              <td></td>
              <td>{$orgBookkeeper}</td>
            </tr>
            <tr align="center" style="font-size: 6pt;">
              <td>_____________</td>
              <td>_____________</td>
            </tr>
            <tr align="center" style="font-size: 4pt;">
              <td align="center">подпись</td>
              <td align="center">расшифровка подписи</td>
            </tr>
          </table>

          <table>
            <tr align="left" style="font-size: 6pt;"><td colspan="2">
              <b>Кассир</b>
            </td></tr>
            <tr align="center" style="font-size: 6pt; width: 200">
              <td></td>
              <td></td>
            </tr>
            <tr align="center" style="font-size: 6pt;">
              <td>_____________</td>
              <td>_____________</td>
            </tr>
            <tr align="center" style="font-size: 4pt;">
              <td>подпись</td>
              <td>расшифровка подписи</td>
            </tr>
          </table>

        </td></tr>

      </table>
    </td>

  </tr>
</table>

{/content}';
    Migration_Index_View::ExecuteQuery("UPDATE sp_templates SET template = ? WHERE name = 'Приходный кассовый ордер'", array($order));
}
// End

/* Apply new changes for PBX */
updateVtlibModule('PBXManager', 'packages/vtiger/mandatory/PBXManager.zip');

// SalesPlatform.ru end
?>
