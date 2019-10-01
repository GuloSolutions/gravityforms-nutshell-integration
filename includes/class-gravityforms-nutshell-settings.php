<?php

class GravityNutshellSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private $options;
    private $name;
    private static $id;
    private $labels;
    private $tags;
    private $customFields;


    /**
     * Start up.
     */
    public function __construct($name)
    {
        global $gravity_forms;

        if (null === $gravity_forms) {
            $gravity_forms = new Controllers\GravityFormsController();
        }

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_init', array($this, 'setApiUsers'));
        $this->name = $name;
        $this->tags = $gravity_forms->findTags();
        $this->customFields = $gravity_forms->findCustomFields();


        foreach($this->customFields as $k=>$v) {
            if (is_array($v)) {
                foreach($v as $vv){
                    error_log(print_r($vv->name, true));
                }
            }
        }
    }

    /**
     * Add options page.
     */
    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            $this->name,
            'manage_options',
            'gravityforms-nutshell-integration',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback.
     */
    public function create_admin_page()
    {
        ?>
<div class="wrap">
    <?php echo '<h4>'.$this->name.' '.'Settings</h4>'; ?>
    <form id="wp_gf_options_settings" class="gf_nutshell_options" method="post" action="options.php">
        <?php
                settings_fields('my_option_group');
        do_settings_sections('wp-gf-nutshell-admin');
        submit_button(); ?>
    </form>
</div>
<?php
    }

    /**
     * Register and add settings.
     */
    public function page_init()
    {
        $forms = GFAPI::get_forms();
        $api_fields = [];

        register_setting(
            'my_option_group', // Option group
            'nutshell_api_username', // Option name
            array($this, 'sanitize_email') // Sanitize
        );

        register_setting(
            'my_option_group', // Option group
            'nutshell_api_key', // Option name
            array($this, 'sanitize_apikey') // Sanitize
        );

        add_settings_field(
            'nutshell_api_username',
            'Enter API username',
            array($this, 'user_callback'),
            'wp-gf-nutshell-admin',
            'creds',
            array('title' => 'nutshell_api_username')
        );

        add_settings_field(
            'nutshell_api_key',
            'Enter API key',
            array($this, 'api_callback'),
            'wp-gf-nutshell-admin',
            'creds',
            array('title' => 'nutshell_api_key')
        );

        add_settings_section(
            'creds', // ID,
                'API info',
            array($this, 'print_user_info'), // Callback
                'wp-gf-nutshell-admin'
        );

        foreach ($forms as $form) {
            add_settings_section(
                $form['title'], // ID
                $form['title'], // Title
                array($this, 'print_section_info'), // Callback
                'wp-gf-nutshell-admin'
            );

            $form_title = $this->cleanFormTitle($form['title']);

            foreach ($form['fields'] as $field) {
                if (!empty($field->label)) {
                    $option_name = str_replace(' ', '_', $field->label);
                    $option_name = strtolower($option_name);
                    $option_name .= '_'.$form_title;
                    $form_labels[] = $option_name;

                    add_settings_field(
                        $form_title,
                        'Select a Nutshell user to associate with the form',
                        array($this, 'dropdown_option_users_callback'),
                        'wp-gf-nutshell-admin',
                        $form['title'],
                        array('title' => $form_title, 'field' => 'api_users')
                    );

                    register_setting(
                        'my_option_group', // Option group
                        'dropdown_option_setting_api_users_'.$form_title.'_api_users'
                    );

                    add_settings_field(
                        $form_title.'_tags',
                        'Select a tag for this form',
                        array($this, 'dropdown_option_tags_callback'),
                        'wp-gf-nutshell-admin',
                        $form['title'],
                        array('label' => $form_title, 'field' => '_api_tags')
                    );

                    register_setting(
                        'my_option_group',
                        'dropdown_option_setting_tag_name_'.$form_title.'_api_tags'
                    );

                    add_settings_field(
                        $option_name,
                        $field->label,
                        array($this, 'dropdown_option_nutshell_callback'),
                        'wp-gf-nutshell-admin',
                        $form['title'],
                        array('label' => $form_title, 'field' => $option_name)
                    );

                    register_setting(
                        'my_option_group',
                        'dropdown_option_setting_option_name_'.$form_title.'_'.$option_name
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
            return 'Please enter a valid email';
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
        $current_option = $input_text = '';
        $current_option = get_option($args['title']);
        $args['value'] = filter_var($current_option, FILTER_VALIDATE_EMAIL);

        $this->print_text_input($args);
    }

    public function api_callback($args)
    {
        $current_option = $input_text = $clean = '';
        $current_option = get_option($args['title']);
        $args['value'] = filter_var($current_option, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        $this->print_text_input($args);
    }

    public function dropdown_option_nutshell_callback($args)
    {
        $the_option = 'dropdown_option_setting_option_name_'.$args['label'].'_'.$args['field'];

        $this->dropdown_option_setting_options = get_option($the_option); ?>
<select name=<?php echo $the_option.'[dropdown_option_nutshell]'; ?>
    id="dropdown_option_nutshell">
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'name') ? 'selected' : ''; ?>
    <option value="name" <?php echo $selected; ?>>Name</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'email') ? 'selected' : ''; ?>
    <option value="email" <?php echo $selected; ?>>Email</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'address') ? 'selected' : ''; ?>
    <option value="address" <?php echo $selected; ?>>Address</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'phone') ? 'selected' : ''; ?>
    <option value="phone" <?php echo $selected; ?>>Phone</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'notes') ? 'selected' : ''; ?>
    <option value="notes" <?php echo $selected; ?>>Notes</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'title') ? 'selected' : ''; ?>
    <option value="title" <?php echo $selected; ?>>Title</option>
    <?php $selected = (isset($this->dropdown_option_setting_options['dropdown_option_nutshell']) && $this->dropdown_option_setting_options['dropdown_option_nutshell'] === 'description') ? 'selected' : ''; ?>
    <option value="description" <?php echo $selected; ?>>Description
    </option>
    <?php
        foreach($this->customFields as $k=>$v) {
            if (is_array($v)) {
                foreach($v as $vv){
                    ?>
                    <?php $selected = (isset($this->dropdown_option_tags['dropdown_option_nutshell']) && $this->dropdown_option_tags['dropdown_option_nutshell'] === str_replace(' ', '_', $vv->name)) ? 'selected' : ''; ?>
                    <option value=<?php echo str_replace(' ', '_', $vv->name); ?>
                        <?php echo $selected; ?>><?php echo $vv->name; ?>
                    </option>
                    <?php
                }
            }
        }
        echo '</select>';
    }

    public function dropdown_option_users_callback($args)
    {
        $the_option_users = 'dropdown_option_api_users';
        $the_option = 'dropdown_option_setting_api_users_'.$args['title'].'_api_users';

        $this->dropdown = get_option($the_option);

        if (!isset($this->dropdown['dropdown_option_api_users'])) {
            $this->dropdown['dropdown_option_api_users'] = get_option('nutshell_api_username');
        }

        $this->dropdown_option_api_users = array_values(get_option($the_option_users)); ?>
<select name=<?php echo $the_option.'[dropdown_option_api_users]'; ?>
    id='dropdown_option_api_users'>
    <?php

        $total = count($this->dropdown_option_api_users);

        for ($i = 0; $i < $total; $i++) {
            ?>
    <?php $selected = (isset($this->dropdown['dropdown_option_api_users']) && $this->dropdown['dropdown_option_api_users'] == $this->dropdown_option_api_users[$i][1]) ? 'selected' : ''; ?>
    <option value=<?php echo $this->dropdown_option_api_users[$i][1]; ?> <?php echo $selected; ?>><?php echo $this->dropdown_option_api_users[$i][0]; ?>
    </option>
    <?php
        }
        echo '</select>';
    }

    public function dropdown_option_tags_callback($args)
    {
        $the_option = 'dropdown_option_setting_tag_name_'.$args['label'].'_api_tags';
        $class = "class='tags_selected'";

        $this->dropdown_option_tags = get_option($the_option); ?>
    <select name=<?php echo $the_option.'[dropdown_option_api_tags][]'; ?>
        <?php echo 'multiple'; ?>
        id="dropdown_option_api_tags">
        <?php
            foreach ($this->tags->Contacts as $tag) {
                ?>
        <?php $selected = (isset($this->dropdown_option_tags['dropdown_option_api_tags']) && in_array(str_replace(' ', '_', $tag), $this->dropdown_option_tags['dropdown_option_api_tags'])) ? 'selected' : ''; ?>
        <option value=<?php echo str_replace(' ', '_', $tag); ?>
            <?php echo $selected; ?> <?php if ($selected): echo $class;
                endif; ?>><?php echo $tag; ?>
        </option>
        <?php
            }
        echo '</select>';
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
    public function print_text_input($args, $type = 'value')
    {
        $placeholder = strcspn(strtolower($type), 'aeiou') > 0 ? "Please enter a $type" : "Please enter an $type";
        $value = !empty($args['value']) ? $args['value'] : '';

        printf(
            sprintf('<input type="text" id=%s name="%s" placeholder="%s" value="%s" ></input>', $args['title'], $args['title'], $placeholder, $value)
        );
    }

    // set api users in transients; renew in a week
    public function setApiUsers()
    {
        if (false === ($api_users = get_transient('_s_nutshell_users_results'))) {
            $api_users = $this->getApiUsers();
            set_transient('_s_nutshell_users_results', $api_users, 7 * DAY_IN_SECONDS);
            update_option('dropdown_option_api_users', $api_users);
        }
    }

    public function getApiUsers()
    {
        global $gravity_forms;

        $api_users = [];
        $users = $gravity_forms->findApiUsers();

        foreach ($users as $user) {
            if ($user->isEnabled) {
                $api_users[] = [$user->name, $user->emails[0]];
            }
        }

        return $api_users;
    }

    public function cleanFormTitle($raw_title)
    {
        $form_title = preg_replace('/[^A-Za-z0-9 ]/', '', $raw_title);
        $form_title = str_replace(' ', '_', strtolower($form_title));

        return $form_title;
    }
}
