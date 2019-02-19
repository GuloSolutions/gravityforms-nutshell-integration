<?php

namespace Controllers;

use GravityFormsController;

class GravityFormsDataController
{
    public $form_data;

    public function __construct()
    {
        $this->form_data = 'TEST';
        error_log(print_r('called', true));
    }

    public function sendData()
    {
        error_log(print_r('in send', true));
    }


    public function getData()
    {
        return $this->form_data;
    }
}
