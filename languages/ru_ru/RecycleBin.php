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
	'RecycleBin'                   => 'Корзина'              , 
	'LBL_SELECT_MODULE'            => 'Выберите Модуль', 
	'LBL_EMPTY_RECYCLEBIN'         => 'Очистить Корзину', 
	'LBL_RESTORE'                  => 'Востановить'      , // KEY 5.x: LBL_MASS_RESTORE
	'LBL_NO_PERMITTED_MODULES'     => 'Нет доступных модулей', 
	'LBL_RECORDS_LIST'             => 'Список'            , // TODO: Review
	'LBL_NO_RECORDS_FOUND'         => 'Не найдено записей для востановления в модуле', // KEY 5.x: LBL_EMPTY_MODULE
        'Recycle Bin' => 'Корзина',   
        //SalesPlatform.ru begin localization fix
        'LBL_SEARCH_FOR_MODULES' => 'Поиск для модулей',
        'List' => 'Список',
        //SalesPlatform.ru end localization fix
);
$jsLanguageStrings = array(
	'JS_MSG_EMPTY_RB_CONFIRMATION' => 'Вы уверены, что хотите навсегда удалить из базы данных все удаленные записи?', 
	'JS_LBL_RESTORE_RECORDS_CONFIRMATION' => 'Вы уверены, что хотите восстановить записи?',
    'JS_LBL_RESTORE_RECORD_CONFIRMATION' => 'Вы уверены, что хотите восстановить запись?',
    'JS_RESTORING_RECORDS' => 'Восстановление записей',
    'JS_MASS_DELETE_CONFIRMATION_RB' => 'Вы уверены, что хотите навсегда удалить записи?',
    'JS_DELETE_CONFIRMATION_RB'   => 'Вы уверены, что хотите навсегда удалить запись?',
    'JS_RESTORING_RECORD' => 'Восстановление записи',
    'JS_RESTORE_AND_UNTRASH_FILE_IN_DRIVE' => 'Восстановление в Vtiger и на диске',    
);

// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/RecycleBin.php';
// SalesPlatform.ru end