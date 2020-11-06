<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Vtiger_Systems_Model extends Vtiger_Base_Model{

    const tableName = 'vtiger_systems';

    public function getId() {
        return $this->get('id');
    }

    public function isSmtpAuthEnabled() {
        $smtp_auth_value = $this->get('smtp_auth');
        // SalesPlatform.ru begin
        return ($smtp_auth_value == 'on' || $smtp_auth_value == 1 || $smtp_auth_value == 'true') ? "true" : "false";
        //return ($smtp_auth_value == 'on' || $smtp_auth_value == 1 || $smtp_auth_value == 'true') ? true : false;
        // SalesPlatform.ru end
    }
    
    // SalesPlatform.ru begin
    public function isUseSendMailEnabled() {
        $use_sendmail = $this->get('use_sendmail'); 
        return ($use_sendmail == 'on' || $use_sendmail == 1) ? "true" : "false";
    }

    public function isUseMailAccountEnabled() {
        $use_mail_account = $this->get('use_mail_account');
        return ($use_mail_account == 'on' || $use_mail_account == 1) ? "true" : "false";
    }
    // SalesPlatform.ru end

    public function save() {
        $db = PearDatabase::getInstance();

        $id = $this->getId();
        $params = array();

        $server_password = $this->get('server_password');
        if ($id) {
            if (!Vtiger_Functions::isProtectedText($server_password)) {
                $server_password = Vtiger_Functions::toProtectedText($server_password);
            }
        } else {
            $server_password = Vtiger_Functions::toProtectedText($server_password);
        }
        
        // SalesPlatform.ru begin
        array_push($params, $this->get('server'),$this->get('server_port'),$this->get('server_username'),$server_password,$this->get('server_type'),
                   $this->isSmtpAuthEnabled(),$this->get('server_path'),$this->get('from_email_field'),$this->get('server_tls'),$this->get('from_name'),$this->get('use_sendmail'),$this->get('use_mail_account'));
        //array_push($params, $this->get('server'),$this->get('server_port'),$this->get('server_username'),$this->get('server_password'),$this->get('server_type'),
                   //$this->isSmtpAuthEnabled(),$this->get('server_path'),$this->get('from_email_field'));
        // SalesPlatform.ru end

        if(empty($id)) {
            $id = $db->getUniqueID(self::tableName);
            //To keep id in the beginning
            array_unshift($params, $id);
            // SalesPlatform.ru begin
            $query = 'INSERT INTO '.self::tableName.' VALUES(' . generateQuestionMarks($params) . ')';
            //$query = 'INSERT INTO '.self::tableName.' VALUES(?,?,?,?,?,?,?,?,?)';
            // SalesPlatform.ru end
        }else{
            // SalesPlatform.ru begin
            $query = 'UPDATE '.self::tableName.' SET server = ?, server_port= ?, server_username = ?, server_password = ?,
                server_type = ?,  smtp_auth= ?, server_path = ?, from_email_field=?, server_tls=?, from_name=?, use_sendmail=?, use_mail_account=? WHERE id = ?';
            //$query = 'UPDATE '.self::tableName.' SET server = ?, server_port= ?, server_username = ?, server_password = ?,
            //    server_type = ?,  smtp_auth= ?, server_path = ?, from_email_field=? WHERE id = ?';
            // SalesPlatform.ru end
            $params[] = $id;
        }
        $db->pquery($query,$params);
        return $id;
    }

    public static function getInstanceFromServerType($type,$componentName) {
        // SalesPlatform.ru begin
        require_once 'includes/SalesPlatform/NetIDNA/idna_convert.class.php';
        // SalesPlatform.ru end
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM '.self::tableName.' WHERE server_type=?';
        $params = array($type);
        $result = $db->pquery($query,$params);
        try{
        $modelClassName = Vtiger_Loader::getComponentClassName('Model', $componentName, 'Settings:Vtiger');
        }catch(Exception $e) {
            $modelClassName = self;
        }
        $instance = new $modelClassName();
        if($db->num_rows($result) > 0 ){
            $rowData = $db->query_result_rowdata($result,0);
            $instance->setData($rowData);
        }
        // SalesPlatform.ru begin
        $idn = new idna_convert();
        $mail_server_username = $idn->decode($instance->get('server_username'));
        $from_email_field = $idn->decode($instance->get('from_email_field'));
        $instance->set('server_username', $mail_server_username);
        $instance->set('from_email_field', $from_email_field);
        // SalesPlatform.ru end
        return $instance;
    }

}
