<?php

namespace Controllers;

use NutshellApi\NutshellApi;
use GravityFormsController;

class NutshellController
{
    public $api;

    public function __construct()
    {
        $username = $apiKey = '';

        if (defined('NUTSHELL_API_APIKEY') && defined('NUTSHELL_API_USERNAME')) {
            $apiKey = NUTSHELL_API_APIKEY;

            $username = NUTSHELL_API_USERNAME;

            $this->api = new NutshellApi($username, $apiKey);
        }
    }

    public function getContact()
    {
        $this->result = $this->api->call('getContact', $params);
    }

    public function findNutshellContacts()
    {
        $params = array( 'contactId' => 132 );

        $result = $this->api->findContacts($params);

        return $result;
    }
}
