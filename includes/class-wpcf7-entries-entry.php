<?php
/**
 * File containing the class WPCF7_Entries_Entry.
 *
 * @package wpcf7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles view entry functionality and page for Contact Form 7 Entries.
 *
 * @since 1.0.1
 */
class WPCF7_Entries_Entry {
	/**
	 * Form id of current entry.
	 */
	var $form = false;

	/**
	 * Current entry id
	 */
	var $entry_id = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		
		$entry = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wpcf7_entries WHERE id = " . $_GET['id']);
		if ( !$entry ) {
			return;
		}
		
		$this->form = $entry->form_id;
		$this->entry_id = $_GET['id'];

		$this->name = $entry->name;
		$this->email = $entry->email;
		$this->subject = $entry->subject;
		$this->submitted_date = $entry->submitted_date;

		$this->delete();
	}

	/**
	 * Hanlde delete a entry of a form.
	 * @since 1.0.1
	 */
	public function delete() {		
		if ( !$this->form || !wp_verify_nonce($_GET['delete'], (string) $this->entry_id ) ) {
			return;
		}

		global $wpdb;

		$wpdb->delete($wpdb->prefix  . 'wpcf7_entries', 		array('ID' => $this->entry_id));
		$wpdb->delete($wpdb->prefix  . 'wpcf7_entries_fields', 	array('entry_id' => $this->entry_id));
		exit(wp_safe_redirect(admin_url('admin.php?page=wpcf7-entries&form=' . $this->form)));
	}

	/**
	 * Output admin page for view single entry. If entry not exists show not found page
	 * @since 1.0.1
	 */
	public function output() {
		global $wpdb;

		if ( !$this->entry_id ) {
			return include_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/view-entry-404.php';			
		}

		$name = get_option( 'wpcf7_entries_field_name', 'your-name' );
		if (empty($name) ) {
			$name = 'your-name';
		}

		$email = get_option( 'wpcf7_entries_field_email', 'your-email' );
		if (empty($email) ) {
			$email = 'your-email';
		}

		$subject = get_option( 'wpcf7_entries_field_subject', 'your-subject' );
		if (empty($subject) ) {
			$subject = 'your-subject';
		}

		$fields = array($name => $this->name, $email => $this->email, $subject => $this->subject);

		$form_id = $this->form;
		$entry_id = $this->entry_id;
		$submitted_date = $this->submitted_date;

		$entry_fields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcf7_entries_fields WHERE entry_id = " . $this->entry_id);

		foreach ($entry_fields as $field) {
			$fields[$field->field_id] = $field->value;
		}

		include_once WPCF7_ENTRIES_PLUGIN_DIR . '/includes/view-entry.php';
	}
}