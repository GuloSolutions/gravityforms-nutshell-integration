<?php

namespace Controllers;

use Controllers\NutshellController;

class GravityFormsController
{
    private $nutshell;
    private $contacts = [];

    public function __construct()
    {
        if (!$this->checkIfGFActive()) {
            return;
        }

        $this->nutshell->getInstanceData();
        $this->nutshell->getUser();
        $this->nutshell->findNutshellContacts();
    }

    public function checkIfGFActive()
    {
        if (class_exists('GFCommon')) {
            $this->nutshell = new NutshellController();
            return true;
        }
        return false;
    }

    public function addAction()
    {
        add_action('gform_after_submission_2', 'post_to_third_party_2', 10, 2);

        function post_to_third_party_2($entry, $form)
        {
            error_log(print_r($form, true));

            error_log(print_r('after submit', true));

            $contacts = $this->nutshell->getContacts();
            error_log(print_r('contacts', true));
            error_log(print_r($contacts, true));
        }
    }

    // public function getForms() {

    // 	$sig = $this->calculate_signature();

    // 	$url = $base_url . $this->route . '?api_key=' . $api_key . '&signature=' . $sig . '&expires=' . $expires;

    // 	$response = wp_remote_request( $url, array('method' => 'GET' ) );

    // 	if ( wp_remote_retrieve_response_code( $response ) != 200 || ( empty( wp_remote_retrieve_body( $response ) ) ) ){
    // 	    echo 'There was an error attempting to access the API.';
    // 	    die();
    // 	}

    // 	$body_json = wp_remote_retrieve_body( $response );
    // 	$body = json_decode( $body_json, true );

    // 	$data            = $body['response'];
    // 	$status_code     = $body['status'];
    // 	$total           = 0;
    // 	$total_retrieved = 0;

    // 	return $data;
    // }

    // public function calculate_signature($string, $private_key) {

 //    	$hash = hash_hmac("sha1", $string, $private_key, true);

 //    	$sig = rawurlencode(base64_encode($hash));

 //    	return $sig;
    // }
}
