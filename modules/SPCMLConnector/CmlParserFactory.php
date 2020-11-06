<?php

require_once 'modules/SPCMLConnector/UnsupportedProtocolVersionException.php';
require_once 'modules/SPCMLConnector/AbstractCMLParser.php';
require_once 'modules/SPCMLConnector/CMLParser200.php';

/**
 * Resolve concrece parser for CommerceML version
 */
class CmlParserFactory {
    
    private static $versionsParsersMap = array(
        array(
            'startVersion' => '2.00',
            'endVersion' => '2.*',
            'parserClass' => 'CmlParser200'
        )
    );
    
    /**
     * 
     * @param string $cmlVersion
     * @return AbstractCMLParser
     * @throws UnsupportedProtocolVersionException
     */
    public static function getParser($cmlVersion) {
        $cmlParserClass = null;
        list($cmlMajorVersion, $cmlMinorVersion) = explode(".", $cmlVersion);
        foreach(self::$versionsParsersMap as $parserMetadata) {
            list($majorStartVersion, $minorStartVersion) = explode(".", $parserMetadata['startVersion']);
            list($majorEndVersion, $minorEndVersion) = explode(".", $parserMetadata['endVersion']);
            
            if( (int) $cmlMajorVersion >= (int) $majorStartVersion && 
                (int) $cmlMajorVersion <= (int) $majorEndVersion &&
                (int) $cmlMinorVersion >= (int) $minorStartVersion && 
                ($minorEndVersion == '*' || (int) $cmlMinorVersion <= (int) $minorEndVersion) ) {
                
                $cmlParserClass = $parserMetadata['parserClass'];
                break;
            } 
        }
        
        if($cmlParserClass == null) {
            throw new UnsupportedProtocolVersionException("Unsupported CommerceML version " . $cmlVersion);
        }
        
        return new $cmlParserClass();
    }
}