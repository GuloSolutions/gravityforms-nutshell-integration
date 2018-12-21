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

        $gravity_forms = Controllers\GravityFormsController::getInstance();
    }

    public function post_to_nutshell_2($form)
    {
        global $gravity_forms;

        $saved_form_ids = [];

        $saved_form_ids = get_option('my_option_name');

        if ($form->form_id == $saved_form_ids['id_number']) {
            $names = [];
            $params = [];

            $id = $name = $email = $phone = '';

            $field_object = RGFormsModel::get_form_meta($form['form_id']);

            foreach ($field_object['fields'] as $field) {
                if (strtolower($field->label) == 'name') {
                    $id = $field->id;
                    $name = $form[$id];
                }

                if (strtolower($field->label) == 'email') {
                    $id = $field->id;
                    $email = $form[$id];
                }

                if (strtolower($field->label) == 'phone') {
                    $id = $field->id;
                    $phone = $form[$id];
                }
            }

            $contacts = $gravity_forms->getContacts();

            foreach ($contacts as $contact) {
                error_log(print_r(strtolower($contact->name), true));
                $contact->name = strtolower($contact->name);
                $names[] = $contact->name;
            }

            $name = strtolower($name);

            if (array_search($name, $names) > 0) {
                return;
            } else {
                $params = [
                        'name' => ucwords($name),
                        'phone' => $phone,
                        'email' => $email
                ];

                $gravity_forms->addContact($params);
            }
        }
    }
}
