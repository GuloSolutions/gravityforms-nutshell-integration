<?php

namespace App\Controllers;

use NutshellApi;

use App\Controllers\GravityFormsController;


class NutshellController {

	$apiKey;

	$username;

	$api;

	$gravity_data;

	public function __construct()
	{
		$gravity = new GravityForms();

		$gravity_data = $gravity->get();

		$this->api = new NutshellApi($username, $apiKey);
	}

	public function getContact()
	{
		$this->result = $this->api->call('getContact', $params);
	}

	public function findContacts(params)
	{
		$result = $api->findContacts($params);
	}


	$params = array( 'contactId' => 132 );
	$result = $api->call('getContact', $params);

	$params = array(
			'query' => array(
				'leadId' => 1209,
			),
	);

}
