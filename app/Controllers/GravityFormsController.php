<?php

namespace Controllers;

use Controllers\NutshellController;
use Controllers\GravityFormsDataController;


class GravityFormsController
{
    private $nutshell;
    private static $instance;
    private $contacts = [];
    public $gf_data;

    public function __construct()
    {
        if (!$this->checkIfGFActive()) {
            return;
        }

        $this->nutshell->getInstanceData();
        $this->nutshell->getUser();
        $this->contacts = $this->nutshell->findNutshellContacts();
    }

    public function checkIfGFActive()
    {
        if (class_exists('GFCommon')) {
            $this->nutshell = new NutshellController();
            // $this->gf_data = new GravityFormsDataController();
            return true;
        }
        return false;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function addContact($params)
    {
        $this->nutshell->addContact($params);
    }

    public function addNote($params)
    {
        $this->nutshell->addContact($params);
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new GravityFormsController();
            return self::$instance;
        }
    }
    public function post_to_nutshell()
    {
        error_log(print_r('called', true));
    }
}
