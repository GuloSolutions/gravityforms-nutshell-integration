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
        if (defined('NUTSHELL_API_APIKEY_IMPERSONATION') && defined('NUTSHELL_API_USERNAME')) {
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

    public function getContact($id)
    {
        error_log(print_r($id, true));
        $contact = $this->api->getContact(['contactId' => $id]);
        return $contact;
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
        $newContact = $this->api->newContact($params);

        $newContactId = $newContact->id;

        if ($newContactId) {
            return $newContactId;
        }
        return false;
    }

    public function addNote($params)
    {
        $entity = $params['entity'];
        $note = $params['note'];
        $newNote = $api->call('newNote', $entity, $note);
    }

    public function editContact($params)
    {

        // $params->email = (array) $params->email;

        // $params->name = (array) $params->name;
        // $params->phone = (array) $params->phone;

        $phone = ['phone' => '1111111111'];

        $this->api->editContact($params['id'], $params['rev'][0], $phone);
    }
}
