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
	'Asterisk'                     => 'Asterisk',
	'PBXManager'                   => 'Звонки',
	'SINGLE_PBXManager'            => 'Звонок',
	'LBL_CALL_INFORMATION'         => 'Информиация о Звонке',
	'Call From'                    => 'Звонок от',
	'Call To'                      => 'Звонок',
	'Time Of Call'                 => 'Время Звонка',
	'PBXManager ID'                => 'ID записи Звонка',
    
    //Blocks
    'LBL_PBXMANAGER_INFORMATION'   => 'Детали Звонка',
    'LBL_CUSTOM_INFORMATION'       => 'Информация',
    
    // list view settings links
    'LBL_SERVER_CONFIGURATION' => 'Настройка конфигурации',
    
    //Detail view header title
    'LBL_CALL_FROM' => 'Звонок от',
    'LBL_CALL_TO' => 'Звонок',
    
    //Incoming call pop-up 
    'LBL_HIDDEN' => 'Скрытый', 
  
    // Fields
    'Total Duration' => 'Длительность (сек)',
    'Recording URL' => 'Запись Звонка',
    
    'SINGLE_PBXManager' => 'Детали Звонка' ,
    'Call Status' => 'Статус',
    'Customer Number' => 'Телефон клиента',
    'Customer' => 'Клиент',
    'User' => 'Пользователь',
    'Start Time' => 'Время начала Звонка',
    'Office Phone' => 'Рабочий телефон',
    'Direction' => 'Тип Звонка',
    'Bill Duration' => 'Время разговора (сек)',
    'Gateway' => 'Имя шлюза',
    'Customer Type' => 'Тип клиента',
    'End Time' => 'Время завершения Звонка',
    'Source UUID' => 'Идентификатор источника',

    //SalesPlatform.ru begin
    'inbound' => 'Входящий',
    'outbound' => 'Исходящий',
    'ringing' => 'Звонок',
    'in-progress' => 'В прогрессе',
    'completed' => 'Завершен',
    'busy' => 'Занято',
    'no-answer' => 'Нет ответа',
    'Answered elsewhere' => 'Ответили в другом месте',
    'No user responding' => 'Нет ответа',
    'Circuit/channel congestion' => 'Линия занята',
    'Incoming Line Name' => 'Входящая линия',
    //SalesPlatform.ru end
    
    //SalesPlatform.ru begin
    'Recording url' => 'Ссылка на запись разговора (ВАТС)',
    'Is recorded' => 'Запись разговора сохранена (ВАТС)',
    'Recorder call id' => 'Идентификатор записи (ВАТС)',
    'Provider' => 'ВАТС',
    'Status code' => 'Внутрениий статус (ВАТС)',
    'From number' => 'Звонок от (ВАТС)',
    'To number' => 'Номер, на который позвонили (ВАТС)',
    //SalesPlatform.ru end
);

//SalesPlatform.ru begin
$jsLanguageStrings = array(
    'Enter Email-id' => 'Введите E-mail',
    'Select' => 'Выберите',
    'Save' => 'Сохранить'
);
//SalesPlatform.ru end

// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/PBXManager.php';
// SalesPlatform.ru end