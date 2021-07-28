<?php

/**
 * Plugin Name: Contact Form 7 - Entries
 * Plugin URI: 
 * Description: Contact form 7 entries.
 * Author: Repon Hossain
 * Author URI: 
 * Text Domain: wpcf7-entries
 * Domain Path: /languages/
 * Version: 1.0.1
 * License: GPL2+
 *
 * @package wpcf7-entries
 */


// Define constants.
define( 'WPCF7_ENTRIES_VERSION', '1.0.1' );
define( 'WPCF7_ENTRIES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCF7_ENTRIES_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WPCF7_ENTRIES_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );


require_once dirname( __FILE__ ) . '/includes/class-wpcf7-entries.php';

/**
 * Main instance of Contact form 7 entries.
 *
 * Returns the main instance of Contact Form 7 Entries to prevent the need to use globals.
 *
 * @since  1.0.1
 * @return WPCF7_Entries
 */
function WPCF7_Entries() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
	return WPCF7_Entries::instance();
}

$GLOBALS['WPCF7_Entries'] = WPCF7_Entries();

// Activation - works with symlinks.
register_activation_hook( __FILE__, array( WPCF7_Entries(), 'activate' ) );