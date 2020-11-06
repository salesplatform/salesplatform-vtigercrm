<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
if (!defined('VTIGER_UPGRADE'))
    die('Invalid entry point');
chdir(dirname(__FILE__) . '/../../../');
include_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
include_once 'include/utils/utils.php';

// Install SPPDFTemplates first
installVtlibModule('SPPDFTemplates', "packages/vtiger/optional/SPPDFTemplates.zip");

// 5.2.1-20110411
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `inn` varchar(30) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `kpp` varchar(30) default ''", array());


// 5.2.1-20110506
Migration_Index_View::ExecuteQuery('INSERT INTO `sp_templates` VALUES (1,\'Счет\',\'Invoice\',\'{header}\n\n<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>\n\n<p style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</p>\n\n<div style="font-weight: bold; text-align: center">Образец заполнения платежного поручения</div>\n\n<table border="1" cellpadding="2">\n<tr>\n  <td width="140">ИНН {$orgInn}</td><td width="140">КПП {$orgKpp}</td><td rowspan="2" width="50"><br/><br/>Сч. №</td><td rowspan="2" width="200"><br/><br/>{$orgBankAccount}</td>\n</tr>\n<tr>\n<td colspan="2" width="280"><span style="font-size: 8pt">Получатель</span><br/>{$orgName}</td>\n</tr>\n<tr>\n<td colspan="2" rowspan="2" width="280"><span style="font-size: 8pt">Банк получателя</span><br/>{$orgBankName}</td>\n<td width="50">БИК</td>\n<td rowspan="2" width="200">{$orgBankId}<br/>{$orgCorrAccount}</td>\n</tr>\n<tr>\n<td width="50">Сч. №</td>\n</tr>\n</table>\n<br/>\n<h1 style="text-align: center">СЧЕТ № {$invoice_no} от {$invoice_invoicedate}</h1>\n<br/><br/>\n<table border="0">\n<tr>\n<td width="100">Плательщик:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n<tr>\n<td width="100">Грузополучатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border="1" style="font-size: 8pt" cellpadding="2">\n    <tr style="text-align: center; font-weight: bold">\n	<td width="30">№</td>\n      <td width="260">Наименование<br/>товара</td>\n      <td width="65">Единица<br/>изме-<br/>рения</td>\n      <td width="35">Коли-<br/>чество</td>\n	<td width="70">Цена</td>\n	<td width="70">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width="30">{$productNumber}</td>\n      <td width="260">{$productName} {$productComment}</td>\n	<td width="65" style="text-align: center">{$productUnits}</td>\n	<td width="35" style="text-align: right">{$productQuantityInt}</td>\n	<td width="70" style="text-align: right">{$productPrice}</td>\n	<td width="70" style="text-align: right">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<table border="0" style="font-size: 8pt;font-weight: bold">\n    <tr>\n      <td width="460">\n        <table border="0" cellpadding="2">\n          <tr><td width="460" style="text-align: right">Итого:</td></tr>\n          <tr><td width="460" style="text-align: right">Сумма НДС:</td></tr>\n          <tr><td width="460" style="text-align: right">Всего к оплате:</td></tr>\n        </table>\n      </td>\n      <td width="70">\n        <table border="1" cellpadding="2">\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n        </table>\n      </td>\n  </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n<br/>\n    <p>Руководитель предприятия  __________________ ( {$orgDirector} ) <br/>\n    <br/>\n    Главный бухгалтер  __________________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',110,50,\'P\'),(2,\'Накладная\',\'SalesOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Расходная накладная № {$salesorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$account_accountname}</span></td>\n</tr>\n</table>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\" rowspan=\"2\">№</td>\n	<td width=\"200\" rowspan=\"2\">Товар</td>\n	<td width=\"50\" rowspan=\"2\" colspan=\"2\">Мест</td>\n	<td width=\"60\" rowspan=\"2\" colspan=\"2\">Количество</td>\n	<td width=\"60\" rowspan=\"2\">Цена</td>\n	<td width=\"60\" rowspan=\"2\">Сумма</td>\n	<td width=\"70\">Номер ГТД</td>\n    </tr>\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"70\">Страна<br/>происхождения</td>\n    </tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\" rowspan=\"2\">{$productNumber}</td>\n	<td width=\"200\" rowspan=\"2\">{$productName}</td>\n	<td width=\"25\" rowspan=\"2\"></td>\n	<td width=\"25\" rowspan=\"2\">шт.</td>\n	<td width=\"30\" rowspan=\"2\" style=\"text-align: right\">{$productQuantityInt}</td>\n	<td width=\"30\" rowspan=\"2\">{$productUnits}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"60\" rowspan=\"2\" style=\"text-align: right\">{$productNetTotal}</td>\n	<td width=\"70\">{$customsId}</td>\n    </tr>\n    <tr>\n	<td width=\"70\">{$manufCountry}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Итого:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"400\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <table border=\"0\">\n    <tr>\n	<td>Отпустил  __________ </td><td>Получил  __________ </td>\n    </tr>\n    </table>\n{/ending}\n\',50,0,\'P\'),(3,\'Предложение\',\'Quotes\',\'\n{header}\n\n<p style=\"font-weight: bold\">\n{$orgName}<br/>\nИНН {$orgInn}<br/>\nКПП {$orgKpp}<br/>\n{$orgBillingAddress}<br/>\nТел.: {$orgPhone}<br/>\nФакс: {$orgFax}<br/>\n{$orgWebsite}\n</p>\n\n<h1>Коммерческое предложение № {$quote_no}</h1>\n<p>Действительно до: {$quote_validtill}</p>\n<hr size=\"2\">\n\n<p style=\"font-weight: bold\">\n{$account_accountname}<br/>\n{$billingAddress}\n</p>\n{/header}\n\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n    <tr style=\"text-align: center; font-weight: bold\">\n	<td width=\"30\">№</td>\n	<td width=\"260\">Товары (работы, услуги)</td>\n	<td width=\"70\">Ед.</td>\n	<td width=\"30\">Кол-во</td>\n	<td width=\"70\">Цена</td>\n	<td width=\"70\">Сумма</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width=\"30\">{$productNumber}</td>\n	<td width=\"260\">{$productName}</td>\n	<td width=\"70\">{$productUnits}</td>\n	<td width=\"30\" style=\"text-align: right\">{$productQuantity}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productPrice}</td>\n	<td width=\"70\" style=\"text-align: right\">{$productNetTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<p></p>\n<table border=\"0\">\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Итого:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryNetTotal}</td>\n    </tr>\n    <tr>\n	<td width=\"460\" style=\"text-align: right\">Сумма НДС:</td>\n	<td width=\"70\" style=\"text-align: right\">{$summaryTax}</td>\n    </tr>\n</table>\n\n<p style=\"font-weight: bold\">\nВсего: {$summaryGrandTotal} руб. ( {$summaryGrandTotalLiteral} )\n</p>\n\n{/summary}\n\n{ending}\n    <hr size=\"2\">\n    <p>Руководитель предприятия  __________ ( {$orgDirector} ) <br/>\n    </p>\n{/ending}\n\',85,0,\'P\')', array());
Migration_Index_View::ExecuteQuery('INSERT INTO `sp_templates` VALUES (4,\'Заказ на закупку\',\'PurchaseOrder\',\'{header}\n<h1 style=\"font-size: 14pt\">Заказ на закупку № {$purchaseorder_no}</h1>\n<hr>\n<table border=\"0\" style=\"font-size: 9pt\">\n<tr>\n<td width=\"80\">Поставщик:</td><td width=\"450\"><span style=\"font-weight: bold\">{$vendor_vendorname}</span></td>\n</tr>\n<tr>\n<td width=\"80\">Покупатель:</td><td width=\"450\"><span style=\"font-weight: bold\">{$orgName}</span></td>\n</tr>\n</table>\n{/header}\n{table_head}\n<table border=\"1\" style=\"font-size: 8pt\" cellpadding=\"2\">\n<tr style=\"text-align: center; font-weight: bold\">\n<td width=\"30\">№</td>\n<td width=\"200\">Товар</td>\n<td width=\"60\" colspan=\"2\">Количество</td>\n<td width=\"60\">Цена</td>\n<td width=\"60\">Сумма</td>\n</tr>\n{/table_head}\n{table_row}\n<tr>\n<td width=\"30\">{$productNumber}</td>\n<td width=\"200\">{$productName}</td>\n<td width=\"30\" style=\"text-align: right\">{$productQuantityInt}</td>\n<td width=\"30\">{$productUnits}</td>\n<td width=\"60\" style=\"text-align: right\">{$productPrice}</td>\n<td width=\"60\" style=\"text-align: right\">{$productNetTotal}</td>\n</tr>\n{/table_row}\n{summary}\n</table>\n<p></p>\n<table border=\"0\" style=\"font-weight: bold\">\n<tr>\n<td width=\"350\" style=\"text-align: right\">Итого:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryNetTotal}</td>\n</tr>\n<tr>\n<td width=\"350\" style=\"text-align: right\">Сумма НДС:</td>\n<td width=\"60\" style=\"text-align: right\">{$summaryTax}</td>\n</tr>\n</table>\n<p>\nВсего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>\n<span style=\"font-weight: bold\">{$summaryGrandTotalLiteral}</span>\n</p>\n{/summary}\n{ending}\n{/ending}\',50,0,\'P\')', array());
Migration_Index_View::ExecuteQuery("create table if not exists sp_templates_seq (id int(11) not NULL) ENGINE=InnoDB  DEFAULT CHARSET=utf8", array());
Migration_Index_View::ExecuteQuery('INSERT INTO sp_templates_seq (id) VALUES (4)', array());

