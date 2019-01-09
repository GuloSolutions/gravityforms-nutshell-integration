<?php
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $name;
    private static $id;

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
        // Set class property
        $this->options = get_option('my_option_name');
        error_log(print_r($this->options), true);
        ?>

        <div class="wrap">
            <?php echo '<h4>' . $this->name .' '.'Settings</h4>'; ?>
            <form id="test" class="gf_nutshell_options" method="post" action="update-options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('my_option_group');
                do_settings_sections('my-setting-admin');
                //echo '<input type="text" class="btn btn-primary" id="toggle-%s"  data-toggle="toggle" data-size="large" aria-pressed="false" autocomplete="off">TEST</button>';
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
        $this->options = get_option('form_option_name');
        error_log(print_r($this->options), true);

        global $new_whitelist_options;

        //$opts = $new_whitelist_options['general'];

        error_log(print_r($new_whitelist_options, true));

        // register_setting(
        //     'my_option_group', // Option group
        //     'form_option_name', // Option name
        //     array( $this, 'sanitize' ) // Sanitize
        // );

        add_settings_section(
                'setting_section_id', // ID
                'Choose a form and fields below', // Title
                array( $this, 'print_section_info' ), // Callback
                'my-setting-admin' // Page
        );

        foreach ($forms as $form) {
            add_settings_section(
                    $form['title'], // ID
                    $form['title'], // Title
                    array( $this, 'print_section_info' ), // Callback
                    'my-setting-admin' // Page
                );

            foreach ($form['fields'] as $field) {

                error_log(print_r($field, true));
                $option_name = str_replace(' ', '_', $field->label);
                $option_name = strtolower($option_name);

                error_log(print_r($option_name, true));

                register_setting(
                    $form['title'], // Option group
                    $option_name, // Option name
                    array( $this, 'sanitize' ) // Sanitize
                );

                add_settings_field(
                        $field->label,
                        $option_name,
                        array( $this, 'title_callback'),
                        'my-setting-admin',
                        $form['title'],
                        array('label' => $field->label)
                );
            }
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['id_number'])) {
            $new_input['id_number'] = absint($input['id_number']);
        }

        if (isset($input['title'])) {
            $new_input['title'] = sanitize_text_field($input['title']);
        }

        return $new_input;
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
            isset($this->options['id_number']) ? esc_attr($this->options['id_number']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback($args)
    {
        //$this->options = get_option('form_option_name');

        error_log(print_r($args, true));

        static $id;
        if ($id === null) {
            $id = 0;
        }
        printf(
            sprintf('<input type="checkbox" name="checkbox[%s]" class="btn btn-primary" id="toggle-%s" data-toggle="toggle" data-size="large" aria-pressed="false" autocomplete="off">On</button>', $args['label'], $id)
        );
        $id++;

    }
}
