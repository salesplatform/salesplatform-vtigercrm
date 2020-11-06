<?php
/**
 * Провайдер для отправки сообщений через систему Informgrad Direct2.
 * @author Юрий Самарин <syn@informgrad.ru>
 */
// SalesPlatform.ru begin
//include_once dirname(__FILE__) . '/../ISMSProvider.php';
// SalesPlatform.ru end
include_once dirname(__FILE__) . '/informgrad/Direct2Connector.class.php';
define('LOG', false);
define('INFORMGRAD_DIRECT2_LOG_FILENAME', dirname(__FILE__) . '/../../../../logs/InformgradDirect2.log');

class SMSNotifier_InformgradDirect2_Provider implements SMSNotifier_ISMSProvider_Model {
    private static $REQUIRED_PARAMETERS = array('LBL_INFORMGRAD_ACCOUNT_NUMBER');
    private $username;
    private $password;
    private $parameters = array();
    private $connector;
    private $logger;

    function  __construct() {
        $this->connector = new Direct2Connector();
        if (($proxyParams = $this->getProxyParams()) != null) {
            $this->connector->setProxyParams($proxyParams);
        }
        if (LOG == true) {
            $this->logger = fopen(INFORMGRAD_DIRECT2_LOG_FILENAME, 'a');
        }
    }
    
    public function getName() {
        return 'InformgradDirect2';
    }

    function  __destruct() {
        if (LOG == true) {
            fflush($this->logger);
            fclose($this->logger);
        }
    }

    function log($str) {
        if (LOG == true) {
            fwrite($this->logger, $str . "\n");
        }
    }

    public function getRequiredParams() {
        $this->log('getRequiredParams');
        if ((!empty($this->username)) && (!empty($this->password))) {
            $this->log('getBalance');
            $xml_struct = $this->connector->getBalance($this->username, $this->password);
            $this->log(print_r($xml_struct, true));
        }
        return self::$REQUIRED_PARAMETERS;
    }

    public function getServiceURL($type = false) {
        return false;
    }

