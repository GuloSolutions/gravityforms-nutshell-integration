<?php

use Controllers\GravityFormsDataController;

include_once('/home/radboris/gravityforms-nutshell-integration/app/Controllers/GravityFormsDataController.php');

const GRAVITYFORMS_NUTSHELL_PLUGIN_SETTINGS_PAGE = '/wp-admin/options-general.php?page=my-setting-admin';

if (!empty($_POST)) {
    $all_options = [];
    $unchecked = [];

    // get form info reconstructing it from forms
    $forms = GFAPI::get_forms();
    foreach ($forms as $form) {
        $form_title = str_replace(' ', '_', strtolower($form['title']));
        foreach ($form['fields'] as $field) {
            $option_name = str_replace(' ', '_', $field->label);
            $option_name = strtolower($option_name);
            $option_name .= '_'.$form_title;
            $all_options[] = $option_name;
        }
    }

    error_log(print_r('notes', true));

    error_log(print_r($_POST['note'], true));



    if (isset($_POST['checkbox'])) {
        $check_values = array_keys($_POST['checkbox']);
        $unchecked = array_diff($all_options, $check_values);

        foreach ($_POST['checkbox'] as $k=>$v) {
            update_option($k, 1);
        }

        foreach ($unchecked as $u) {
            update_option($u, 0);
        }

        $form = new GravityFormsDataController();
        $form->sendData();

        error_log(print_r($form->sendData(), true));

        header("Location: " . "http://" . $_SERVER['HTTP_HOST'] . GRAVITYFORMS_NUTSHELL_PLUGIN_SETTINGS_PAGE);
        exit;
    }
}