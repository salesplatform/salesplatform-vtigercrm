<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
header('Content-Type: text/html; charset= utf-8');
require_once( "hybridauth/Hybrid/Auth.php" );
include_once dirname(__FILE__) . '/SPSocialConnector.php';

class SPSocialConnectorHelper {
    
    /**
     * Parse URL from social nets fields
     * in Accounts, Leads, Contacts
     * @param string $URL
     * @return object $response
     */
    static function parseURL($URL) {
        $response = null;
        
        if(strpos($URL, "http://") === FALSE && strpos($URL, "https://") === FALSE) {
                $URL = "http://".$URL;
        }

        $parseURL = parse_url($URL);

        if(strpos($parseURL['host'], "www.")!==FALSE) {
            $domen = substr($parseURL['host'], 4);
        } else {
            $domen = $parseURL['host'];
        }

        if(empty($parseURL['query'])) {
            if(strpos($parseURL['path'], "/id")!==FALSE) {
                $id = substr($parseURL['path'], 3);
            } else {
                $id = substr($parseURL['path'], 1);
            }
        } else {
            $id = substr($parseURL['query'], 3);
        }

        $response->domen = $domen;
        $response->id = $id;

        return $response;
    }
    
    /**
     * Send private message to social net
     * @global type $adb DB instance
     * @param type $id unique ID in social net
     * @param type $text message text
     * @param type $domen social net domen
     * @return type $msg_id response
     */
    static function hybridAuthSend($id, $text, $domen) {        

        global $adb;
        
        $qresult = $adb->pquery("SELECT * FROM vtiger_sp_socialconnector_providers where provider_domen like '%$domen%'", array());
        while($resultrow = $adb->fetch_array($qresult)) {
            $social_network = $resultrow['provider_name'];
        }

        // start a new session (required for Hybridauth)
        session_start();

        // change the following paths if necessary 
        $config = dirname(__FILE__) . '/hybridauth/config.php';


        try {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth( $config );

            // try to authenticate the user, user will be redirected for authentication, 
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $req = $hybridauth->authenticate( "$social_network" );  

            // send private message to user  
            $id_and_text =  $id."?!?".$text;
            $msg_id = $req->sendPrivateMessage( $id_and_text );

            $req->logout(); 

        } catch( Exception $e ) {  
            echo '<div style="text-align: center; margin: 10px;">' . vtranslate("LBL_ERROR") . ": " . $e->getMessage() . '</div>';
        }    
     
        return $msg_id;
    }
        
    static function hybridauthUserProfile( $hybridID, $hybridDomen ) {

        global $adb;
        
        $qresult = $adb->pquery("SELECT * FROM vtiger_sp_socialconnector_providers where provider_domen like '%$hybridDomen%'", array());
        while($resultrow = $adb->fetch_array($qresult)) {
            $social_network = $resultrow['provider_name'];
        }

        // start a new session (required for Hybridauth)
        session_start(); 

        // change the following paths if necessary 
        $config   = dirname(__FILE__) . '/hybridauth/config.php';

        try {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth( $config );

            // try to authenticate the user, user will be redirected for authentication, 
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $req = $hybridauth->authenticate( "$social_network" );  

            // get the user profile 
            $user_profile = $req->getUserProfileByID( $hybridID );
            $user_profile->provider = $social_network;

            $req->logout(); 
        } catch( Exception $e ) {  
            echo '<div style="text-align: center; margin: 10px;">' . vtranslate("LBL_ERROR") . ": " . $e->getMessage() . '</div>';
        }    

        return $user_profile;
    }
        
