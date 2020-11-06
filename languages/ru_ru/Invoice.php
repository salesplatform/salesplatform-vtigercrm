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
	'SINGLE_Invoice'               => 'Счет'                    , 
	'LBL_EXPORT_TO_PDF'            => 'Экспорт в PDF:'       , 
	'LBL_SEND_MAIL_PDF'            => 'Отправить PDF по Email:'         , // TODO: Review
	'LBL_ADD_RECORD'               => 'Добавить Счет'                 , // TODO: Review
	'LBL_RECORDS_LIST'             => 'Список Счетов'   , // KEY 5.x: LBL_LIST_FORM_TITLE
	'LBL_INVOICE_INFORMATION'      => 'Информация Счета', 
    // SalesPlatform.ru begin New localization
        'LBL_INVOICE_ADD_ACT' => 'Создать Акт',
	'Sales Order'                  => 'Заказ',
    //'Sales Order'                  => 'Заказ на Продажу',
    // SalesPlatform.ru end
	'Customer No'                  => 'Номер Клиента'   , 
	'Invoice Date'                 => 'Дата'                    , 
    // SalesPlatform.ru begin New localization
	'Purchase Order'               => 'Закупка', 
    //'Purchase Order'               => 'Заказ на Закупку', 
    // SalesPlatform.ru end
	'Sales Commission'             => 'Комиссия'            , 
	'Invoice No'                   => 'Счет №'                , 
	'LBL_RECEIVED'                 => 'Получено'                    , // TODO: Review
	'LBL_BALANCE'                  => 'Баланс'                     , // TODO: Review
	'Sent'                         => 'Отправлен'          , 
	'Credit Invoice'               => 'Просрочен'          , 
	'Paid'                         => 'Оплачен'              , 
	'AutoCreated'                  => 'Автосоздан'        ,
    'Created'                      => 'Создан'              , // KEY 5.x: LBL_CREATED
    'Approved'                     => 'Одобрен',
	'Cancel'                       => 'Отменен'            , // KEY 5.x: LBL_CANCEL_BUTTON_LABEL
    
    'LBL_INVOICE_ADD_CONSIGNMENT'  => 'Создать Реализацию',
        
    //SalesPlatform.ru begin
    'Self Company' => 'Юр. лицо',    
    //SalesPlatform.ru end
    'Invoice'                      => 'Счета',
    'LBL_NOT_A_BUNDLE' => 'Не связаны',
    'LBL_SUB_PRODUCTS'	=> 'Вспомогательные изделия',
    'LBL_ACTION'	=> 'Действие',
    'LBL_THIS' => 'Это',
    'LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM' => 'Удаляется из системы. Пожалуйста, удалите или замените этот элемент',
    'LBL_THIS_LINE_ITEM_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_THIS_LINE_ITEM' => 'Эта позиция удаляется из системы,пожалуйста, удалите эту строку элементов',
    );

// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/Invoice.php';
// SalesPlatform.ru end