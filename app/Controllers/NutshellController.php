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
        //if (defined('NUTSHELL_API_APIKEY_IMPERSONATION') && defined('NUTSHELL_API_USERNAME')) {
            $apiKey = '4fb90195f717456ed9f66350c71a7e03a9a7b376';
            $username = 'zwilson@gulosolutions.com';
            $this->api = new \NutshellApi($username, $apiKey);
        //}
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
        $contact = $this->api->getContact(['contactId' => $id]);
        return $contact;
    }

    public function findNutshellContacts()
    {
        $params = array('contactId' => $this->id, 'orderBy' => 'modifiedTime');
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

    public function addNote($params, $note)
    {
        $entity = $params['entity'];
        $newNote = $this->api->newNote($entity, $note);
    }

    public function editContact($params, $fields_to_update)
    {
        $this->api->editContact($params['id'], $params['rev'][0], $fields_to_update);
    }

    public function findUsers($email)
    {
        return $this->api->searchContactsAndUsers(['string' => $email]);
    }

    public function searchContacts($name)
    {
        return $this->api->searchContacts(['string' => $name, 'limit' => 1]);
    }
}