    static function addDataByModule($module, $recordid, $profileFromSocialNet) {
        
        global $adb;

        $profileFromDB = new stdClass();
        $profile = new stdClass();
        $response = array();

        $i = 0;     // Iterator of changed fields

        if($module == 'Contacts') {

            $query = "SELECT t1.mailingcity, t1.mailingcountry, t2.firstname, t2.lastname, t2.email, t2.mobile, t3.homephone, t3.birthday 
                FROM vtiger_contactaddress t1, vtiger_contactdetails t2, vtiger_contactsubdetails t3
                where t1.contactaddressid = '$recordid' and t2.contactid = '$recordid' and t3.contactsubscriptionid = '$recordid'" ;

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->firstname = $row['firstname'];
                $profileFromDB->lastname = $row['lastname'];
                $profileFromDB->birthday = $row['birthday'];
                $profileFromDB->email = $row['email'];
                $profileFromDB->mobile = $row['mobile'];
                $profileFromDB->homephone = $row['homephone'];
                $profileFromDB->mailingcity = $row['mailingcity'];
                $profileFromDB->mailingcountry = $row['mailingcountry'];
            }
            
            $profile->firstname = $profileFromSocialNet->firstName;
            $profile->lastname = $profileFromSocialNet->lastName;
            $profile->birthday = $profileFromSocialNet->birthDay;
            $profile->email = $profileFromSocialNet->email;
            $profile->mobile = $profileFromSocialNet->mobilePhone;
            $profile->homephone = $profileFromSocialNet->homePhone;
            $profile->mailingcity = $profileFromSocialNet->city;
            $profile->mailingcountry = $profileFromSocialNet->country;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ){

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result = $adb->pquery("SHOW COLUMNS FROM vtiger_contactaddress LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_contactaddress SET $index = '$value' WHERE contactaddressid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $result1 = $adb->pquery("SHOW COLUMNS FROM vtiger_contactdetails LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_contactdetails SET $index = '$value' WHERE contactid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $result2 = $adb->pquery("SHOW COLUMNS FROM vtiger_contactsubdetails LIKE '$index'", array());
                    $exists2 = ($adb->num_rows($result2))?TRUE:FALSE;
                    if($exists2) {
                        $query = "UPDATE vtiger_contactsubdetails SET $index = '$value' WHERE contactsubscriptionid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $response[$i]['index'] = $index;
                    $response[$i]['value'] = $value;
                    $i++;
                }
            }
        }

        if($module == 'Leads') {
            
            $query = "SELECT t1.city, t1.country, t2.firstname, t2.lastname, t2.email, t1.mobile, t1.phone, t3.website 
                FROM vtiger_leadaddress t1, vtiger_leaddetails t2, vtiger_leadsubdetails t3 
                WHERE t1.leadaddressid = '$recordid' and t2.leadid = '$recordid' and t3.leadsubscriptionid = '$recordid'";

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->firstname = $row['firstname'];
                $profileFromDB->lastname = $row['lastname'];
                $profileFromDB->email = $row['email'];
                $profileFromDB->mobile = $row['mobile'];
                $profileFromDB->homephone = $row['phone'];
                $profileFromDB->city = $row['city'];
                $profileFromDB->country = $row['country'];
                $profileFromDB->website = $row['website'];
            }

