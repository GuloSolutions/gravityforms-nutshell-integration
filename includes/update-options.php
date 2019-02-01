<?php

require_once('form-options.php');

if (!empty($_POST)) {

  $all_options = [];
  $forms = GFAPI::get_forms();
  foreach($forms as $form){

      $form_title = str_replace(' ', '_', strtolower($form['title']));
      foreach($form['fields'] as $field) {
        $option_name = str_replace(' ', '_', $field->label);
        $option_name = strtolower($option_name);
        $option_name .= '_'.$form_title;

        $all_options[] = $option_name;
    }
  }

    error_log(print_r($all_options, true));
    error_log(print_r('your_name', true));

    error_log(print_r(get_option('your_name'), true));

    if (isset($_POST['checkbox'])) {
        foreach ($_POST['checkbox'] as $k=>$v) {
            update_option($k, 1);
            error_log(print_r($k, true));
        }
        //$referer = $_SERVER['HTTP_REFERER'];
      //header("Location: $referer");
    }

}
