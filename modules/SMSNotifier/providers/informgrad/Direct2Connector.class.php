<?php
/**
 * Класс реализующий взаимодействие с системой отправки SMS-сообщений INFORMGRAD Direct2.
 * @author Юрий Самарин <syn@informgrad.ru>
 */
class Direct2Connector {
    /**
     * URL для взаимодействия с системой INFORMGRAD Direct.
     */
    private static $direct2Url = 'http://www.informgrad.ru:8080/direct2';
    /**
     * Порт для взаимодействия с системой INFORMGRAD Direct.
     */
    private static $direct2Port = 8080;
    /**
     * Настройки прокси-сервера.
     */
    private $proxyParams;
    /**
     * Информация о последней выполненной операции.
     */
    private $info;

    /**
     * Возвращает curl-объект с параметрами готовыми для работы с системой Direct2.
     * @param $commandQuery Команда
     * @param $proxy Параметры прокси-сервера
     * @return curl-объект
     */
    private function getCurlHandler($commandQuery) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$direct2Url . $commandQuery);
        curl_setopt($ch, CURLOPT_PORT, self::$direct2Port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURL_HTTP_VERSION_1_1, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ((!empty($this->proxyParams)) && (is_array($this->proxyParams))) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyParams['addr']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyParams['port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyParams['username'] . ':' . $this->proxyParams['password']);
        }
        return $ch;
    }

    /**
     * Устанавливает параметры прокси-сервера.
     * @param $params Параметры прокси-сервера
     */
    public function setProxyParams($params) {
        if ((isset($params['addr'])) && (isset($params['port'])) && (isset($params['username'])) && (isset($params['password']))) {
            $this->proxyParams = $params;
        }
    }

    /**
     * Возвращает результат последнего выполненного запроса.
     * @return Ассоциативный массив полученный командой curl_getinfo
     */
    public function getLastInfo() {
        return $this->info;
    }

    /**
     * Выполняет команду SendSms — отправка одного SMS-сообщения.
     * @param $login Имя пользователя
     * @param $password Пароль пользователя
     * @param $accountNumber Телефонный номер пользователя от которого будет выполнена отправка SMS-сообщения
     * @param $number Телефонный номер адресата сообщения
     * @param $messageText Текст сообщения
     * @param $userLabel Метка сообщения
     * @param $validityPeriod Срок жизни сообщения
     * @param $replaceIfPresent Флаг указывающий на необходимость заменить предыдущее сообщение
     */
    public function sendSms($login, $password, $accountNumber, $number, $messageText, $userLabel, $validityPeriod = '', $replaceIfPresent = 0) {
        // формируем команду
        $commandQuery = sprintf('/sendSms.do?login=%s&password=%s&accountNumber=%s&number=%s&messageText=%s', $login, $password, $accountNumber, $number, urlencode($messageText));
        if (!empty($userLabel)) {
            $commandQuery .= '&userLabel=' . $userLabel;
        }
        if (!empty($validityPeriod)) {
            $commandQuery .= '&validityPeriod=' . $validityPeriod;
        }
        if (!empty($replaceIfPresent)) {
            $commandQuery .= '&replaceIfPresent=' . $replaceIfPresent;
        }

        $ch = $this->getCurlHandler($commandQuery);

        $output = curl_exec($ch);

        $this->info = curl_getinfo($ch);        
        $this->info['output'] = $output;

        return ($this->info['http_code'] < 300) ? true : false;
    }

    /**
     * Выполняет команду GetStatus - получение статусов сообщений.
     * @param <type> $login Имя пользователя
     * @param <type> $password Пароль пользователя
     * @param <type> $timestamp Временная метка, начиная с которой будут получены статусы всех сообщений
     * @return <type> Структура с информацией о статусах сообщений - результат обработки полученного XML-документа функцией xml_parse_into_struct или false в случае ошибки
     */
    public function getStatus($login, $password, $timestamp) {
        // формируем команду
        $commandQuery = sprintf('/getStatus.do?login=%s&password=%s&timestamp=%d', $login, $password, $timestamp);

        $ch = $this->getCurlHandler($commandQuery);

        $output = curl_exec($ch);

        $this->info = curl_getinfo($ch);
        $this->info['output'] = $output;

        if ($this->info['http_code'] < 300) {
            $parser = xml_parser_create();
            xml_parse_into_struct($parser, $output, $xml_struct);
            xml_parser_free($parser);
        }

        return ($this->info['http_code'] < 300) ? $xml_struct : false;
    }

    public function getBalance($login, $password) {
        // формируем команду
        $commandQuery = sprintf('/getBalance.do?login=%s&password=%s', $login, $password);

        $ch = $this->getCurlHandler($commandQuery);

        $output = curl_exec($ch);

        $this->info = curl_getinfo($ch);
        $this->info['output'] = $output;

        if ($this->info['http_code'] < 300) {
            $parser = xml_parser_create();
            xml_parse_into_struct($parser, $output, $xml_struct);
            xml_parser_free($parser);
        }

        return ($this->info['http_code'] < 300) ? $xml_struct : false;
    }
}
?>
