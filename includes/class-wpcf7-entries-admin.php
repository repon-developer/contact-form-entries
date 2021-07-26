<?php
/**
 * File containing the class Contact_Form7_Entries_Admin.
 *
 * @package wpcf7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin page for Contact Form 7 Entries.
 *
 * @since 1.0.1
 */
class WPCF7_Entries_Admin {

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
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-forms.php';
		$this->form_list = new WPCF7_Entries_Forms();
	}

	/**
	 * Enqueues CSS and JS assets.
	 */
	public function admin_enqueue_scripts() {
		$plugin_screens = ['toplevel_page_wpcf7-entries-settings'];
		if ( !in_array(get_current_screen()->id, $plugin_screens)) {
			return;
		}

		wp_enqueue_style( 'wpcf7-entries', WPCF7_ENTRIES_PLUGIN_URL . '/assets/wpcf7-entries.css', [], '1.0.1');
	}

	/**
	 * Adds pages to admin menu.
	 */
	public function admin_menu() {
		$form_list_hook = add_menu_page(
            __( 'Contacts Form 7 Forms', 'wpcf7-entries' ),
            __( 'Contacts', 'wpcf7-entries' ),
            'manage_options',
            'wpcf7-entries-forms',
            array($this->form_list, 'output'),
           'dashicons-feedback',
		   31
        );
		
        add_action( "load-$form_list_hook", [$this->form_list, 'screen_option' ] );	
        add_submenu_page( 'wpcf7-entries-forms', __('Contact Form 7 Entries Forms', 'wpcf7-entries'), __('Form Submission', 'wpcf7-entries'), 'manage_options', 'wpcf7-entries-forms');


		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-settings.php';
		$settings_page = new WPCF7_Entries_Settings();
        add_submenu_page( 'wpcf7-entries-forms', __('Contact Form 7 Entries Settings', 'wpcf7-entries'), __('Settings', 'wpcf7-entries'), 'manage_options', 'wpcf7-entries-settings', [$settings_page, 'output'], 'dashicons-feedback', 31);
	}

}

WPCF7_Entries_Admin::instance();
