<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
require_once 'modules/SPSocialConnector/SPSocialConnectorHelper.php';
require_once 'modules/SPSocialConnector/SPSocialConnector.php';
require_once 'modules/Accounts/Accounts.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Leads/Leads.php';

class SPSocialConnector_AuthWindow_View extends Vtiger_Popup_View {

    /**
     * @param Vtiger_Request $request
     */
	function process(Vtiger_Request $request) {
        $popuptype = vtlib_purify($request->get('popuptype'));
        switch ($popuptype) {
            case 'send_msg':
                $this->sendMessage($request);
                break;
            case 'get_msg':
                $this->getMessage($request);
                break;
            case 'load_profile':
                $this->loadProfile($request);
                break;
            default:
                break;
        }
	}

    /**
     * Send private message to social net
     * @param Vtiger_Request $request
     */
    function sendMessage(Vtiger_Request $request) {
        global $current_user;

        $url = vtlib_purify($request->get('URL'));
        $text = vtlib_purify($request->get('text'));
        $sourcemodule = vtlib_purify($request->get('source_module'));
        $module = $request->getModule();
        $record_id = vtlib_purify($request->get('record_id'));
        if(!empty($url)) {
            $url = trim($url, ',');
            $urllist = explode(',', $url);
            foreach ($urllist as $key => $value) {
                if ($value == '') {
                    unset($urllist[$key]);
                }
            }
            $recordids = array();
            $urllist = array_values($urllist);
            for ($i = 0; $i < count($urllist); $i++) {
                $recordids[$i] = $record_id[0];
                $response[$i] = SPSocialConnectorHelper::parseURL($urllist[$i]);
                $res[$i] = SPSocialConnectorHelper::hybridAuthSend($response[$i]->id,$text,$response[$i]->domen);
            }

            SPSocialConnector::saveOutboundMsg($text, $urllist, $response, $res, $current_user->id, $recordids, $sourcemodule);	

        }

        $social_net_mapping = array(
            'vk.com' => 'Vkontakte',
            'twitter.com' => 'Twitter',
        );
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $module);
        $viewer->assign('SOCIAL_NET_DOMEN', $response);
        $viewer->assign('SOCIAL_NET_MAPPING', $social_net_mapping);
        $viewer->assign('STATUS', $res);
        $viewer->view('OutboundMsgStatus.tpl', $module);

    }

    /**
     * Load private messages from social nets
     * @param Vtiger_Request $request
     */
    function getMessage(Vtiger_Request $request) {
        $source_module = vtlib_purify($request->get('source_module'));
        $source_record = vtlib_purify($request->get('source_record'));
        $module = $request->getModule();

        $focus = CRMEntity::getInstance($source_module);
        $focus->retrieve_entity_info($source_record, $source_module);

        $vk_url = $focus->column_fields['vk_url'];
        if($vk_url != null) {
            $domenAndID = SPSocialConnectorHelper::parseURL($vk_url);
            $maxMsgID = SPSocialConnectorHelper::getMaxMsgId($module, $source_record, $domenAndID->domen);
            $messages = SPSocialConnectorHelper::hybridAuthGetMsgs($domenAndID->id, $domenAndID->domen, $maxMsgID);
        } else {
            $messages = array();
        }

        SPSocialConnector::saveVkMsg($source_record, $source_module, $messages);
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $module);
        $viewer->assign('MSG_COUNT', count($messages));
        $viewer->view('LoadMsgStatus.tpl', $module);

    }

    /**
     * Load user data from social net when edit Leads/Contacts/Accounts
     * @param Vtiger_Request $request
     */
    function loadProfile(Vtiger_Request $request) {
        $url = vtlib_purify($request->get('URL'));
        $source_module = vtlib_purify($request->get('sourcemodule'));
        $recordid = vtlib_purify($request->get('recordid'));
        $module = $request->getModule();
        $response = SPSocialConnectorHelper::parseURL($url);
        $user_profile = SPSocialConnectorHelper::hybridauthUserProfile($response->id, $response->domen);

        if(!(empty($user_profile->birthDay)) && !(empty($user_profile->birthMonth)) && !(empty($user_profile->birthYear))){
            $date = $user_profile->birthDay.'-'.$user_profile->birthMonth.'-'.$user_profile->birthYear;
            $date = date('Y-m-d', strtotime($date));
        } else {
            $date = NULL;
        }

        $region = trim($user_profile->region, ',');
        $regionlist = explode(',', $region);

        $user_profile->birthDay = $date;
        $user_profile->city = $regionlist[0];
        $user_profile->country = $regionlist[1];

        $updated_fields = SPSocialConnectorHelper::addDataByModule($source_module, $recordid, $user_profile);

        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $module);
        $viewer->assign('UPDATED_FIELDS', $updated_fields);
        $viewer->view('LoadProfileStatus.tpl', $module);

    }
}

