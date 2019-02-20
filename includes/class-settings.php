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
            <form id="test" name= "settingsform" class="gf_nutshell_options" method="post" action="update-options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('my_option_group');
        do_settings_sections('my-setting-admin');
        //submit_button();
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
        $this->options = get_option('form_option_name');

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

            $form_title = str_replace(' ', '_', strtolower($form['title']));

            foreach ($form['fields'] as $field) {
                $option_name = str_replace(' ', '_', $field->label);
                $option_name = $option_name;
                $option_name = strtolower($option_name);
                $option_name .= '_' . $form_title;
                $form_labels[] = $option_name;
                $this->labels[] = $option_name;
                $all_options[] = $option_name;

                register_setting(
                    'my_option_group', // Option group
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
            add_settings_field(
                    "nutshell",
                    "Select a Nutshell user to associate with the form",
                    array( $this, 'note_callback'),
                    'my-setting-admin',
                    $form['title'],
                    array('label' => $option_name)
            );
        }
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback($args)
    {
        $current_option = $input_text ='';

        error_log(print_r($args, true));

        static $id;
        if ($id === null) {
            $id = 0;
        }

        $current_option = get_option($args['label']);

        if (!empty($current_option)) {
            $current_option = 'checked';
            $input_text = 'on';
        } else {
            $current_option = '';
            $input_text = 'off';
        }
        printf(
            sprintf('<button input type="checkbox" name="checkbox[%s]"  class="btn btn-primary" %s id="toggle-%s"  data-toggle="toggle" data-size="large" aria-pressed="false" autocomplete="off">%s</button>', $args['label'], $current_option, $id, $input_text)
        );
        $id++;
    }

    public function note_callback($args)
    {
        printf(
            sprintf('<select name="nutshell">
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                    </select>', $args['label'])
        );
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
