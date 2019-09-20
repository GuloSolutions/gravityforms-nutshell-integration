=== Nutshell Gravity Forms WordPress Plugin ===

Contributors: radboris, zwilson, fsimmons
Donate link: https://www.gulosolutions.com/
Tags: api, forms, gravityforms, Nutshell, crm
Requires at least: 3.0.1
Tested up to: 5.2.1
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin that integrates GravityForms and the Nutshell API. Update Nutshell user accounts on form submission with selected fields.

== Description ==

The plugin creates Nutshell entries form GravityForms submissions. It allows users to select form field as notes for nutshell entries. Provide email for each available form. The info will be routed to the specific Nutshell user

== Installation ==

* Include the plugin in the main composer file under the package and require keys:
   ```
   {
      "url": "https://github.com/GuloSolutions/gravityforms-nutshell-integration.git",
      "type": "git"
   }
   ```

   ```
     "gulo-solutions/gravityforms-nutshell-integration": "dev-master"
   ```
1. run `composer update` or `composer install`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under the Settings tab, add the Nutshell API keys. View the active GravityForms on the site and enter a Nutshell user email  that will be associated with the form. Subsequent form submissions will be directed to this email. You can also choose which form fields will be designated as note to the Nutshell user

== Frequently Asked Questions ==

* What needs to be done in a future release?

  Add Nutshell tags to Contacts from admin

== Upgrade Notice ==

== Screenshots ==

== Changelog ==

1.0.0 - 2019-03-01

* Initial release

1.0.1 - 2019-03-04

* Add API creds in admin section

1.0.2 - 2019-03-04

* Remove unused imports

1.0.3 - 2019-04-09

* Add more Nutshell API methods
* Remove API info after uninstall
* Run frontend functionality if API keys exist

1.1.0 - 2019-04-09

* Latest tag only stable

1.1.1 - 2019-05-22

* Changes to the admin interface, plugin name, sanitization of input, admin forms
* Catch NUTSHELL API errors if no key or missing user

## 1.1.2 - 2019-07-31

- Minor improvements and cleanup
- Add utm code

## 1.1.3 - 2019-08-01

- Admin form improvements: fixed: placeholder text and value text are now separate
- Author name fixed

## 1.1.4 - 2019-08-01

- Add settings link

## 1.1.5 - 2019-09-20

- Add dropdown fields for settings derived from the Nutshell API, including users and tags
- Add Nutshell API methods to allow appending tags and source url to a form and Nutshell Contact
