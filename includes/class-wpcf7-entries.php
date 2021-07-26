<?php
/**
 * File containing the class Contact_Form7_Entries.
 *
 * @package wpcf7-entries
 * @since   1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles core plugin hooks and action setup.
 *
 * @since 1.0.1
 */
class WPCF7_Entries {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0.1
	 */
	private static $instance = null;

	/**
	 * Main Contact Form7 Entries Instance.
	 *
	 * @since  1.0.1
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Includes.
		if ( is_admin() ) {
			require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-admin.php';
		}

		// Actions.
		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
	}

	/**
	 * Performs plugin activation steps.
	 */
	public function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		global $wpdb;

		$table_name = $wpdb->prefix . 'wpcf7_entries';

		maybe_create_table($table_name, "CREATE TABLE $table_name (
			`ID` INT NOT NULL AUTO_INCREMENT , 
			`form_id` INT NOT NULL , 
			`email` VARCHAR(100) NULL , 
			`name` VARCHAR(100) NULL , 
			`subject` VARCHAR(100) NULL , 
			`submitted_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			PRIMARY KEY (`ID`))
		");

		$table_name = $table_name . '_fields';		
		maybe_create_table($table_name, "CREATE TABLE $table_name (
			`ID` INT NOT NULL AUTO_INCREMENT , 
			`entry_id` INT NOT NULL , 
			`field_id` VARCHAR(100) NOT NULL, 
			`value` MEDIUMTEXT, 
			PRIMARY KEY (`ID`))
		");
	}

	/**
	 * Loads textdomain for plugin.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'contact-form-7-entries', false, WPCF7_ENTRIES_PLUGIN_DIR . '/languages/' );
	}
}
