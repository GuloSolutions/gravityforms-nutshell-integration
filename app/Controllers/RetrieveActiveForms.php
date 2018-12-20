<?php

namespace Controllers;


class RetrieveActiveForms
{

	$api_key = 'your_api_key';
	$private_key = 'your_private_key';
	$route = 'forms';
	$expires = strtotime( '+60 mins' );

	public function __construct(){


	}

	function calculate_signature( $string, $private_key )
	{
    	$hash = hash_hmac( 'sha1', $string, $private_key, true );
    	$sig = rawurlencode( base64_encode( $hash ) );

   	 	return $sig;
	}

	function getRequest()
	{
		$string_to_sign = sprintf( '%s:%s:%s:%s', $api_key, 'GET', $route, $expires );
		$sig = self::calculate_signature( $string_to_sign, $private_key );
		$url = 'http://localhost/wpdev/gravityformsapi/' . $route . '?api_key=' . $api_key . '&signature=' . $sig . '&expires=' . $expires;

		$response = wp_remote_request( $url, array( 'method' => 'GET' ) );

		if ( wp_remote_retrieve_response_code( $response ) != 200 || ( empty( wp_remote_retrieve_body( $response ) ) ) ){
		    die( 'There was an error attempting to access the API.' );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if( $body['status'] > 202 ){
		    die( "Could not retrieve forms." );
		}
	}
}