            $profile->firstname = $profileFromSocialNet->firstName;
            $profile->lastname = $profileFromSocialNet->lastName;
            $profile->email = $profileFromSocialNet->email;
            $profile->mobile = $profileFromSocialNet->mobilePhone;
            $profile->phone = $profileFromSocialNet->homePhone;
            $profile->city = $profileFromSocialNet->city;
            $profile->country = $profileFromSocialNet->country;
            $profile->website = $profileFromSocialNet->webSite;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ) {

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result = $adb->pquery("SHOW COLUMNS FROM vtiger_leadaddress LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_leadaddress SET $index = '$value' WHERE leadaddressid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $result1 = $adb->pquery("SHOW COLUMNS FROM vtiger_leaddetails LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_leaddetails SET $index = '$value' WHERE leadid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $result2 = $adb->pquery("SHOW COLUMNS FROM vtiger_leaddetails LIKE '$index'", array());
                    $exists2 = ($adb->num_rows($result2))?TRUE:FALSE;
                    if($exists2) {
                        $query = "UPDATE vtiger_leadsubdetails SET $index = '$value' WHERE leadsubscriptionid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $response[$i]['index'] = $index;
                    $response[$i]['value'] = $value;
                    $i++;
                }
            }
        }

        if($module == 'Accounts') {
            
            $query = "SELECT t1.ship_city, t1.ship_country, t2.email1, t2.otherphone, t2.phone, t2.website 
                FROM vtiger_accountshipads t1, vtiger_account t2 
                WHERE t1.accountaddressid = '$recordid' and t2.accountid = '$recordid'";

            $res = $adb->pquery($query, array());

            while($row = $adb->fetch_array($res)) {
                $profileFromDB->email1 = $row['email1'];
                $profileFromDB->phone = $row['phone'];
                $profileFromDB->otherphone = $row['otherphone'];
                $profileFromDB->ship_city = $row['ship_city'];
                $profileFromDB->ship_country = $row['ship_country'];
                $profileFromDB->website = $row['website'];
            }

            $profile->email1 = $profileFromSocialNet->email;
            $profile->phone = $profileFromSocialNet->mobilePhone;
            $profile->otherphone = $profileFromSocialNet->homePhone;
            $profile->ship_city = $profileFromSocialNet->city;
            $profile->ship_country = $profileFromSocialNet->country;
            $profile->website = $profileFromSocialNet->webSite;

            // Checking which fields not in the table and which fields have the profile at social net
            // $index - field name, $value - value of the field in social net
            foreach($profile as $index => $value) {
                if( !(empty($value)) && empty($profileFromDB->$index) ) {

                    // Check whether there is a column $index in table, if there is then UPDATE
                    $result =  $adb->pquery("SHOW COLUMNS FROM vtiger_accountshipads LIKE '$index'", array());
                    $exists = ($adb->num_rows($result))?TRUE:FALSE;
                    if($exists) {
                        $query = "UPDATE vtiger_accountshipads SET $index = '$value' WHERE accountaddressid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $result1 =  $adb->pquery("SHOW COLUMNS FROM vtiger_account LIKE '$index'", array());
                    $exists1 = ($adb->num_rows($result1))?TRUE:FALSE;
                    if($exists1) {
                        $query = "UPDATE vtiger_account SET $index = '$value' WHERE accountid = '$recordid'";
                        $adb->pquery($query, array());
                    }

                    $response[$i]['index'] = $index;
                    $response[$i]['value'] = $value;
                    $i++;
                }
            }
        }
        
        return $response;
    }

    /**
     * Load private messages from specific social net
     * @param $id
     * @param $domen
     * @param $maxMsgID
     * @return mixed
     */
    static function hybridAuthGetMsgs($id, $domen, $maxMsgID) {
        global $adb;

        $qresult = $adb->pquery("SELECT * FROM vtiger_sp_socialconnector_providers where provider_domen like '%$domen%'", array());
        while($resultrow = $adb->fetch_array($qresult)) {
            $social_network = $resultrow['provider_name'];
        }

        // start a new session (required for Hybridauth)
        session_start();

        // change the following paths if necessary
        $config = dirname(__FILE__) . '/hybridauth/config.php';

        try {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth( $config );

            // try to authenticate the user, user will be redirected for authentication,
            // if he already did, then Hybridauth will ignore this step and return an instance of the adapter
            $req = $hybridauth->authenticate( "$social_network" );

            // get private messages
            $msg_id = $req->getPrivateMessages($id.'?!?'.$maxMsgID);

            $req->logout();

        } catch( Exception $e ) {
            echo '<div style="text-align: center; margin: 10px;">' . vtranslate("LBL_ERROR") . ": " . $e->getMessage() . '</div>';
        }

        return $msg_id;
    }

    /**
     * Return max msg id for specific crmid
     * for specific social net
     * @param $module
     * @param $source_record
     * @param $domen
     * @return mixed|null|string
     * @throws Exception
     */
    static function getMaxMsgId($module, $source_record, $domen) {
        global $adb;

        $map = array(
            'vk.com' => 'vk',
            'twitter.com' => 'tw'
        );

        $query = 'SELECT crmid FROM vtiger_crmentityrel WHERE module = ? AND relcrmid = ?';
        $result = $adb->pquery($query, array($module, $source_record));
        $crm_ids = array();
        while($resultrow = $adb->fetch_array($result)) {
            $crm_ids[] = $resultrow['crmid'];
        }

        $in_clause = implode(',', $crm_ids);
        $search_column = $map[$domen] . '_message_id';
        $query = "SELECT MAX($search_column) as max_message_id FROM vtiger_sp_socialconnector WHERE socialconnectorid IN ($in_clause)";

        $result = $adb->pquery($query, array());
        if ($adb->num_rows($result)) {
            return $adb->query_result($result, 0, 'max_message_id');
        } else {
            return null;
        }

    }

    /**
     * Generate data for HybridAuth config.php and
     * providers/Vkontakte.php (client auth)
     * @param type $parameters
     */
    static function generateHybridAuthConfig($parameters) {
        global $site_URL;
        
        $url = $site_URL . 'modules/SPSocialConnector/hybridauth/';
        $content = file_get_contents("modules/SPSocialConnector/hybridauth/Hybrid/resources/config.php.tpl");
        $content = str_replace('#GLOBAL_HYBRID_AUTH_URL_BASE#', $url, $content );
        $content = str_replace('#TWITTER_APPLICATION_KEY#', $parameters['tw_app_key'], $content );
        $content = str_replace('#TWITTER_APPLICATION_SECRET#', $parameters['tw_app_secret'], $content );
        $content = str_replace('#VKONTAKTE_APPLICATION_APP_ID#', $parameters['vk_app_id'], $content );
        $content = str_replace('#VKONTAKTE_APPLICATION_APP_SECRET#', $parameters['vk_app_secret'], $content );
        file_put_contents("modules/SPSocialConnector/hybridauth/config.php",  $content);
        
        $vk_content = file_get_contents("modules/SPSocialConnector/Vkontakte.php.tpl");
        $vk_content = str_replace('#VKONTAKTE_ACCESS_TOKEN#', $parameters['vk_access_token'], $vk_content );
        file_put_contents("modules/SPSocialConnector/providers/Vkontakte.php",  $vk_content);
    }
    
}

?>
