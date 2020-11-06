<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

require_once 'modules/Import/readers/FileReader.php';
require_once 'modules/Import/readers/parsecsv.lib.php';

class SPImport_CSVReader_Reader extends Import_FileReader_Reader {

	public function getFirstRowData($hasHeader=true) {
		global $default_charset;

		$filePath = $this->getFilePath();

		$headers = array();
		$firstRowData = array();
		$currentRow = 0;
        $i = 0; // iterator for headers array
        $j = 0; // iteratot for firstRowData array
        
        $csv = new parseCSV();
        $csv->delimiter = $this->request->get('delimiter');
        $csv->parse($filePath);
        $data = $csv->data;
        
		foreach ($data as $val) {
            if($currentRow == 0 || ($currentRow == 1 && $hasHeader)) {
                if($hasHeader && $currentRow == 0) {
                    foreach($data[0] as $key => $value) {
                        $headers[$i] = $this->convertCharacterEncoding($key, $this->request->get('file_encoding'), $default_charset);                   
                        $i++;

                    }
                } else {
                    if(!$hasHeader && $currentRow == 0) {
                        foreach($data[0] as $key => $value) {
                            $firstRowData[$j] = $this->convertCharacterEncoding($key, $this->request->get('file_encoding'), $default_charset);                     
                            $j++;

                        }
                    } else {
                        foreach($data[0] as $key => $value) {
                            $firstRowData[$j] = $this->convertCharacterEncoding($value, $this->request->get('file_encoding'), $default_charset);                     
                            $j++;

                        }
                    }
                    break;
                }
            }
            
            $currentRow++;
        }
        
		if($hasHeader) {
			$noOfHeaders = count($headers);
			$noOfFirstRowData = count($firstRowData);
			// Adjust first row data to get in sync with the number of headers
			if($noOfHeaders > $noOfFirstRowData) {
				$firstRowData = array_merge($firstRowData, array_fill($noOfFirstRowData, $noOfHeaders-$noOfFirstRowData, ''));
			} elseif($noOfHeaders < $noOfFirstRowData) {
				$firstRowData = array_slice($firstRowData, 0, count($headers), true);
			}
			$rowData = array_combine($headers, $firstRowData);
		} else {
			$rowData = $firstRowData;
		}

		return $rowData;
	}

	public function read() {
		global $default_charset;

		$filePath = $this->getFilePath();
		$status = $this->createTable();
		if(!$status) {
			return false;
		}

        $csv = new parseCSV();
        $csv->delimiter = $this->request->get('delimiter');
        $csv->parse($filePath);
        $data = $csv->data;
        
		$fieldMapping = $this->request->get('field_mapping');
        
        if(!$this->request->get('has_header')) {
            $firstRow = array_keys($data[0]);
            array_unshift($data, $firstRow);
        }
                
		foreach ($data as $row_data) {
            
            $row_data_index = array_values($row_data);
			$mappedData = array();
			$allValuesEmpty = true;
            
			foreach($fieldMapping as $fieldName => $index) {
				$fieldValue = $row_data_index[$index];
				$mappedData[$fieldName] = $fieldValue;
				if($this->request->get('file_encoding') != $default_charset) {
					$mappedData[$fieldName] = $this->convertCharacterEncoding($fieldValue, $this->request->get('file_encoding'), $default_charset);
				}
				if(!empty($fieldValue)) $allValuesEmpty = false;
			}
			if($allValuesEmpty) continue;
			$fieldNames = array_keys($mappedData);
			$fieldValues = array_values($mappedData);
			$this->addRecordToDB($fieldNames, $fieldValues);
            
		}
	}
        
}
?>
