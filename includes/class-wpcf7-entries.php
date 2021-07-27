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

		//getting the response after sent email
		add_action('wpcf7_mail_sent', [$this, 'wpcf7_save_entry']);
		//$this->wpcf7_save_entry('');
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
		load_plugin_textdomain( 'wpcf7-entries', false, WPCF7_ENTRIES_PLUGIN_DIR . '/languages/' );
	}

	public function wpcf7_save_entry($contact_form) {
		global $wpdb;

		$submission_saving = get_option('wpcf7_entries_submission_saving', 1);		
		if ( !$submission_saving ) {
			return;
		}

		$email = get_option( 'wpcf7_entries_field_email', 'your-email' );
		if (empty($email) ) {
			$email = 'your-email';
		}

		$name = get_option( 'wpcf7_entries_field_name', 'your-name' );
		if (empty($name) ) {
			$name = 'your-name';
		}

		$subject = get_option( 'wpcf7_entries_field_subject', 'your-subject' );
		if (empty($subject) ) {
			$subject = 'your-subject';
		}

		$result = $wpdb->insert($wpdb->prefix . 'wpcf7_entries', array( 
			'form_id' => $_POST['_wpcf7'], 
			'email' => $_POST[$email], 
			'name' => $_POST[$name], 
			'subject' => $_POST[$subject]
		));

		if ( !$result ) {
			return;
		}

		$others_fields = array_filter($_POST, function($value, $key){
			if ( in_array($key, [$email, $name, $subject]) ) {
				return false;
			}

			if ( strpos($key, '_wpcf7') !== false) {
				return false;
			}

			return true;

		}, ARRAY_FILTER_USE_BOTH);

		foreach ($others_fields as $key => $value) {
			$wpdb->insert($wpdb->prefix . 'wpcf7_entries_fields', array( 
				'entry_id' => $wpdb->insert_id,
				'field_id' => $key, 
				'value' => $value
			));
		}
	}
}
