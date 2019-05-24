=== Nutshell Gravity Forms WordPress Plugin ===

Contributors: radboris, zwilson, fsimmons
Tags: api, forms, gravityforms, Nutshell, crm
Donate link: https://www.gulosolutions.com/
Requires at least: 3.0.1
Tested up to: 5.2
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin that integrates GravityForms and the Nutshell API. Update Nutshell user accounts on form submission with selected fields.

== Description ==

The plugin creates Nutshell entries form GravityForms submissions. It allows users to select form field as notes for nutshell entries. Provide email for each available form. The info will be routed to the specific Nutshell user

== Installation ==

1. Include the plugin in the main composer file under the package and require keys:
   ### package
   ```
   {
      "url": "https://github.com/GuloSolutions/gravityforms-nutshell-integration.git",
      "type": "git"
   }
   ```

   ### require
   ```
     "gulo-solutions/gravityforms-nutshell-integration": "dev-master"
   ```
1. run `composer update` or `composer install`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under the Settings tab, add the Nutshell API keys. View the active GravityForms on the site and enter a Nutshell user email  that will be associated with the form. Subsequent form submissions will be directed to this email. You can also choose which form fields will be designated as note to the Nutshell user

== Frequently Asked Questions ==

## What needs to be done in a future release? 

- Addng Nutshell tags to Contacts from admin

=== Upgrade Notice ===

== Screenshots ==

=== Changelog ===

## 1.0.0 - 2019-03-01

- Initial release

## 1.0.1 - 2019-03-04

- Add API creds in admin section

## 1.0.2 - 2019-03-04

- Remove unused imports

## 1.0.3 - 2019-04-09

- Add more Nutshell API methods
- Remove API info after uninstall
- Run frontend functionality if API keys exist

## 1.1.0 - 2019-04-09

- Latest tag only stable

## 1.1.1 - 2019-05-22

- Changes to the admin interface, plugin name, sanitization of input, admin forms
- Catch NUTSHELL API errors if no key or missing user
