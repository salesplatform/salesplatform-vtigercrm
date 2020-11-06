<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$languageStrings = array(
	'SINGLE_PurchaseOrder'         => 'Закупка', 
	'LBL_EXPORT_TO_PDF'            => 'Экспорт в PDF:'       , 
	'LBL_SEND_MAIL_PDF'            => 'Отправить PDF по Email:'         , // TODO: Review
	'LBL_ADD_RECORD'               => 'Добавить Закупку'          , // TODO: Review
	'LBL_RECORDS_LIST'             => 'Список Закупок', // KEY 5.x: LBL_LIST_FORM_TITLE
	'LBL_COPY_SHIPPING_ADDRESS'    => 'Копировать фактический адрес в юридический'       , // TODO: Review
	'LBL_COPY_BILLING_ADDRESS'     => 'Копировать юридический адрес в фактический'        , // TODO: Review
	'LBL_PO_INFORMATION'           => 'Закупка', 
	'PurchaseOrder No'             => 'Закупка №', 
	'Requisition No'               => '№'              , 
	'Tracking Number'              => 'Отслеживаемый номер', 
	'Sales Commission'             => 'Комиссия'            ,
	'LBL_PAID'                     => 'Оплачено'                        , // TODO: Review
	'LBL_BALANCE'                  => 'Баланс'                     , // TODO: Review
	'Received Shipment'            => 'Получено с доставкой', 
        'LBL_COPY_COMPANY_ADDRESS'     => 'Копировать адрес компании',
        'PurchaseOrder'         => 'Закупки', 
        'LBL_LIST_PRICE'               => 'Прайс-лист',
        'List Price'                   => 'Прайс-лист',
        'LBL_COPY_ACCOUNT_ADDRESS' => 'Копировать адрес организации',
	'LBL_SELECT_ADDRESS_OPTION' => 'Выберите адрес для копирования',
	'LBL_BILLING_ADDRESS' => 'Адрес для выставления счета',
	'LBL_COMPANY_ADDRESS' => 'Адрес компании',
	'LBL_ACCOUNT_ADDRESS' => 'Адрес организации',
	'LBL_VENDOR_ADDRESS' => 'Адрес поставщика',
	'LBL_CONTACT_ADDRESS' => 'Контактный адрес!',
        'LBL_THIS' => 'Это',
        'LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM' => 'удаляется из системы. Удалите или замените этот элемент',
        'LBL_THIS_LINE_ITEM_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_THIS_LINE_ITEM' => 'Эта позиция удаляется из системы,пожалуйста, удалите эту строку элементов',        
);

$jsLanguageStrings = array(
    'JS_ORGANIZATION_NOT_FOUND'=> 'Организация пуста!',
    'JS_ORGANIZATION_NOT_FOUND_MESSAGE'=> 'Пожалуйста, выберите организацию, прежде чем копировать адрес',
	'JS_ACCOUNT_NOT_FOUND' => 'Организация пуста!',
	'JS_ACCOUNT_NOT_FOUND_MESSAGE' =>  'Пожалуйста, выберите организацию, прежде чем копировать адрес',
	'JS_VENDOR_NOT_FOUND' => 'Поставщик пуст', 
	'JS_VENDOR_NOT_FOUND_MESSAGE' => 'Пожалуйста, выберите поставщика, прежде чем копировать адрес',
	'JS_CONTACT_NOT_FOUND' => 'Контакт пуст', 
	'JS_CONTACT_NOT_FOUND_MESSAGE' =>  'Пожалуйста, выберите контакт, прежде чем копировать адрес',

  'JS_PLEASE_REMOVE_LINE_ITEM_THAT_IS_DELETED' => 'Пожалуйста, удалите элемент строки',

);
// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/PurchaseOrder.php';
// SalesPlatform.ru end