// Install all SP modules except SPKladr
installVtlibModule('Act', 'packages/vtiger/optional/Act.zip');
installVtlibModule('Consignment', "packages/vtiger/optional/Consignment.zip");
installVtlibModule('SPPayments', "packages/vtiger/optional/SPPayments.zip");
installVtlibModule('SPCMLConnector', "packages/vtiger/optional/SPCMLConnector.zip");
installVtlibModule('SPSocialConnector', "packages/vtiger/optional/SPSocialConnector.zip");
installVtlibModule('SPUnits', "packages/vtiger/optional/SPUnits.zip");

require_once 'vtlib/Vtiger/Module.php';

$module = Vtiger_Module::getInstance('Accounts');
if ($module) {
    $blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $module);
    if ($blockInstance) {
        $innField = Vtiger_Field::getInstance('inn', $module);
        if(!$innField) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'inn';
            $fieldInstance->table = 'vtiger_account';
            $fieldInstance->label = 'INN';
            $fieldInstance->uitype = 1;
            $fieldInstance->column = $fieldInstance->name;
            $fieldInstance->columntype = 'VARCHAR(30)';
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }
        $kppField = Vtiger_Field::getInstance('kpp', $module);
        if(!$kppField) {
            $fieldInstance1 = new Vtiger_Field();
            $fieldInstance1->name = 'kpp';
            $fieldInstance->table = 'vtiger_account';
            $fieldInstance1->label = 'KPP';
            $fieldInstance1->uitype = 1;
            $fieldInstance1->column = $fieldInstance1->name;
            $fieldInstance1->columntype = 'VARCHAR(30)';
            $fieldInstance1->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance1);
        }
    }
}

