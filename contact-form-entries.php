<?php
/*
Plugin Name: Contact Form 7 - Entries
Plugin URI: 
Description: Contact form 7 entries.
Author: Repon Hossain
Author URI: 
Text Domain: contact-form-7-entries
Domain Path: /languages/
Version: 1.0.1
*/

define( 'WPCF7_ENTRIES_VERSION', '1.0.1' );

define( 'WPCF7_ENTRIES_TEXT_DOMAIN', 'contact-form-7-entries' );

define( 'WPCF7_ENTRIES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'WPCF7_ENTRIES_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

define( 'WPCF7_ENTRIES_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once WPCF7_PLUGIN_DIR . '/load.php';
