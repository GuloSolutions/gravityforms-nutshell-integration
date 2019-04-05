<?php

namespace Controllers;

use GravityFormsController;

class NutshellController
{
    public $api;
    private $id;
    private $user;
    private $contacts = [];

    public function __construct()
    {
        $username = $apiKey = '';
        $username = get_option('nutshell_api_username');
        $apiKey = get_option('nutshell_api_key');

        if ($username && $apiKey) {
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
        return $this->api->searchContactsAndUsers(['string' => $email, 'stubResponses' => false ]);
    }

    public function searchContacts($name)
    {
        return $this->api->searchContacts(['string' => $name]);
    }

    public function searchByEmail($email)
    {
        return $this->api->searchByEmail($email);
    }

    public function getNutshellUser($userId, $rev)
    {
        return $this->api->getUser($userId, $rev);
    }
}
