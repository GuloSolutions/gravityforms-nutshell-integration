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
            $newContactId = $form_title = $form_owner = '';
            $fields_to_update = [];
            $idLabelMap = [];
            $dataToSend = [];

            $form_title = str_replace(' ', '_', strtolower($form['title']));

            foreach ($form['fields'] as $field) {
                if (!empty($field->label)) {
                    $option_name = str_replace(' ', '_', $field->label);
                    $option_name = strtolower($option_name);
                    $option_name .= '_'.$form_title;
                    $all_options[] = $option_name;
                }
            }

            //get form owner
            $form_owner = get_option($form_title);
            $notes = 'notes';

            foreach ($form['fields'] as $field) {
                 $option_name = str_replace(' ', '_', $field->label);
                 $option_name = strtolower($option_name);
                 $option_name .= '_'.$form_title;
                $idLabelMap[$field->id] = $field->label;
            }


            $options = get_option('checkbox');

            // exit;

            foreach ($entry as $k=>$v) {
                if (array_keys($idLabelMap, $k) !== null) {
                    if (!empty($idLabelMap[$k])) {
                        $dataToSend[strtolower($idLabelMap[$k])] = $v;
                    }
                }
            }

            foreach($dataToSend as $k=>$v){
                 $option_name = str_replace(' ', '_', $k);
                 $option_name = strtolower($option_name);
                 $option_name .= '_'.$form_title;

                 if (in_array($option_name, array_keys($options)) && $options[$option_name] == 'on'){
                    unset($dataToSend[$k]);
                    $dataToSend[$notes] = $v;
                }
            }

            error_log(print_r($dataToSend, true));

            exit;

            $contacts = $gravity_forms->getContacts();

            foreach ($contacts as $contact) {
                $contact->name = strtolower($contact->name);
                $names[] = $contact->name;
            }

            if (array_search($dataToSend['name'], $names) > 0) {
                return;
            }

            $editContact = $gravity_forms->getContact(13604);

            error_log(print_r($editContact, true));

            exit;

            $email = 'dwqqssssqqeeq@c.com';

            $emailKey = array_search($email, (array) $editContact->email);
            $phoneKey = array_search($phone, (array) $editContact->phone);
            $notesKey = array_search($notes, (array) $editContact->notes);

            $editContact->phone = (array) $editContact->phone;
            $editContact->email = (array) $editContact->email;
            $editContact->rev = (array) $editContact->rev;
            $editContact->notes = (array) $editContact->notes;

            if (!$emailKey || !$phoneKey) {
                $fields_to_update['email'] = $dataToSend['email'];
                $fields_to_update['phone'] = $dataToSend['phone'];
                $gravity_forms->editContact($editContact, $fields_to_update);
            }


            if (!empty($dataToSend['organization'])) {
                    $fields_to_update['company'] = $dataToSend['organization'];
            }


            $params['contact'] = $dataToSend;

            if ($newContactId = $gravity_forms->addContact($params)) {
                error_log(print_r('Created new contact with ID ', true));
                error_log(print_r($newContactId, true));
            } else {
                throw new Exception($e);
                error_log(print_r('Failed to create contact', true));
                error_log(print_r($dataToSend, true));
            }
        }
    }

    public function pre_render_add_note()
    {
        add_action('gform_pre_render', 'set_is_note', 10, 1);

        function set_is_note($form)
        {
            if ($form['title'] !=  'Newsletter') {
                error_log(print_r($form, true));

                error_log(print_r('in prerender2', true));
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

    //convert nested object to array
    public static function object_to_array($obj)
    {
        if (is_object($obj)) {
            $obj = (array) Gravityforms_Nutshell_Integration_Public::dismount($obj);
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = Gravityforms_Nutshell_Integration_Public::object_to_array($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }

    public static function dismount($object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }
}
