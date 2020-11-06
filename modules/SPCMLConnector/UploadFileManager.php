<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Unzip.php';

/**
 * Descripes cretae zip archives operation on website file upload.
 * @author alexd
 */
class UploadFileManager {
    
    /* File uploads directories */
    const UPLOAD_DIR = 'test/upload/site/';
    const DEFAULT_ARCHIVE_NAME = 'import.zip';
    
    private $archiveName;
    private $archiveFileNames;
    private $zip;
    
    /**
     * Initilizate upload directory to import files.
     */
    public function __construct() {
        if(!file_exists(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR);
        }
        
        $this->archiveFileNames = array();
        $this->archiveName = self::DEFAULT_ARCHIVE_NAME;
        $this->zip = new ZipArchive();
    }
    
    private function getImportFileName($number) {
        return "import".$number.".xml";
    }
    
    private function getOffersFileName($number) {
        return "offers".$number.".xml";
    }
    
    private function getOrdersFileName() {
        return "orders.xml";
    }
    
    /**
     * Rewrite archive content by ctaalogs information.
     * @param array<String> $imports
     * @param array<String> $offers
     */
    public function setZipCatalogsContent($imports, $offers) {
        unlink(self::UPLOAD_DIR.$this->archiveName);
        $this->zip->open(self::UPLOAD_DIR.$this->archiveName, ZIPARCHIVE::CREATE);
        $this->archiveFileNames = array();
        foreach($imports as $number => $content) {
            $importFileName = $this->getImportFileName($number);
            $offersFileName = $this->getOffersFileName($number);
            
            $this->zip->addFromString($importFileName, $imports[$number]);
            $this->zip->addFromString($offersFileName, $offers[$number]);
            
            $this->archiveFileNames[] = $importFileName;
            $this->archiveFileNames[] = $offersFileName;
        }
        $this->zip->close();          
    }
    
    /**
     * Adds orders description to archive.
     * @param String $orders
     */
    public function setZipOrdersContent($orders) {
        unlink(self::UPLOAD_DIR.$this->archiveName);
        $this->zip->open(self::UPLOAD_DIR.$this->archiveName, ZIPARCHIVE::CREATE);
        $this->zip->addFromString($this->getOrdersFileName(), $orders);
        $this->zip->close();
        
        $this->archiveFileNames = array();
        $this->archiveFileNames[] = $this->getOrdersFileName();
    }
    
    /**
     * Return binary content of zip file.
     * @return String
     */
    public function getZipContent() {
        $zipCatalog = file_get_contents(self::UPLOAD_DIR.$this->archiveName);
        return $zipCatalog;
    }
    
    /**
     * Return archive name.
     * @return String
     */
    public function getZipFileName() {
        return $this->archiveName;
    }
    
    /**
     * Return all file names which contains in archive. Return order - by add order.
     * @return array
     */
    public function getArchiveFileNames() {
        return $this->archiveFileNames;
    }
}
