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
        error_log(print_r($this->options), true); ?>

        <div class="wrap">
            <?php echo '<h4>' . $this->name .' '.'Settings</h4>'; ?>
            <form id="test" class="gf_nutshell_options" method="post" action="update-options.php">
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
        $this->options = get_option('form_option_name');
        error_log(print_r($this->options), true);

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

                register_setting(
                    $form['title'], // Option group
                    $option_name, // Option name
                    array( $this, 'sanitize' ) // Sanitize
                );

                add_settings_field(
                        $field->label,
                        $field->label,
                        array( $this, 'title_callback'),
                        'my-setting-admin',
                        $form['title'],
                        array('label' => $option_name)
                );
            }
        }
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback($args)
    {
        $current_option = $input_text ='';

        static $id;
        if ($id === null) {
            $id = 0;
        }

        $current_option = get_option($args['label']);

        if ('1' === $current_option) {
            $current_option = 'checked';
            $input_text = 'on';
        } else {
            $current_option = '';
            $input_text = 'off';
        }

        printf(
            sprintf('<input type="checkbox" name="checkbox[%s]" class="btn btn-primary" %s id="toggle-%s"  data-toggle="toggle" data-size="large" aria-pressed="false" autocomplete="off">%s</button>', $args['label'], $current_option, $id, $input_text)
        );
        $id++;
    }
}
