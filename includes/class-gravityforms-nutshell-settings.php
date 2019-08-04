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
        add_options_page(
            'Settings Admin',
            $this->name,
            'manage_options',
            'gravityforms-nutshell-integration',
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
        do_settings_sections('wp-gf-nutshell-admin');
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
        $api_fields = [];

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
                'wp-gf-nutshell-admin',
                'creds',
                array('title' => 'nutshell_api_username')
            );

        add_settings_field(
                'nutshell_api_key',
                "Enter API key",
                array( $this, 'api_callback'),
                'wp-gf-nutshell-admin',
                'creds',
                array('title' => 'nutshell_api_key')
            );

        add_settings_section(
                'creds', // ID,
                'API info',
                array( $this, 'print_user_info' ), // Callback
                'wp-gf-nutshell-admin'
            );

        foreach ($forms as $form) {
            add_settings_section(
                    $form['title'], // ID
                    $form['title'], // Title
                    array( $this, 'print_section_info' ), // Callback
                    'wp-gf-nutshell-admin'
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
                'wp-gf-nutshell-admin',
                $form['title'],
                array('title' => $form_title)
            );

            register_setting(
                'my_option_group',
                'nutshell_tags_'.$form_title, // Option name
                array( $this, 'sanitize_tags_forms' ) // Sanitize
            );

            add_settings_field(
                'nutshell_tags_'.$form_title,
                "Enter a tag to associate with the form",
                array( $this, 'tags_callback'),
                'wp-gf-nutshell-admin',
                $form['title'],
                array('title' => 'nutshell_tags_'.$form_title)
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
                        array( $this, 'dropdown_option_nutshell_callback' ),
                        'wp-gf-nutshell-admin',
                        $form['title'],
                        array('label' => $form_title, 'field' => $option_name)
                    );

                    register_setting(
                        'my_option_group',
                        'dropdown_option_setting_option_name_'.$form_title .'_'.$option_name
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
        $clean = '';

        if ($clean = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH)) {
            return $clean;
        }
    }

    public function sanitize_tags_forms($input)
    {
        $clean = '';

        if ($clean = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH)) {
            return $clean;
        }
    }

    public function user_callback($args)
    {
        $current_option = $input_text ='';
        $current_option = get_option($args['title']);
        $args['value'] = filter_var($current_option, FILTER_VALIDATE_EMAIL);

        $this->print_text_input($args);
    }

    public function api_callback($args)
    {
        $current_option=$input_text=$clean='';
        $current_option = get_option($args['title']);
        $args['value'] = filter_var($current_option, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        $this->print_text_input($args);
    }

    public function dropdown_option_nutshell_callback($args)
    {

        $the_option = 'dropdown_option_setting_option_name_'.$args['label'].'_'.$args['field'];
        $this->dropdown_option_setting_options = get_option( $the_option);

        ?>
            <select name=<?php echo $the_option.'[dropdown_option_nutshell]' ?> id="dropdown_option_nutshell">
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell_sticky_media_url_sticky_pdf'] === 'name') ? 'selected' : '' ; ?>
                    <option value="name" <?php echo $selected; ?>>Name</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'email') ? 'selected' : '' ; ?>
                    <option value="email" <?php echo $selected; ?>>Email</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'address') ? 'selected'   : '' ; ?>
                    <option value="address" <?php echo $selected; ?>>Address</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'phone') ? 'selected'   : '' ; ?>
                    <option value="phone" <?php echo $selected; ?>>Phone</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'notes') ? 'selected'   : '' ; ?>
                    <option value="notes" <?php echo $selected; ?>>Notes</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'title') ? 'selected'   : '' ; ?>
                    <option value="title" <?php echo $selected; ?>>Title</option>
                <?php $selected = (isset( $this->dropdown_option_setting_options['dropdown_option_nutshell'] ) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'description') ? 'selected'   : '' ; ?>
                    <option value="description" <?php echo $selected; ?>>Description</option>
            </select>
            <?
    }

    public function note_callback($args)
    {
        $current_option=$input_text=$clean='';
        $current_option = get_option($args['title']);
        $args['value'] = filter_var($current_option, FILTER_VALIDATE_EMAIL);

        $this->print_text_input($args, 'email');
    }

    public function tags_callback($args)
    {
        $current_option=$input_text=$clean='';
        $current_option = get_option($args['title']);

        $args['value'] = filter_var($current_option, FILTER_SANITIZE_STRING);

        $this->print_text_input($args, 'tag');
    }

    public function print_section_info()
    {
        return '';
    }

    public function print_user_info()
    {
        return '';
    }

    /*
     * Output text input
     */
    private function print_text_input($args, $type = 'value')
    {
        $placeholder = strcspn(strtolower($type), "aeiou") > 0 ? "Please enter a $type" : "Please enter an $type" ;
        $value = !empty($args['value']) ? $args['value'] : '';

        printf(
            sprintf('<input type="text" id=%s name="%s" placeholder="%s" value="%s" ></input>', $args['title'], $args['title'], $placeholder, $value)
        );
    }
}
