<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.gulosolutions.com/
 * @since      1.0.0
 *
 * @package    Gravityforms_Nutshell_Integration
 * @subpackage Gravityforms_Nutshell_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gravityforms_Nutshell_Integration
 * @subpackage Gravityforms_Nutshell_Integration/public
 * @author     Gulo <Gulo Solutions>
 */
class Gravityforms_Nutshell_Integration_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The Nutshell API var.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $nutshell;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
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

        /**
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/gravityforms-nutshell-integration-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/gravityforms-nutshell-integration-public.js', array( 'jquery' ), $this->version, false);
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
            $newContactId = $form_title = $form_owner = $user_id = '';
            $notes = 'notes';
            $fields_to_update = $idLabelMap = $dataToSend = $new_contact = $editContact = $users = [];

            $form_title = str_replace(' ', '_', strtolower($form['title']));

            foreach ($form['fields'] as $field) {
                if (!empty($field->label)) {
                    $option_name = str_replace(' ', '_', $field->label);
                    $option_name = strtolower($option_name);
                    $option_name .= '_'.$form_title;
                    $all_options[] = $option_name;
                }
            }
            // get form owner for admin
            $form_owner = get_option($form_title);

            // get user id
            try {
                $users = $gravity_forms->findUsers($form_owner);
                // throw an exception if the user  is a Contact or does not exist
                if ($users[0]->entityType != 'Users') {
                    throw new Exception('No user(s) with this email');
                }
            } catch (Exception $e) {
                error_log(print_r($e->getMessage(), true));
                return;
            }

            $filtered = array_filter((array)$users, function ($v) {
                return $v->entityType != 'Contacts';
            });

            $user_id = array_values($filtered)[0]->id;

            foreach ($form['fields'] as $field) {
                $option_name = str_replace(' ', '_', $field->label);
                $option_name = strtolower($option_name);
                $option_name .= '_'.$form_title;
                $idLabelMap[$field->id] = $field->label;
            }

            // get options for admin form
            $options = get_option('checkbox');

            foreach ($entry as $k=>$v) {
                if (array_keys($idLabelMap, $k) !== null) {
                    if (!empty($idLabelMap[$k])) {
                        $dataToSend[strtolower($idLabelMap[$k])] = $v;
                    }
                }
            }

            foreach ($dataToSend as $k=>$v) {
                $option_name = str_replace(' ', '_', $k);
                $option_name = strtolower($option_name);
                $option_name .= '_'.$form_title;

                if (in_array($option_name, array_keys($options)) && $options[$option_name] == 'on') {
                    unset($dataToSend[$k]);
                    $dataToSend[$notes] = $v;
                }
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

                if (!$emailKey || !$phoneKey) {
                    $fields_to_update['email'] = $dataToSend['email'];
                    $fields_to_update['phone'] = $dataToSend['phone'];
                    $fields_to_update['owner'] = ['entityType' => 'Users', 'id' =>  $user_id];
                }

                if (!empty($dataToSend['organization'])) {
                    $fields_to_update['description'] = $dataToSend['organization'];
                }

                $gravity_forms->editContact($editContact, $fields_to_update);

                if (!empty($dataToSend[$notes])) {
                    $gravity_forms->addNote(['entity' => ['entityType' =>'Contacts', 'id' => $editContact->id]], $dataToSend[$notes]);
                }
            } else {
                $new_contact['name'] = $dataToSend['name'];
                $new_contact['email'] = $dataToSend['email'];
                $new_contact['phone'] = $dataToSend['phone'];
                $new_contact['owner'] = ['entityType' => 'Users', 'id' =>  $user_id];

                if (!empty($dataToSend['organization'])) {
                    $new_contact['description'] = $dataToSend['organization'];
                }

                $params['contact'] = $new_contact;

                if ($newContactId = $gravity_forms->addContact($params)) {
                    if (!empty($dataToSend[$notes])) {
                        $gravity_forms->addNote(['entity' => ['entityType' =>'Contacts', 'id' => $newContactId]], $dataToSend[$notes]);
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
            if ($form['title'] !=  'Newsletter') {
                $props = array(
                'id' => 100,
                'type' => 'hidden',
                'value' => 'Test'
            );
                $field = GF_Fields::create($props);
                array_push($form['fields'], $field);

                return $form;
            }
        }
    }
}
