<?php
/*+**********************************************************************************
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.                                                              
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
include_once(dirname(__DIR__).'/hybridauth/Hybrid/Providers/Twitter.php');

class Hybrid_Providers_Extended_Twitter extends Hybrid_Providers_Twitter {
    // new function: view user profile by id
    function getUserProfileByID( $id ) {
        $parameters = array( 'screen_name' => "$id" ); 
        $response = $this->api->get( 'users/show.json', $parameters );
       
        // check the last HTTP status code returned
        if ( $this->api->http_code != 200 ) {
            throw new Exception( "User profile request failed! {$this->providerId} returned an error. " . 
                                $this->errorMessageByStatus( $this->api->http_code ), 6 );
        }

        if ( ! is_object( $response ) || ! isset( $response->id ) ) {
            throw new Exception( "User profile request failed! {$this->providerId} api returned an invalid response.", 6 );
        }

        // store the user profile  
        $this->user->profile->identifier  = (property_exists($response,'id'))?$response->id:"";
        $this->user->profile->displayName = (property_exists($response,'screen_name'))?$response->screen_name:"";
        $this->user->profile->description = (property_exists($response,'description'))?$response->description:"";
        $this->user->profile->firstName   = (property_exists($response,'name'))?$response->name:""; 
        $this->user->profile->photoURL    = (property_exists($response,'profile_image_url'))?$response->profile_image_url:"";
        $this->user->profile->profileURL  = (property_exists($response,'screen_name'))?("http://twitter.com/".$response->screen_name):"";
        $this->user->profile->webSiteURL  = (property_exists($response,'url'))?$response->url:""; 
        $this->user->profile->region      = (property_exists($response,'location'))?$response->location:"";

        return $this->user->profile;
    }

    // new function: send private message to user by URL
    function sendPrivateMessage( $id_and_text ) {
        list($id, $text) = explode( '?!?', $id_and_text );
        $status = '@'.$id.' '.$text;
        $parameters = array( 'status' => $status); 
        $response  = $this->api->post( 'statuses/update.json', $parameters ); 

        // check the last HTTP status code returned
        if ( $this->api->http_code != 200 ) {
            throw new Exception( "Update user status failed! {$this->providerId} returned an error. " . 
                                $this->errorMessageByStatus( $this->api->http_code ) );
            return -1;
        } else {
            return $response->id_str;
        }
    }
}

