<?php

/* !
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

/**
 * Hybrid_Providers_Foursquare provider adapter based on OAuth2 protocol
 *
 * http://hybridauth.sourceforge.net/userguide/IDProvider_info_Foursquare.html
 */


class Hybrid_Providers_LinkedIn extends Hybrid_Provider_Model_OAuth2 {


	 
	function initialize() {
		parent::initialize();

		// Provider apis end-points
		$this->scope="r_basicprofile, r_emailaddress, rw_company_admin,w_share";
		$this->api->api_base_url = "https://api.linkedin.com/v1/";
		$this->api->authorize_url = "https://www.linkedin.com/oauth/v2/accessToken";
		$this->api->token_url = "https://api.stocktwits.com/api/2/oauth/token";



	}
	
	function getUserProfile() {
	
	
		$data = $this->api->get("people/~?format=json");
drupal_set_message($data);
		if (!isset($data->user->id)) {
			throw new Exception("User profile request failed! {$this->providerId} returned an invalid response: ". Hybrid_Logger::dumpData( $data ), 6);
		}

		$data = $data->user;

		$this->user->profile->identifier = $data->id;
		$this->user->profile->firstName = $data->name;
		$this->user->profile->displayName  = $data->name;
	    $this->user->profile->photoURL = $data->avatar_url;
        $this->user->profile->profileURL="https://www.stocktwits.com/".$data->username;
		return $this->user->profile;
	}
	function setUserStatus($status) {

		if (is_array($status) && isset($status['message']) && isset($status['picture'])) {
			$response = $this->api->post('messages/create.json', array('body' => $status['message'], 'chart' => $status['picture']), null, null, true);
		} else {
			$response = $this->api->post('messages/create.json', array('body' => $status));
		}

		if ($this->api->http_code != 200) {
			throw new Exception("Update user status failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus($this->api->http_code));
		}

		return $response;
	}



	

}
