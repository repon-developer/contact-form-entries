<?php
/**
 * File containing the class Contact_Form7_Entries_Admin.
 *
 * @package contact-form-7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin page for Contact Form 7 Entries.
 *
 * @since 1.0.1
 */
class Contact_Form7_Entries_Admin {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0.1
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
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
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 12 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}



	/**
	 * Enqueues CSS and JS assets.
	 */
	public function admin_enqueue_scripts() {
		$plugin_screens = ['toplevel_page_wpcf7-entires-settings'];
		if ( !in_array(get_current_screen()->id, $plugin_screens)) {
			return;
		}

		wp_enqueue_style( 'wpcf7-entries', WPCF7_ENTRIES_PLUGIN_URL . '/assets/wpcf7-entries.css', [], '1.0.1');
	}

	/**
	 * Adds pages to admin menu.
	 */
	public function admin_menu() {
		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-contact-form7-entries-settings.php';
		$settings_page = new Contact_Form7_Entries_Admin_Settings();
        add_menu_page(__('Contacts', 'contact-form-7-entries'), __('Contacts', 'contact-form-7-entries'), 'manage_options', 'wpcf7-entires-settings', [$settings_page, 'output'], 'dashicons-feedback', 31);
	}

}

Contact_Form7_Entries_Admin::instance();
