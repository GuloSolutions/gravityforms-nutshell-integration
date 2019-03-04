<?php

class MySettingsPage
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
            'nutshell_api_username' // Option name
                    //array( $this, 'sanitize' ) // Sanitize
        );

        register_setting(
            'my_option_group', // Option group
            'nutshell_api_key' // Option name
                    //array( $this, 'sanitize' ) // Sanitize
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
                'creds', // ID
                'User credentials', // Title
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
                    $form_title // Option name
                    //array( $this, 'sanitize' ) // Sanitize
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
                    //array( $this, 'sanitize' ) // Sanitize
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

    public function user_callback($args)
    {
        $current_option = $input_text ='';
        $current_option = get_option($args['title']);
        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($current_option) ? $current_option: "Please enter an value")
        );
    }

    public function api_callback($args)
    {
        $current_option = $input_text ='';
        $current_option = get_option($args['title']);
        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($current_option) ? $current_option: "Please enter an value")
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
        $current_option = $input_text ='';
        $current_option = get_option($args['title']);

        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($current_option) ? $current_option: "Please enter an email")
        );
    }

    public function sanitize($input)
    {
        $new_input = array();

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $new_input['title'] = sanitize_text_field($input['title']);
        }

        // $new_input = array();
        // if( isset( $input['id_number'] ) )
        //     $new_input['id_number'] = absint( $input['id_number'] );

        // if( isset( $input['title'] ) )
        //     $new_input['title'] = sanitize_text_field( $input['title'] );

        // return $new_input;
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
