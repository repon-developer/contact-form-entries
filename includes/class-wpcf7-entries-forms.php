<?php
/**
 * File containing the class WPCF7_Entries_Forms.
 *
 * @package wpcf7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Handles Contact Form 7 Entries Forms.
 *
 * @since 1.0.1
 */
class WPCF7_Entries_Forms {

	/**
	 * Constructor.
	 */
	public function __construct() {	
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 20, 3 );
	}

	public static function set_screen( $status, $option, $value ) {
        return $value;
    }

	public function screen_option() {
        add_screen_option( 'per_page', [
            'label' => __('Entry Per Page', 'wpcf7-entries'),
            'default' => 15,
            'option' => 'form_per_page'
        ] );
    }

    public function output() {
		require_once WPCF7_ENTRIES_PLUGIN_DIR. '/includes/class-wpcf7-forms-table.php';
		$forms = new WPCF7_Forms_Table();
		$forms->prepare_items(); ?>

        <div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('Form Submission', 'wpcf7-entries'); ?></h1>

            <form method="post">
                <?php wp_nonce_field('_affiliate_one_overview', 'affiliate_one_overview'); ?>
                <?php $forms->display(); ?>
            </form>
        </div>
        <?php
    }

}