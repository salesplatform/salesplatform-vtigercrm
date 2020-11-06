<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

class ShutdownHandler {
    
    private $logFile = 'logs/application.log';
    private $startExecutionDirectory;
    
    public function __construct() {
        /* On shutdown apache can change dir - so in handler need restore it */
        $this->startExecutionDirectory = getcwd();
    }

    public function registerSystemEventsLog() {
        register_shutdown_function(function() {
            $error = error_get_last();
            if($error != null) {
                chdir($this->startExecutionDirectory);
                
                $type = $error['type'];
                if($type == E_ERROR || $type == E_CORE_ERROR || $type == E_COMPILE_ERROR
                        || $type == E_USER_ERROR || $type == E_RECOVERABLE_ERROR || $type == E_PARSE) {
                    
                    $this->log("Error on process request. Details: ", $error);
                    error_log("Error on process request. Details: " . $error);
                }
            }
        });
    }
    
    private function log($message, $error = null) {
        $date = date("Y-m-d H:i:s");
        
        $logMessage = "[" . $date . "] " . $message;
        if($error != null) {
            $logMessage .= "\n" . $this->prepareErrorListMessage($error);
        }
        
        error_log($logMessage);
        file_put_contents($this->logFile, $logMessage . "\n", FILE_APPEND);
    }
    
    private function prepareErrorListMessage($error) {
        $message = "Code - " . $error['type'] . "\n";
        $message .= "Message - " . $error['message'] . "\n";
        $message .= "File - " . $error['file'] . "\n";
        $message .= "Line - " . $error['line'] . "\n";
        
        return $message;
    }
}
