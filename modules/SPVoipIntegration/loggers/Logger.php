<?php

namespace SPVoipIntegration\loggers;

class Logger {
    
    private static $logFile = 'logs/voip_integration.log';
    
    public static function setLogFile($logFileName) {
        self::$logFile = $logFileName;
    }
    
    public static function log($message, \Exception $exception = null) {
        $logMessage = "[" . date('Y-m-d H:i:s') . "]" . $message;
        if($exception != null) {
            $logMessage .= "\n Exception message: " . $exception->getMessage() . "\n" . 
                        "Exception trace: \n" . $exception->getTraceAsString() . "\n";
        }
        
        file_put_contents(self::$logFile, $logMessage . "\n", FILE_APPEND);
    }
    
    public static function debugLog($message, $object = null) {
        $logMessage = "[" . date('Y-m-d H:i:s') . "]" . $message;
        if ($object != null) {
            $logMessage .= "\n Object Information: " . print_r($object, true) . "\n";
        }
        file_put_contents(self::$logFile, $logMessage . "\n", FILE_APPEND);
    }
    
}