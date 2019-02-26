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
        // Set class property
        $this->options = get_option('my_option_name'); ?>

        <div class="wrap">
            <?php echo '<h4>' . $this->name .' '.'Settings</h4>'; ?>
            <form id="test" class="gf_nutshell_options" method="post" action="options.php">
                <?php
                settings_fields('my_option_group');
            do_settings_sections('my-setting-admin');
         submit_button();
        ?>
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
            'my_option_name' // Option name
            //array( $this, 'sanitize' ) // Sanitize
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
        }
    }

    public function note_callback($args)
    {

        $current_option = $input_text ='';
        $current_option = get_option($args['title']);

        printf(
            sprintf('<input type="text" id=%s name="%s" value="%s"></input>', $args['title'], $args['title'], !empty($current_option) ? $current_option: "Please enter an email")
        );
    }

    public function sanitize( $input )
    {
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

    public function getOptions()
    {
        return $this->labels;
    }
}