$module = Vtiger_Module::getInstance('Products');
if ($module) {
    $blockInstance = Vtiger_Block::getInstance('LBL_PRODUCT_INFORMATION', $module);
    if ($blockInstance) {
        $innField = Vtiger_Field::getInstance('manuf_country', $module);
        if(!$innField) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'manuf_country';
            $fieldInstance->table = 'vtiger_products';
            $fieldInstance->label = 'Manuf. Country';
            $fieldInstance->uitype = 1;
            $fieldInstance->column = $fieldInstance->name;
            $fieldInstance->columntype = 'VARCHAR(100)';
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }
        $kppField = Vtiger_Field::getInstance('customs_id', $module);
        if(!$kppField) {
            $fieldInstance1 = new Vtiger_Field();
            $fieldInstance1->name = 'customs_id';
            $fieldInstance->table = 'vtiger_products';
            $fieldInstance1->label = 'Customs ID';
            $fieldInstance1->uitype = 1;
            $fieldInstance1->column = $fieldInstance1->name;
            $fieldInstance1->columntype = 'VARCHAR(100)';
            $fieldInstance1->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance1);
        }
    }
}

Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add column `bankaccount` varchar(1024) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `bankname` varchar(1024) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `bankid` varchar(30) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `corraccount` varchar(100) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `director` varchar(100) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `bookkeeper` varchar(100) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `entrepreneur` varchar(100) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `entrepreneurreg` varchar(100) default ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_organizationdetails` add `okpo` varchar(100) default ''", array());

// 5.2.1-20110824
Migration_Index_View::ExecuteQuery("alter table `vtiger_systems` add `server_tls` varchar(20) default NULL", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_systems` add `from_name` varchar(200) DEFAULT ''", array());
Migration_Index_View::ExecuteQuery("alter table `vtiger_systems` add `use_sendmail` varchar(5) DEFAULT 'false'", array());

// 5.3.0-201207
// Add units code to Products
$productsModuleInstance = Vtiger_Module::getInstance('Products');
$blockInstance = Vtiger_Block::getInstance('LBL_STOCK_INFORMATION', $productsModuleInstance);
$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'unit_code';
$fieldInstance->label = 'Unit Code';
$fieldInstance->table = 'vtiger_products';
$fieldInstance->column = 'unit_code';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 1;
$blockInstance->addField($fieldInstance);

// Add units code to Services
$servicesModuleInstance = Vtiger_Module::getInstance('Services');
$blockInstance = Vtiger_Block::getInstance('LBL_SERVICE_INFORMATION', $servicesModuleInstance);
$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'unit_code';
$fieldInstance->label = 'Unit Code';
$fieldInstance->table = 'vtiger_service';
$fieldInstance->column = 'unit_code';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 1;
$blockInstance->addField($fieldInstance);

// Create sp_custom_reports table
Migration_Index_View::ExecuteQuery("CREATE TABLE IF NOT EXISTS sp_custom_reports (
    reporttype varchar(50) NOT NULL,
    datefilter int(1) default 0,
    ownerfilter int(1) default 0,
    accountfilter int(1) default 0,
    functionname varchar(255) NOT NULL,
    PRIMARY KEY  (reporttype)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci", array());
