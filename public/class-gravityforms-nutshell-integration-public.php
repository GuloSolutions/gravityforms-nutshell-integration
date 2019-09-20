<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @see       https://www.gulosolutions.com/
 * @since      1.0.0
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author     Gulo <Gulo Solutions>
 */
class Gravityforms_Nutshell_Integration_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $plugin_name;

    /**
     * The Nutshell API var.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $nutshell;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of this plugin
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name the name of the plugin
     * @param string $version     the version of this plugin
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gravityforms_Nutshell_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gravityforms_Nutshell_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/gravityforms-nutshell-integration-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gravityforms_Nutshell_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gravityforms_Nutshell_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
    }

    public function startService()
    {
        global $gravity_forms;
        $gravity_forms = new Controllers\GravityFormsController();
    }

    public function after_submission()
    {
        add_action('gform_after_submission', 'send_data_to_nutshell', 10, 2);

        function send_data_to_nutshell($entry, $form)
        {
            global $gravity_forms;
            $newContactId = $form_title = $form_owner = $user_id = $source_url = '';
            $notes = 'notes';
            $fields_to_update = $idLabelMap = $data_to_send = $new_contact = $editContact = $users = $all_options = $form_options = $dataToSend = [];

            $form_title = str_replace(' ', '_', strtolower($form['title']));

            foreach ($form['fields'] as $field) {
                if (!empty($field->label)) {
                    $option_name = str_replace(' ', '_', $field->label);
                    $option_name = strtolower($option_name);
                    $form_option = 'dropdown_option_setting_option_name_'.$form_title.'_'.$option_name.'_'.$form_title;
                }
                $data_to_send[$option_name] = get_option($form_option)['dropdown_option_nutshell'];
            }

            // get form owner for admin
            $form_owner = get_option('dropdown_option_setting_api_users_'.$form_title.'_api_users')['dropdown_option_api_users'];

            // get tags for admin
            $tags_array = get_option('dropdown_option_setting_tag_name_'.$form_title.'_api_tags')['dropdown_option_api_tags'];

            $tag_object = new ArrayObject(array());

            if (count($tags_array) > 1) {
                foreach($tags_array as $tag){
                    $tag_object->append($tag);
                }
            } elseif (count($tags_array) == 1) {
                $tag_object->append = $tags_array[0];
            }

            foreach ($entry as $k => $v) {
                if (array_keys($idLabelMap, $k) !== null) {
                    if (!empty($idLabelMap[$k])) {
                        $dataToSend[strtolower($idLabelMap[$k])] = $v;
                    }
                }
            }

            foreach ($form['fields'] as $field) {
                $option_name = str_replace(' ', '_', $field->label);
                $option_name = strtolower($option_name);
                $option_name .= '_'.$form_title;
                $idLabelMap[$field->id] = $field->label;
            }

            foreach ($entry as $k => $v) {
                if (array_keys($idLabelMap, $k) !== null) {
                    if (!empty($idLabelMap[$k])) {
                        $dataToSend[strtolower($idLabelMap[$k])] = $v;
                    }
                }
            }

            if (null !== $tags_array) {
                $dataToSend['tags'] = $tag_object;
            }

            $users = $gravity_forms->findApiUsers($form_owner);

            foreach($users as $user) {
                if (in_array($form_owner, $user->emails)){
                    $user_id = $user->id;
                    break;
                }
            }

            foreach ($entry as $k => $v) {
                if ($k == 'source_url'){
                    $source_url = $v;
                }
            }

            $custom_fields = $gravity_forms->findCustomFields();

            if ($custom_fields->Contacts[0]->name == 'Source URL') {
                $custom_fields_object = new ArrayObject(array());
                $custom_fields_object[$custom_fields->contacts->name] = $source_url;
                $dataToSend['customFields'] = $custom_fields_object;
             }

            // search methods return stubs; get methods full info
            $contact = $gravity_forms->searchByEmail($dataToSend['email']);

            if (!empty($contact->contacts[0]->id)) {
                $editContact = $gravity_forms->getContact($contact->contacts[0]->id);

                $emailKey = array_search($dataToSend['email'], (array) $editContact->email);
                $phoneKey = array_search($dataToSend['phone'], (array) $editContact->phone);

                $editContact->phone = (array) $editContact->phone;
                $editContact->email = (array) $editContact->email;
                $editContact->rev = (array) $editContact->rev;
                $editContact->notes = (array) $editContact->notes;

                if (property_exists($editContact, 'tags')){
                    $editContact->tags = (array) $editContact->tags;
                }

                if (!$emailKey || !$phoneKey) {
                    $fields_to_update['email'] = $dataToSend['email'];
                    $fields_to_update['phone'] = $dataToSend['phone'];
                    $fields_to_update['owner'] = ['entityType' => 'Users', 'id' => $user_id];
                }

                if (!empty($dataToSend['organization'])) {
                    $fields_to_update['description'] = $dataToSend['organization'];
                }

                if (!empty($dataToSend['tags'])) {
                    $tags = array_merge($editContact->tags, $dataToSend['tags']);
                    $fields_to_update['tags'] = $dataToSend['tags'];
                }

                $gravity_forms->editContact($editContact, $fields_to_update);

                if (!empty($dataToSend[$notes])) {
                    $gravity_forms->addNote(['entity' => ['entityType' => 'Contacts', 'id' => $editContact->id]], $dataToSend[$notes]);
                }
            } else {
                $new_contact['name'] = $dataToSend['name'];
                $new_contact['email'] = $dataToSend['email'];
                $new_contact['phone'] = $dataToSend['phone'];
                $new_contact['owner'] = ['entityType' => 'Users', 'id' => $user_id];

                if (!empty($dataToSend['organization'])) {
                    $new_contact['description'] = $dataToSend['organization'];
                }

                if (!empty($dataToSend['tags'])) {
                    $new_contact['tags'] = $dataToSend['tags'];
                }

                $params['contact'] = $new_contact;

                if ($newContactId = $gravity_forms->addContact($params)) {

                    if (!empty($dataToSend[$notes])) {
                        $gravity_forms->addNote(['entity' => ['entityType' => 'Contacts', 'id' => $newContactId, 'name' => $dataToSend['name']]], $dataToSend[$notes]);
                    }
                } else {
                    throw new Exception($e);
                }
            }
        }
    }

    public function pre_render_add_note()
    {
        add_action('gform_pre_render', 'set_is_note', 10, 1);

        function set_is_note($form)
        {
            if ($form['title'] != 'Newsletter') {
                $props = array(
                'id' => 100,
                'type' => 'hidden',
                'value' => 'Test',
            );
                $field = GF_Fields::create($props);
                array_push($form['fields'], $field);

                return $form;
            }
        }
    }
}
