<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

if (defined('VTIGER_UPGRADE')) {
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_mail_accounts ADD COLUMN trash_folder varchar(50) AFTER sent_folder', array());
    
    /* Fix migration from 5.4.0 version */
    updateVtlibModule('SPCMLConnector', 'packages/vtiger/optional/SPCMLConnector.zip');
    updateVtlibModule('SPSocialConnector', 'packages/vtiger/optional/SPSocialConnector.zip');
    
    $moduleInstance = Vtiger_Module_Model::getInstance("Accounts");
    if($moduleInstance && !$moduleInstance->getField("splastsms")) {
        $block = Vtiger_Block_Model::getInstance('LBL_ACCOUNT_INFORMATION', $moduleInstance);
        if($block) {
            $lastSmsField = new Vtiger_Field();
            $lastSmsField->name = 'splastsms';
            $lastSmsField->label = 'Last SMS Date';
            $lastSmsField->table ='vtiger_account';
            $lastSmsField->column = 'splastsms';
            $lastSmsField->columntype = 'datetime';
            $lastSmsField->typeofdata = 'DT~O';
            $lastSmsField->uitype = '70';
            $lastSmsField->masseditable = '1';
            $lastSmsField->readonly = 1;
            $lastSmsField->presence = 0;

            $block->addField($lastSmsField); 
        }
        
    }
    
    $moduleInstance = Vtiger_Module_Model::getInstance("Contacts");
    if($moduleInstance && !$moduleInstance->getField("splastsms")) {
        $block = Vtiger_Block_Model::getInstance('LBL_CONTACT_INFORMATION', $moduleInstance);
        if($block) {
            $lastSmsField = new Vtiger_Field();
            $lastSmsField->name = 'splastsms';
            $lastSmsField->label = 'Last SMS Date';
            $lastSmsField->table ='vtiger_contactdetails';
            $lastSmsField->column = 'splastsms';
            $lastSmsField->columntype = 'datetime';
            $lastSmsField->typeofdata = 'DT~O';
            $lastSmsField->uitype = '70';
            $lastSmsField->masseditable = '1';
            $lastSmsField->readonly = 1;
            $lastSmsField->presence = 0;

            $block->addField($lastSmsField); 
        }
    }
    
    $moduleInstance = Vtiger_Module_Model::getInstance("Leads");
    if($moduleInstance && !$moduleInstance->getField("splastsms")) {
        $block = Vtiger_Block_Model::getInstance('LBL_LEAD_INFORMATION', $moduleInstance);
        if($block) {
            $lastSmsField = new Vtiger_Field();
            $lastSmsField->name = 'splastsms';
            $lastSmsField->label = 'Last SMS Date';
            $lastSmsField->table ='vtiger_leaddetails';
            $lastSmsField->column = 'splastsms';
            $lastSmsField->columntype = 'datetime';
            $lastSmsField->typeofdata = 'DT~O';
            $lastSmsField->uitype = '70';
            $lastSmsField->masseditable = '1';
            $lastSmsField->readonly = 1;
            $lastSmsField->presence = 0;

            $block->addField($lastSmsField); 
        }
    }    
    /* End fix migration 5.4.0 version */
    
    Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET quickcreate=2 WHERE tabid=(SELECT tabid FROM vtiger_tab WHERE name='SPPayments') AND columnname='amount'", array());
    
    $moduleInstance = Vtiger_Module_Model::getInstance("Products");
    if($moduleInstance && !$moduleInstance->getField("sp_product_international_code")) {
        $block = Vtiger_Block_Model::getInstance('LBL_PRODUCT_INFORMATION', $moduleInstance);
        if($block) {
            $internationalCodeField = new Vtiger_Field();
            $internationalCodeField->name = 'sp_product_international_code';
            $internationalCodeField->label = 'International code';
            $internationalCodeField->table ='vtiger_products';
            $internationalCodeField->column = 'sp_product_international_code';
            $internationalCodeField->columntype = 'VARCHAR(255)';
            $internationalCodeField->typeofdata = 'V~O';
            $internationalCodeField->uitype = '1';
            $internationalCodeField->masseditable = '1';
            $internationalCodeField->readonly = 1;
            $internationalCodeField->presence = 0;

            $block->addField($internationalCodeField); 
        }
    }
    
    //SalesPlatform.ru begin initializing the field with uitype 19 with the CKEditor editor
    $db = PearDatabase::getInstance();
    $db->pquery('UPDATE vtiger_field SET typeofdata=? WHERE columnname=?', array('V~O~CKE', 'notecontent'));
    //SalesPlatform.ru end initializing the field with uitype 19 with the CKEditor editor
    
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
Валюта: наименование, код Российский рубль, 643<br/>
Идентификатор государственного контракта, договора (соглашения) (при наличии)</p>
{/header}

{table_head}
<table border="1" style="font-size: 6pt" cellpadding="2">
    <tr style="text-align: center">
	<td width="130" valign="middle" rowspan="2">Наименование товара (описание<br/>выполненных работ, оказанных услуг),<br/>имущественного права</td>
    <td width="40" valign="middle" rowspan="2">Код<br /> вида<br /> товара</td>
	<td width="77" valign="middle" colspan="2">Единица<br/>измерения</td>
        <td width="45" valign="middle" rowspan="2">Коли-<br/>чество<br/>(объем)</td>
	<td width="55" valign="middle" rowspan="2">Цена (тариф)<br/>за единицу<br/>измерения</td>
	<td width="60" valign="middle" rowspan="2">Стоимость товаров (работ,<br/>услуг),<br/>имущественных<br/>прав, без налога -<br/>всего</td>
	<td width="35" valign="middle" rowspan="2">В том<br/>числе<br/>сумма<br/>акциза</td>
	<td width="55" valign="middle" rowspan="2">Налоговая<br/>ставка</td>
        <td width="55" valign="middle" rowspan="2">Сумма<br/>налога<br/>предъяв-<br/>ляемая<br/>покупателю</td>
	<td width="70" valign="middle" rowspan="2">Стоимость товаров<br/>(работ, услуг),<br/>имущественных<br/>прав, с налогом -<br/>всего</td>
	<td width="85" valign="middle" colspan="2">Страна<br/>происхождения товара</td>
    <td width="75" valign="middle" rowspan="2">Регистрационный<br/> номер<br/>таможенной<br/>декларации</td>
	</tr>
        <tr style="text-align: center">
	  <td width="27" valign="middle">код</td>
          <td width="50" valign="middle">условное<br/>обозначение<br/>(национальное)</td>
	  <td width="35" valign="middle">цифровой<br/>код</td>
	  <td width="50" valign="middle">краткое<br/>наименование</td>
	</tr>
    <tr style="text-align: center">
	<td width="130">1</td>
    <td width="40">1а</td>
	<td width="27">2</td>
	<td width="50">2а</td>
	<td width="45">3</td>
	<td width="55">4</td>
	<td width="60">5</td>
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
	<td width="130" style="padding: 3px">{$productName}</td>
    <td width="40" style="text-align: center; padding: 3px;">{$internatonalCode}</td>  
	<td width="27" style="text-align: center;padding: 3px">{$productUnitsCode}</td>
	<td width="50" style="text-align: center;padding: 3px">{$productUnits}</td>
	<td width="45" style="text-align: right;padding: 3px">{$productQuantity}</td>
	<td width="55" style="text-align: right;padding: 3px">{$productPrice}</td>
	<td width="60" style="text-align: right;padding: 3px">{$productNetTotal}</td>
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
	<td width="347" colspan="7"><span style="font-weight: bold">Всего к оплате</span></td>
	<td width="60" style="text-align: right;padding: 3px"><span style="font-weight: bold">{$summaryNetTotal}</span></td>
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
  <td width="200" style="text-align: right">Индивидуальный предприниматель<br/> или иное уполномоченное лицо</td>
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
}

