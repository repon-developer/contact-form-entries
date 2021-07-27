<?php
/**
 * File containing the class WPCF7_Entries_Admin.
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
		//add our plugin menu for dashboard
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		//implement our stylesheet and script
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		//Contact form 7 form list
		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-forms.php';
		$this->form_list = new WPCF7_Entries_Forms();
	}

	/**
	 * Enqueues CSS and JS assets. We just implement stylesheet and script for contact form 7 entries plugin
	 */
	public function admin_enqueue_scripts() {
		//our plugin screens
		$plugin_screens = ['contacts_page_wpcf7-entries-settings', 'admin_page_wpcf7-entry'];

		//if current screen is not form our plugin then return
		if ( !in_array(get_current_screen()->id, $plugin_screens)) {
			return;
		}

		//implement stylesheet for our plugin screen
		wp_enqueue_style( 'wpcf7-entries', WPCF7_ENTRIES_PLUGIN_URL . '/assets/wpcf7-entries.css', [], '1.0.1');
	}

	/**
	 * Adds pages to admin menu.
	 */
	public function admin_menu() {
		$menu_name = get_option( 'wpcf7_entries_menu_name');		
		if ( empty($menu_name) ) {
			$menu_name = __( 'Contacts', 'wpcf7-entries' );
		}

		//Add top level menu page of contact form 7 entries
		$form_list_hook = add_menu_page(
            __( 'Contacts Form 7 Forms', 'wpcf7-entries' ),
            $menu_name,
            'manage_wpcf7_entries',
            'wpcf7-entries-forms',
            array($this->form_list, 'output'),
           'dashicons-feedback',
		   31
        );

		//Enable screen option for page settings		
        add_action( "load-$form_list_hook", [$this->form_list, 'screen_option' ] );	

		//Add submenu page for just change the menu name from contacts for form submission
        add_submenu_page( 'wpcf7-entries-forms', __('Contact Form 7 Entries Forms', 'wpcf7-entries'), __('Form Submission', 'wpcf7-entries'), 'manage_wpcf7_entries', 'wpcf7-entries-forms');

		//settings submenu page of contact form 7 entries
		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-settings.php';
		$settings_page = new WPCF7_Entries_Settings();
        add_submenu_page( 'wpcf7-entries-forms', __('Contact Form 7 Entries Settings', 'wpcf7-entries'), __('Settings', 'wpcf7-entries'), 'manage_options', 'wpcf7-entries-settings', [$settings_page, 'output']);


		//add dependency for entry list of contact form 7 & initiate of contact form entry list
		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-list.php';
		$entry_list = new WPCF7_Entries_List();

		//Entry List page for a contact form. Hidden page, just use for showing entry list
		$entry_list_hook = add_submenu_page( null, __('Contact Form 7 Entries', 'wpcf7-entries'), __('Entries', 'wpcf7-entries'), 'manage_wpcf7_entries', 'wpcf7-entries', [$entry_list, 'output']);

		//hook action for screen opton of entry list page
		add_action( "load-$entry_list_hook", [$entry_list, 'screen_option' ] );	


		require_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/class-wpcf7-entries-entry.php';
		$view_entry = new WPCF7_Entries_Entry();

		//Add submenu page for view entry
		add_submenu_page( null, __('View Entry', 'wpcf7-entries'), __('View Entry', 'wpcf7-entries'), 'manage_wpcf7_entries', 'wpcf7-entry', [$view_entry, 'output']);
	}

}

WPCF7_Entries_Admin::instance();
