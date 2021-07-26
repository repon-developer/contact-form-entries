<?php
/**
 * File containing the class Contact_Form7_Entries_Admin_Settings.
 *
 * @package contact-form-7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin settings page for Contact Form 7 Entries.
 *
 * @since 1.0.1
 */
class Contact_Form7_Entries_Admin_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {	
		add_action( 'admin_init', [$this, 'register_settings']);		
	}

	public function register_settings() {
		add_settings_section('entries_settings_options', '', '', 'wpcf7_entries_settings_options');

		add_settings_field(
            'wpcf7_entries_submission_saving',
            __('Submission Saving', 'wpcf7-entries'),
            array( $this, 'submission_saving' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );

		register_setting('wpcf7_entires_settings', 'wpcf7_entries_submission_saving');

		add_settings_field(
            'wpcf7_entries_menu_name',
            __('Menu Name', 'wpcf7-entries'),
            array( $this, 'entries_menu_name' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );

		register_setting('wpcf7_entires_settings', 'wpcf7_entries_field_email');

		add_settings_field(
            'wpcf7_entries_field_email',
            __('Email Field', 'wpcf7-entries'),
            array( $this, 'wpcp7_entries_email_field' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );

		register_setting('wpcf7_entires_settings', 'wpcf7_entries_field_name');

		add_settings_field(
            'wpcf7_entries_field_name',
            __('Name Field', 'wpcf7-entries'),
            array( $this, 'wpcp7_entries_name_field' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );

		register_setting('wpcf7_entires_settings', 'wpcf7_entries_field_subject');

		add_settings_field(
            'wpcf7_entries_field_subject',
            __('Subject Field', 'wpcf7-entries'),
            array( $this, 'wpcp7_entries_subject_field' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );

		register_setting('wpcf7_entires_settings', 'wpcf7_entries_roles');

		add_settings_field(
            'wpcf7_entries_roles',
            __('View Entries Roles', 'wpcf7-entries'),
            array( $this, 'wpcp7_entries_roles' ),
            'wpcf7_entries_settings_options',
            'entries_settings_options'
        );
	}

	function submission_saving() {
		$submission_saving = get_option('wpcf7_entries_submission_saving', 1);
		printf('<label><input type="checkbox" name="wpcf7_entries_submission_saving" %s value="1"> %s</label>', checked(1, $submission_saving, false), __('Enable', 'wpcf7-entries'));
	}

	function entries_menu_name() {
		printf('<input type="text" name="wpcf7_entries_menu_name" value="%s" />', get_option( 'wpcf7_entries_menu_name', __('Contacts', 'wpcf7-entries') ) );
	}

	function wpcp7_entries_email_field() {
		printf('<input type="text" name="wpcf7_entries_field_email" value="%s" />', get_option( 'wpcf7_entries_field_email', 'your-email' ) );
	}

	function wpcp7_entries_name_field() {
		printf('<input type="text" name="wpcf7_entries_field_name" value="%s" />', get_option( 'wpcf7_entries_field_name', 'your-name' ) );
	}

	function wpcp7_entries_subject_field() {
		printf('<input type="text" name="wpcf7_entries_field_subject" value="%s" />', get_option( 'wpcf7_entries_field_subject', 'your-subject' ) );
	}

	function wpcp7_entries_roles() {
		global $wp_roles;

		$roles = array_filter((array) get_option( 'wpcf7_entries_roles'));

		echo '<ul>';
		foreach ($wp_roles->role_names as $role => $role_name) {
			if ( $role === 'administrator') continue;
			$checked = checked( true, in_array($role, $roles), false);
			printf('<li><label><input name="wpcf7_entries_roles[]" type="checkbox" value="%s" %s /> %s</label></li>', $role, $checked, $role_name);
		}

		echo '</ul>';
	}

    public function output() {
        ?>
        <div class="wrap wpcf7-entris-settings-wrap">
			<h1 class="wp-heading-inline"><?php _e('Settings', 'wpcf7-entries') ?></h1>

			<div class="wpcf7-entries-row">
				<form class="wpcf7-entries-options" method="post" action="options.php">
					<?php settings_fields( 'wpcf7_entires_settings' ); ?>

					<h2 class="nav-tab-wrapper">
						<a href="#settings-options" class="nav-tab"><?php _e('Settings', 'wpcf7-entries') ?></a>
						<a href="#settings-about" class="nav-tab"><?php _e('About', 'wpcf7-entries') ?></a>
					</h2>

					<?php
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Used for basic flow.
					if ( ! empty( $_GET['settings-updated'] ) ) {
						echo '<div class="updated fade job-manager-updated"><p>' . esc_html__( 'Settings successfully saved', 'wpcf7-entries' ) . '</p></div>';
					}

					
					echo '<div id="settings-options" class="settings_panel">';
						echo '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s</p>';

						do_settings_sections( 'wpcf7_entries_settings_options' );

						echo '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s</p>';				
					echo '</div>';

					echo '<div id="settings-about" class="settings_panel">';

						echo '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s</p>';
					
					echo '</div>';
					?>

					<?php submit_button() ?>
				</form>

				<div class="sidebar">
					<div class="box-widget">
						<h3 class="widget-title"><?php _e('Contacts', 'wpcf7-entries'); ?></h3>
						<p><strong><?php _e('Help & Resource', 'wpcf7-entries'); ?></strong></p>
						<ol>
							<li><a href="#" target="_blank"><?php _e('FAQs', 'wpcf7-entries'); ?></a></li>
							<li><a href="#" target="_blank"><?php _e('Support forums', 'wpcf7-entries'); ?></a></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('.nav-tab-wrapper a').click(function() {
				if ( '#' !== jQuery(this).attr( 'href' ).substr( 0, 1 ) ) {
					return false;
				}
				jQuery('.settings_panel').hide();
				jQuery('.nav-tab-active').removeClass('nav-tab-active');
				jQuery( jQuery(this).attr('href') ).show();
				jQuery(this).addClass('nav-tab-active');
				window.location.hash = jQuery(this).attr('href');
				jQuery( 'form.wpcf7-entries-options' ).attr( 'action', 'options.php' + jQuery(this).attr( 'href' ) );
				window.scrollTo( 0, 0 );
				return false;
			});

			var goto_hash = window.location.hash;
			if ( '#' === goto_hash.substr( 0, 1 ) ) {
				jQuery( 'form.wpcf7-entries-options' ).attr( 'action', 'options.php' + jQuery(this).attr( 'href' ) );
			}
			if ( goto_hash ) {
				var the_tab = jQuery( 'a[href="' + goto_hash + '"]' );
				if ( the_tab.length > 0 ) {
					the_tab.click();
				} else {
					jQuery( '.nav-tab-wrapper a:first' ).click();
				}
			} else {
				jQuery( '.nav-tab-wrapper a:first' ).click();
			}
			
		</script>
        <?php
    }

}