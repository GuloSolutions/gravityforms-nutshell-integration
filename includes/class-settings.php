<?php
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $name;

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
        $this->options = get_option('my_option_name'); ?>
        <div class="wrap">
            <?php echo '<h3>' . $this->name .' '.'settings</h3>'; ?>

            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
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

        register_setting(
            'my_option_group', // Option group
            'form_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

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
                $button = sprintf('<button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">%s</button>', $field->label);

                add_settings_field(
                        $field->label,
                        $button,
                        array( $this, 'title_callback' ),
                        'my-setting-admin',
                        $form['title']
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
    // public function title_callback()
    // {
    //     printf(
    //         '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
    //         isset($this->options['title']) ? esc_attr($this->options['title']) : ''
    //     );
    // }
}
