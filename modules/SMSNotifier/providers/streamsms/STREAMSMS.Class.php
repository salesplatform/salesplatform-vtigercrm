<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
/*******************************************************************************

Функции формирования XML-POST-запросов на сервер http://gateway.api.sc/xml/

Для формирования запросов используется библиотека cURL из PHP.
(на PHP серевре должны быть разрешены исходящие запросы и установлен
модуль cURL).

Пример использования - в конце

*******************************************************************************/

Class STREAMSMS {

    /**
    * GetMessageStatus - Расшифровка статуса сообщения
    *
    * @param $status string Статус сообщения
    * @return string Расшифровка статуса сообщения
    */
    function GetMessageStatus($status) {
        switch($status) {
            case 'partly_deliver':
                $msg = 'Сообщение доставлено на сервер';
                break;
            case 'send':
                $msg = 'Сообщение передано в мобильную сеть';
                break;
            case 'deliver':
                $msg = 'Сообщение доставлено получателю';
                break;
            case 'not_deliver':
                $msg = 'Ошибка: сообщение отклонено';
                break;
            case 'expired':
                $msg = 'Ошибка: истек срок жизни сообщения';
                break;
            default:
                $msg = 'Статус не распознан';
                break;
        }
        return $msg;
    }

    /**
     * SendToServer - отправка запроса на сервер через cURL
     *
     * @param $xml_data string XML-запрос к серверу (SOAP)
     * @param $URL
     * @return string XML-ответ от сервера (SOAP)
     */
    function SendToServer($xml_data, $URL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);

        if(curl_errno($ch)) {
            die("Error: ".curl_error($ch));
        } else {
            curl_close($ch);
            return $data;
        }
    }

    /**
    * SendTextMessage - передача простого текстового SMS-сообщения
    *
    * @param $login string Логин пользователя
    * @param $password string Пароль пользователя
    * @param $destinationAddress string Мобильный телефонный номер получателя сообщения, в международном формате: код страны + код сети + номер телефона. Пример: 7903123456
    * @param $messageData string Текст сообщения, поддерживаемые кодировки IA5 и UCS2
    * @param $sourceAddress string Адрес отправителя сообщения. До 11 латинских символов или до 15 цифровых
    * @return array("Ответ сервера" => (string), "ID сообщения" => (decimal)) Ответ сервера в виде массива данных
    */
    function SendTextMessage($login, $password, $destinationAddress, $messageData, $sourceAddress) {
        $xml_data = '<?xml version="1.0" encoding="UTF-8"?>
            <request>
                <security>
                    <login value="'.$login.'" />
                    <password value="'.$password.'" />
                </security>
                <message>
                    <sender>'.$sourceAddress.'</sender>
                    <text>'.$messageData.'</text>
                    <abonent phone="'.$destinationAddress.'"/>
                </message>
            </request>';

        $data = $this->SendToServer($xml_data, 'http://gateway.api.sc/xml/');

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);

        if($results[1]['tag'] == 'ERROR') {
            return array(
                'Ответ сервера' => $results[1]['value'],
                'ID сообщения' => ''
            );
        } else {
            return array(
                'Ответ сервера' => 'Операция выполнена',
                'ID сообщения' => $results[1]['attributes']['ID_SMS']
            );
        }
    }

    /**
    * GetMessageState – запрос на получение статус отправленного SMS-сообщения
    *
    * @param $login string Логин пользователя
    * @param $password string Пароль пользователя
    * @param $messageId string Идентификатор сообщения
    * @return array("Ответ сервера" => (string), "Отчёт получен" => (string), "Статус сообщения" => (string))
    */
    function GetMessageState($login, $password, $messageId) {
        $xml_data = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <security>
                    <login value="'.$login.'" />
                    <password value="'.$password.'" />
                </security>
                <get_state>
                    <id_sms>'.$messageId.'</id_sms>
                </get_state>
            </request>';

        $data = $this->SendToServer($xml_data, 'http://gateway.api.sc/xml/state.php');

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);

        if($results[1]['tag'] == 'ERROR') {
            return array(
                "Ответ сервера" => $results[1]['value'],
                "Статус сообщения" => 'Ответ не распознан'
            );
        } else {
            return array(
                "Ответ сервера" => 'Операция выполнена',
                "Статус сообщения" => $this->GetMessageStatus($results[1]['value'])
            );
        }
    }
}
?>
