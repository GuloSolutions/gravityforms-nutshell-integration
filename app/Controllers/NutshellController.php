<?php

namespace Controllers;

use GravityFormsController;

// At this time Nutshell is not using composer or PSR-4
require_once(dirname(__FILE__).'/../../vendor/nutshellcrm/nutshell-api-php/NutshellApi.php');

class NutshellController
{
    public $api;
    private $id;
    private $user;
    private $contacts = [];

    public function __construct()
    {
        $username = $apiKey = '';
        if (defined('NUTSHELL_API_APIKEY') && defined('NUTSHELL_API_USERNAME')) {
            $apiKey = NUTSHELL_API_APIKEY;
            $username = NUTSHELL_API_USERNAME;
            $this->api = new \NutshellApi($username, $apiKey);
        }
    }

    public function getInstanceData()
    {
        $this->id = $this->api->instanceData()->id;
        return $this->id;
    }

    public function getUser()
    {
        $this->user = $this->api->getUser($this->id);
        return $this->user;
    }

    public function getNote()
    {
        $this->result = $this->api->call('getNote', array($userId));
    }

    public function getContact()
    {
        $this->result = $this->api->call('getContact', $params);
    }

    public function findNutshellContacts()
    {
        $params = array( 'contactId' => $this->id);
        $result = $this->api->findContacts($params);

        return $result;
    }

    public function getAuthenticatedUser()
    {
        return $this->user;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function addContact($params)
    {
        error_log(print_r('in add', true));

        error_log(print_r($params, true));

        $params['contact']['creator'] = $this->id;

        $newContact = $this->api->newContact($params);

        error_log(print_r($newContact, true));

        $newContactId = $newContact->id;

        if ($newContactId) {
            return true;
        }
        return false;
    }

    public function addNote($params)
    {
        $entity = $params['entity'];
        $note = $params['note'];
        $newNote = $api->call('newNote', $entity, $note);
    }
}
