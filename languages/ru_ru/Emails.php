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
	'SINGLE_Emails'                => 'E-mail'                  ,
	'Emails'                       => 'Сообщения E-mail'   , 
	'LBL_SELECT_EMAIL_IDS'         => 'Выберите E-mail адреса'      , // TODO: Review
	'LBL_SUBJECT'                  => 'Тема:'                   , 
	'LBL_ATTACHMENT'               => 'Вложение'            , 
	'LBL_BROWSE_CRM'               => 'Выбрать Документ'                  , // TODO: Review
	'LBL_SEND'                     => 'Отправить'          , 
	'LBL_SAVE_AS_DRAFT'            => 'Сохранить как черновик'               , // TODO: Review
	'LBL_GO_TO_PREVIEW'            => 'Перейти к предварительному просмотру'               , // TODO: Review
	'LBL_SELECT_EMAIL_TEMPLATE'    => 'Выбрать шаблон письма', // KEY 5.x: LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE
	'LBL_COMPOSE_EMAIL'            => 'Создать E-Mail'       , 
	'LBL_TO'                       => 'Кому:'                   , 
	'LBL_CC'                       => 'Копия:'                , 
	'LBL_BCC'                      => 'Скрытая копия:' , 
	'LBL_ADD_CC'                   => 'Добавить копию'                      , // TODO: Review
	'LBL_ADD_BCC'                  => 'Добавить скрытую копию'                     , // TODO: Review
	'LBL_MAX_UPLOAD_SIZE'          => 'Максимальный размер загружаемого файла'      , // TODO: Review
	'LBL_EXCEEDED'                 => 'Превышен'                    , // TODO: Review
	'LBL_FORWARD'                  => 'Переслать'          , // KEY 5.x: LBL_FORWARD_BUTTON
	'LBL_PRINT'                    => 'Печать'                , // KEY 5.x: LBL_PRINT_EMAIL
	'LBL_DESCRIPTION'              => 'Описание'            , 
	'LBL_FROM'                     => 'От:'                      , 
	'LBL_INFO'                     => 'Информация'                        , // TODO: Review
        'LBL_DRAFT'                    => 'Черновик',
	'LBL_DRAFTED_ON'               => 'Составлено на', 
	'LBL_SENT_ON'                  => 'Отправлено'                     , // TODO: Review
	'LBL_OWNER'                    => 'Ответственный'                       , // TODO: Review
	'Date & Time Sent'             => 'Отправлено'        ,
        'Parent ID'                    => 'Связан с',
        'Owner'                        => 'Ответственный',
        'Access Count'                 => 'Число просмотров',
        'Time Start'                   => 'Время начала',
    
        //SalesPlatform.ru begin add locale
        'LBL_ATTACHED' => 'Прикреплено',
        'LBL_DRAFT' => 'Черновик',
        'LBL_MAIL_DATE' => 'Дата письма',
        'LBL_EMAIL_INFORMATION' => 'Информация о E-Mail сообщении',
        'Emails_Block1' => 'Описание',
        'Emails_Block2' => 'Подробности Сообщения',
        'Emails_Block3' => 'Текст Сообщения',
        'Date_Sent'     => 'Отправлено',        
        //SalesPlatform.ru end
        'LBL_EMAILTEMPLATE_WARNING'    => 'Правильны ли ваши метки слияния',
        'LBL_EMAILTEMPLATE_WARNING_CONTENT' => 'Пожалуйста, убедитесь, что выбранный шаблон имеет метки слияния, относящиеся к записи получателя. 
                                            Если вы отправляете электронное письмо на Обращение, но теги слияния принадлежат модулю Контакты (например: $contacts-lastname$), 
                                            то значения не будут объединены.', 
        'Draft'                        => 'Черновик',
    );

$jsLanguageStrings = array(
    'JS_WARNING' => 'Предупреждение',
); 
// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/Emails.php';
// SalesPlatform.ru end