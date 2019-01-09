<?php

//class UpdateOptions {

	// public function __construct()
 //    {
 //    	$this->save();
 //    }


     // public function save()
     // {
		if (isset($_POST['checkbox'])) {
			error_log(print_r('this is the checkbox', true));
			error_log(print_r($_POST['checkbox'], true));

			error_log(print_r('after', true));

	}
	// }

	//exit;

    // function activate() {
    //     if (false === get_option('MY_options'))
    //         update_option('MY_options', $this->get_default_options());
    // }

    // function register_settings() {
    //     register_setting('MY_options', 'MY_options', array($this, 'validate_options'));
    //     if (false === ($options = get_option('MY_options')))
    //         $options = $this->get_default_options();
    //     update_option('MY_options', $options);
    // }

    // function get_default_options() {
    //     $options = array(
    //         'cb_option' => 1, // the checkbox-option in question
    //         'option_n' => 'whatever',
    //         ...
    //     );
    //     return $options;
    // }

    // function validate_options($options) {
    //     $validated['cb_option'] = (1 === $options['cb_option']) ? 1 : 0;
    //     $validated['option_n'] = SANITIZE_IN_SOME_WAY($options['title_name']);
    //     ...
    //     return $validated;
    // }
//}