    /**
     * Устанавливает параметры для аутентификации.
     * @param <type> $username Имя
     * @param <type> $password Пароль
     */
    public function setAuthParameters($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function setParameter($key, $value) {        
        $this->parameters[$key] = $value;
    }

    public function getParameter($key, $defvalue = false) {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        return $defvalue;
    }

    /**
     * Возвращает настройки прокси-сервера, установленные в vTiger CRM.
     * @return Настройки прокси-сервера
     */
    private function getProxyParams() {
        global $adb;
        if (!empty($adb)) {
            $sql = 'select * from vtiger_systems where server_type = ?';
            $result = $adb->pquery($sql, array('proxy'));
            if ($adb->num_rows($result) > 0) {
                $proxy['addr'] = $adb->query_result($result, 0, 'server');
                $proxy['port'] = $adb->query_result($result, 0, 'server_port');
                $proxy['username'] = $adb->query_result($result, 0, 'server_username');
                $proxy['password'] = $adb->query_result($result, 0, 'server_password');
                return $proxy;
            }
        }
    }

    /**
     * Выполняет отправку сообщений.
     * @param $message Тест сообщения
     * @param $tonumbers Номера адресатов
     * @return Результат операции
     */
    public function send($message, $tonumbers) {
        if (!is_array($to_numbers)) {
            $to_numbers = array($to_numbers);
        }
        $this->log(sprintf('%s Запрос на отправку SMS-сообщений (количество сообщений: %d)', date('d.m.Y H:i:s'), count($to_numbers)));        
        $results = array();
        foreach ($tonumbers as $number) {
            $userLabel = $this->generateId();
            $number = $this->getClearedDestinationAddress($number);
            $result['to'] = $number;
            $result['id'] = $userLabel;
            $success = $this->connector->sendSms($this->username, $this->password, $this->getParameter('accountNumber'), $number, $message, $userLabel);
            $info = $this->connector->getLastInfo();
            if ($success == true) {
                $result['error'] = false;
                $result['status'] = self::MSG_STATUS_PROCESSING;
                $result['statusmessage'] = 'Сообщение успешно отправлено';
            } else {
                $result['error'] = true;
                $result['status'] = self::MSG_STATUS_ERROR;
                $result['statusmessage'] = sprintf('При отправке сообщения произошла ошибка, код %d', $info['http_code']);
            }
            $results[] = $result;
            $this->log(sprintf('Адресат: %s, %s', $result['to'], $result['statusmessage']));
            $this->log(print_r($result, true));
            if ($result['error']) {
                $this->log(print_r($info, true));
            }
        }
        return $results;
    }

    /**
     * Выполняет запрос на получение статуса сообщения.
     * @param $messageid Идентификатор сообщения
     * @return Результат запроса
     */
    public function query($messageid) {
        $this->log(sprintf('%s Запрос на получение статуса сообщения (идентификатор: %s)', date('d.m.Y H:i:s'), $messageid));
        if (empty($messageid)) {
            $this->log('Некорректно указан идентификатор сообщения');
            return array(
                    'error' => true,
                    'needlookup' => 0,
                    'statusmessage' => 'Некорректно указан идентификатор сообщения',
                    'status' => self::MSG_STATUS_ERROR,
            );
        }

        $for_24h = time() - 86400;

        $xml_struct = $this->connector->getStatus($this->username, $this->password, $for_24h);
        $info = $this->connector->getLastInfo();

        if ($xml_struct !== false) {
            $userLabelFound = false;
            $statusDetected = false;
            foreach ($xml_struct as $record) {
                if (($record['tag'] == 'USERLABEL') && ($record['value'] == $messageid)) {
                    $userLabelFound = true;
                } elseif (($userLabelFound) && ($record['tag'] == 'STATUS')) {
                    $status = $record['value'];
                    switch ($status) {
                        // статус не установлен
                        case 0: $result = array(
                                    'needlookup' => 1,
                                    'statusmessage' => 'Статус еще не установлен',
                                    'status' => self::MSG_STATUS_ERROR,
                            );
                            break;
                        // STATUS_WAITING
                        case 1: $result = array(
                                    'needlookup' => 1,
                                    'statusmessage' => 'Сообщение в состоянии ожидания',
                                    'status' => self::MSG_STATUS_DISPATCHED,
                            );
                            break;
                        // STATUS_ENROTE
                        case 2: $result = array(
                                    'needlookup' => 1,
                                    'statusmessage' => 'Сообщение доставляется адресату',
                                    'status' => self::MSG_STATUS_PROCESSING,
                            );
                            break;
                        // STATUS_DELIVERED
                        case 3: $result = array(
                                    'needlookup' => 0,
                                    'statusmessage' => 'Сообщение доставлено адресату',
                                    'status' => self::MSG_STATUS_DELIVERED,
                            );
                            break;
                        // STATUS_EXPIRED
                        case 4:
                        // STATUS_DELETED
                        case 5:
                        // STATUS_UNDELIVERABLE
                        case 6: $result = array(
                                    'needlookup' => 0,
                                    'statusmessage' => 'Сообщение не доставлено адресату',
                                    'status' => self::MSG_STATUS_FAILED,
                            );
                            break;
                        // STATUS_ACCEPTED
                        case 7: $result = array(
                                    'needlookup' => 1,
                                    'statusmessage' => 'Сообщение доставлено на SMS-центр',
                                    'status' => self::MSG_STATUS_DISPATCHED,
                            );
                            break;
                        // STATUS_UNKNOWN
                        case 8:
                        // STATUS_REJECTED
                        case 9: $result = array(
                                    'needlookup' => 0,
                                    'statusmessage' => 'Ошибка при отправке сообщения',
                                    'status' => self::MSG_STATUS_FAILED,
                            );
                            break;
                    }
                    $result['error'] = false;
                    $statusDetected = true;
                    break;
                }
            }
            if (!$statusDetected) {
                $result = array(
                        'error' => true,
                        'needlookup' => 0,
                        'statusmessage' => 'Не удалось определить статус сообщения',
                        'status' => self::MSG_STATUS_ERROR,
                );
            }
            //$this->log(print_r($info, true));
            //$this->log(print_r($xml_struct, true));
            $this->log($result['statusmessage']);
            return $result;
        } else {
            $result = array(
                    'error' => true,
                    'needlookup' => 0,
                    'statusmessage' => sprintf('При запросе статуса сообщения произошла ошибка, код %d', $info['http_code']),
                    'status' => self::MSG_STATUS_ERROR,
            );
            $this->log($result['statusmessage']);
            $this->log(print_r($info, true));
            return $result;
        }
    }

    /**
     * Генерирует метку для отправляемого сообщения.
     * @return Метка для отправляемого сообщения
     */
    private function generateId() {
        return 'vtiger_' . time() . '_' . rand(1000, 9999);
    }

    /**
     * Выполняет очистку номера адресата.
     * @param $number Номер адресата в произвольном формате
     * @return Номер содержащий только числа
     */
    private function getClearedDestinationAddress($number) {
        return substr(preg_replace('/[^0-9]/', '', $number), 0, 11);
    }
}
?>
