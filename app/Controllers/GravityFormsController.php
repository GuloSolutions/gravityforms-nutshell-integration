<?php

namespace Controllers;

class GravityFormsController
{

    // $base_url = 'http://localhost/wpdev/gravityformsapi/';
    // $api_key = 'your_api_key';
    // $private_key = 'your_private_key';

    // private $method  = 'GET';

    // private $route;

    // private $date;

    public function __construct()
    {
        if (!$this->checkIfGFActive()) {
            return;
        }
    }

    public function checkIfGFActive()
    {
        if (class_exists('GFCommon')) {
            error_log(print_r("active", true));
            return true;
        }
        return false;
    }

    public function addAction()
    {
        add_action('gform_after_submission_2', 'post_to_third_party_2', 10, 2);
        function post_to_third_party_7($entry, $form)
        {
            $baseURI = 'https://app-2GCK1V3Z33.marketingautomation.services/webforms/receivePostback/WxviTCKbWEDiVzA/';
            $endpoint = '1234io3l-c442-8c9e-1234-9933b6n1gi6s';
            $post_url = $baseURI . $endpoint;
            $body = array(
            'First Name' => rgar($entry, '30'),
            'Last Name' => rgar($entry, '31'),
            'Email Name' => rgar($entry, '3'),
            'Phone Number' => rgar($entry, '10'),
            'trackingid__sb' => $_COOKIE['__ss_tk']
            );
            $request = new WP_Http();
            $response = $request->post($post_url, array( 'body' => $body ));
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
