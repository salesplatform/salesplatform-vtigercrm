<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * All Rights Reserved.
 * Description: Defines the Russian language pack.
 * The Initial Translator is Eugene Babiy (eugene.babiy@gmail.com).
 * This Language Pack modified and supported by SalesPlatform Ltd
 * SalesPlatform vtiger CRM Russian Community: http://community.salesplatform.ru/
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
$languageStrings = Array (
    'SPSocialConnector'                         =>  'Сообщения',

    'Assigned To'                               =>  'Кому назначено',
    'Created Time'                              =>  'Время создания',
    'Modified Time'                             =>  'Время изменения',
    'message'                                   =>  'Сообщение',
    'Date Time'                                 =>  'Дата и время отправки',
    'SINGLE_SPSocialConnector'                  =>  ' ',

    'LBL_SPSOCIALCONNECTOR_INFORMATION'         =>  'Информация',
    'StatusInformation'                         =>  'Информация статуса',
    'LBL_CHECK_STATUS'                          =>  'Проверить статус',

    'LBL_ADD_MORE_FIELDS'                       =>  'Добавьте URL',
    'LBL_STEP_1'                                =>  'Шаг 1',
    'LBL_STEP_2'                                =>  'Шаг 2',
    'LBL_WRITE_YOUR_MESSAGE_HERE'               =>  'Текст сообщения',

    'LBL_LOAD_PROFILE'                          =>  'Загрузить',

    'Please select the URL to send the message' =>  'Выберите URL для отправки сообщения',

    'Compose message'                           =>  'Отправка сообщения',
    'Message'                                   =>  'Введите текст сообщения',

    // for SPSocialConnectorEnterUrlWizard.tpl
    'Import'                                    =>  'Импорт данных',
    'Enter URL'                                 =>  'Введите URL профиля в социальной сети',

    // for SPSocialConnectorSendMsg.tpl
    'Error: empty URL field'                    =>  'Ошибка: поле URL не заполнено',
    'Status'                                    =>  'Статус отправки',

     // Error messages
    'Unspecified error.'                        =>  'Неизвестная ошибка',
    'Hybriauth configuration error.'            =>  'Ошибка в настройках библиотеки HybridAuth',
    'Provider not properly configured.'         =>  'Нет настроек для данной социальной сети',
    'Unknown or disabled provider.'             =>  'Неизвестная или неподключенная социальная сеть',
    'Missing provider application credentials.' =>  'Отсутствуют настройки для социальной сети',
    'Authentification failed. The user has canceled the authentication or the provider refused the connection.' =>  'Неудачная аутентификация. Пользователь отменил аутентификацию или социальная сеть оборвала соединение',
    'User profile request failed. Most likely the user is not connected to the provider and he should authenticate again.'  =>  'Неудачный запрос к профилю пользователя. Скорее всего пользователь не подключен к социальной сети и он должен аутентифицироваться заново.',
    'User not connected to the provider.'       =>  'Пользователь не подключен к социальной сети',
    'Provider does not support this feature.'   =>  'Социальная сеть не поддерживает данную функцию',

    // for SPSocialConnectorLoadProfile.tpl
    'Profile'                                   =>  'Профиль пользователя',
    'First name:'                               =>  'Имя:',
    'Last name:'                                =>  'Фамилия:',
    'Social net:'                               =>  'Социальная сеть:',
    'User ID:'                                  =>  'ID пользователя:',
    'Web site:'                                 =>  'Личный сайт',
    'Birthday:'                                 =>  'Дата рождения:',
    'Gender:'                                   =>  'Пол',
    'male'                                      =>  'мужской',
    'female'                                    =>  'женский',
    'e-mail:'                                   =>  'E-mail',
    'Mobile phone:'                             =>  'Мобильный телефон:',
    'Home phone:'                               =>  'Домашний телефон:',
    'Region:'                                   =>  'Место проживания:',

    // for SPSocialConnectorLoadProfile.tpl
    'Changes'                                   =>  'Изменения',
    'The following fields have been changed'    =>  'Следующие поля были изменены',
    'No changed fields'                         =>  'Нет изменных полей',
    // ----------------- if module Contacts -----------------
    'firstname'                                 =>  'Имя',
    'lastname'                                  =>  'Фамилия',
    'birthday'                                  =>  'День рождения ',
    'email'                                     =>  'Адрес E-mail',
    'mobile'                                    =>  'Мобильный тел.',
    'homephone'                                 =>  'Домашний тел.',
    'mailingcity'                               =>  'Город',
    'mailingcountry'                            =>  'Страна',
    // ----------------- if module Leads -----------------
    'phone'                                     =>  'Телефон',
    'city'                                      =>  'Город',
    'country'                                   =>  'Страна',
    'website'                                   =>  'Веб-сайт',
    // ----------------- if module Accounts -----------------
    'otherphone'                                =>  'Доп. тел.',
    'ship_city'                                 =>  'Ф/А Город',
    'ship_country'                              =>  'Ф/А Страна',
    'email1'                                    =>  'Адрес Email',

    // ----------------- Detail View -----------------
    'Sent'                                      =>  'Сообщение отправлено',
    'Not sent'                                  =>  'Сообщение не отправлено',
    'Incoming'                                  =>  'Входящее',
    'Outbound'                                  =>  'Исходящее',
    'Import result'                             =>  'Импортировано',
    'Messages'                                  =>  'сообщений',

    'Social net'                                =>  'Социальная сеть',
    'Comment'                                   =>  'Комментарий',
    'Field'                                     =>  'Поле',
    'New value'                                 =>  'Новое значение',
    'Vkontakte Message ID' => 'ID сообщения Vkontakte',
    'Twitter Message ID' => 'ID сообщения Twitter'
);

// SalesPlatform.ru begin SPConfiguration fix
include 'renamed/SPSocialConnector.php';
// SalesPlatform.ru end

?>
