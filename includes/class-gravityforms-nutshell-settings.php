<?php

class GravityNutshellSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $name;
    private static $id;
    private $labels;

    /**
     * Start up
     */
    public function __construct($name)
    {
        add_action('admin_menu', array( $this, 'add_plugin_page' ));
        add_action('admin_init', array( $this, 'page_init' ));
        $this->name = $name;
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            $this->name,
            'manage_options',
            'my-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        ?>

        <div class="wrap">
            <?php echo '<h4>' . $this->name .' '.'Settings</h4>'; ?>
            <form id="test" class="gf_nutshell_options" method="post" action="options.php">
                <?php
                settings_fields('my_option_group');
        do_settings_sections('my-setting-admin');
        submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        $forms = GFAPI::get_forms();
        $checkbox = [];

        register_setting(
            'my_option_group', // Option group
            'nutshell_api_username', // Option name
            array( $this, 'sanitize_email' ) // Sanitize
        );

        register_setting(
            'my_option_group', // Option group
            'nutshell_api_key', // Option name
            array( $this, 'sanitize_apikey' ) // Sanitize
        );

        add_settings_field(
                'nutshell_api_username',
                "Enter API username",
                array( $this, 'user_callback'),
                'my-setting-admin',
                'creds',
                array('title' => 'nutshell_api_username')
            );

        add_settings_field(
                'nutshell_api_key',
                "Enter API key",
                array( $this, 'api_callback'),
                'my-setting-admin',
                'creds',
                array('title' => 'nutshell_api_key')
            );

        add_settings_section(
                'creds', // ID,
                'API info',
                array( $this, 'print_user_info' ), // Callback
                'my-setting-admin' // Page
            );

        foreach ($forms as $form) {
            add_settings_section(
                    $form['title'], // ID
                    $form['title'], // Title
                    array( $this, 'print_section_info' ), // Callback
                    'my-setting-admin' // Page
                );

            $form_title = str_replace(' ', '_', strtolower($form['title']));
            register_setting(
                    'my_option_group', // Option group
                    $form_title, // Option name
                    array( $this, 'sanitize_email_forms' ) // Sanitize
                );

            add_settings_field(
                $form_title,
                "Select a Nutshell user to associate with the form",
                array( $this, 'note_callback'),
                'my-setting-admin',
                $form['title'],
                array('title' => $form_title)
            );

            register_setting(
                    'my_option_group', // Option group
                    'checkbox' // Option name
                );

            foreach ($form['fields'] as $field) {
                if (!empty($field->label)) {
                    $option_name = str_replace(' ', '_', $field->label);
                    $option_name = $option_name;
                    $option_name = strtolower($option_name);
                    $option_name .= '_' . $form_title;
                    $form_labels[] = $option_name;

                    add_settings_field(
                        $option_name,
                        $field->label,
                        array( $this, 'title_callback'),
                        'my-setting-admin',
                        $form['title'],
                        array('label' => $option_name)
                    );
                }
            }
        }
    }

    public function sanitize_email($input)
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return $input;
        }
        return;
    }


    public function sanitize_email_forms($input)
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return $input;
        } else {
            return "Please enter a valid email";
        }
    }

    public function sanitize_apikey($input)
    {
        $new_value = '';
        if ($new_value = sanitize_text_field($input)) {
            error_log(print_r($new_value, true));
        }
        return $new_value;
    }

    public function user_callback($args)
    {
        $current_option = $input_text ='';
        $current_option = get_option($args['title']);
        $clean = filter_var($current_option, FILTER_VALIDATE_EMAIL);

        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($clean) ? $clean: "Please enter a value")
        );
    }

    public function api_callback($args)
    {
        $current_option=$input_text=$clean='';
        $current_option = get_option($args['title']);
        $clean = filter_var($current_option, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($clean) ? $clean: "Please enter a value")
        );
    }

    public function title_callback($args)
    {
        $current_option = $input_text ='';
        $current_option = get_option('checkbox');

        if (!empty($current_option[$args['label']])) {
            $current_option = 'checked';
            $input_text = 'Designated as a note';
        } else {
            $current_option = '';
            $input_text = 'Not a note';
        }
        printf(
            sprintf('<input type="checkbox" name="checkbox[%s]" class="btn btn-primary" %s id="%s"  data-toggle="toggle" data-size="large" aria-pressed="false" autocomplete="off">%s</input>', $args['label'], $current_option, $args['label'], $input_text)
        );
    }

    public function note_callback($args)
    {
        $current_option=$input_text=$clean='';
        $current_option = get_option($args['title']);

        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($clean) ? $clean: "Please enter an email")
        );
    }

    public function print_section_info()
    {
        return '';
    }

    public function print_user_info()
    {
        return '';
    }
}
