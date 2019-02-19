<?php

class FormOptions
{
    //static $obj;

    public $options;

    public function __construct()
    {
    }

    public function getFormOptions($options)
    {
        $this->options = $options;
        global $all_options;
        $all_options = 'NEW GLOBAL';
    }

    public function get()
    {
        return $this->options;
    }
}

$options = new FormOptions();
