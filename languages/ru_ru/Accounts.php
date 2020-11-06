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
	'Accounts'                     => 'Контрагенты'      , 
	'SINGLE_Accounts'              => 'Контрагент'        , 
	'LBL_ADD_RECORD'               => 'Добавить Контрагента'            , // TODO: Review
	'LBL_RECORDS_LIST'             => 'Список Контрагентов', // KEY 5.x: LBL_LIST_FORM_TITLE
	'LBL_ACCOUNT_INFORMATION'      => 'Информация о Контрагенте', 
	'LBL_SHOW_ACCOUNT_HIERARCHY'   => 'Иерархия Контрагента', 
	'industry'                     => 'Отрасль'              , 
	'Account Name'                 => 'Контрагент'        , 
	'Account No'                   => 'Контрагент №'    , 
	'Website'                      => 'Веб-сайт'             , 
	'Ticker Symbol'                => 'Краткое название', 
	'Member Of'                    => 'Входит в группу', 
	'Employees'                    => 'Кол-во сотрудников', 
	'Ownership'                    => 'Форма'                  , 
	'SIC Code'                     => 'Код по классификатору', 
	'Other Email'                  => 'Доп. E-mail'                , 
	'Analyst'                      => 'Аналитик'            , 
	'Competitor'                   => 'Конкурент'          , 
	'Customer'                     => 'Клиент'                , 
	'Integrator'                   => 'Интегратор'        , 
	'Investor'                     => 'Инвестор'            , 
	'Press'                        => 'Пресса'                , 
	'Prospect'                     => 'Перспективный'  , 
	'Reseller'                     => 'Посредник'          , 
        'Type'                         => 'Тип'                  ,
	'LBL_START_DATE'               => 'Дата начала'       , 
	'LBL_END_DATE'                 => 'Дата окончания' , 
	'LBL_DUPLICATES_EXIST'         => 'Такой клиент уже существует.', // TODO: Review
	'LBL_COPY_SHIPPING_ADDRESS'    => 'Копировать фактический адрес в юридический'       , // TODO: Review
	'LBL_COPY_BILLING_ADDRESS'     => 'Копировать юридический адрес в фактический'        , // TODO: Review
    'LBL_DUPLICATES_EXIST'         => 'Такой клиент уже существует.',
    
    // SalesPlatform.ru begin New fields
	'INN'                          => 'ИНН',
	'KPP'                          => 'КПП',
    'LBL_SETUP_WEBFORMS'           => 'Установка Веб-Форм',
    // SalesPlatform.ru end
    'Type'                         => 'Тип' ,
    'LBL_IMAGE_INFORMATION' => 'Изображение профиля',
    'Organization Image' => 'Изображение организации',
    'Other Phone' => 'Другой тел.',
    'Phone' => 'Основной телефон',
    'Email' => 'Основной адрес электронной почты',
);
$jsLanguageStrings = array(
	'LBL_RELATED_RECORD_DELETE_CONFIRMATION' => 'Вы уверены, что хотите удалить запись?', // TODO: Review
	'LBL_DELETE_CONFIRMATION'      => 'Удаление записи повлечет за собой удаление связанных записей модулей Сделки и Предложения. Вы уверены, что хотите удалить данную запись?', // TODO: Review
	'LBL_MASS_DELETE_CONFIRMATION' => 'Удаление записей повлечет за собой удаление связанных записей модулей Сделки и Предложения. Вы уверены, что хотите удалить данные записи?', // TODO: Review
	'JS_DUPLICTAE_CREATION_CONFIRMATION' => 'Такой клиент уже существует. Создать дубликат?', // TODO: Review
);

// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/Accounts.php';
// SalesPlatform.ru end