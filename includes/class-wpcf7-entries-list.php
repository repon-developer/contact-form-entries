<?php
/**
 * File containing the class WPCF7_Entries_List.
 *
 * @package wpcf7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Contact Form 7 Entries.
 *
 * @since 1.0.1
 */
class WPCF7_Entries_List  {

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
            'label' => __('Form Per Page', 'wpcf7-entries'),
            'default' => 15,
            'option' => 'entry_per_page'
        ] );
    }

    public function output() {
        $form = get_post($_GET['form']);

        if ( !is_a($form, 'WP_Post') || $form->post_type !== 'wpcf7_contact_form') {
            ?>            
            <div class="wrap">
                <h1 class="wp-heading-inline"><strong><?php _e('No Form Exists', 'wpcf7-entries'); ?></strong></h1>
            </div>
            <?php
            return;
        }

		require_once WPCF7_ENTRIES_PLUGIN_DIR. '/includes/class-wpcf7-entries-table.php';
		$entry_table = new WPCF7_Entries_Table();
		$entry_table->prepare_items(); ?>

        <div class="wrap">
			<h1 class="wp-heading-inline"><strong><?php echo $form->post_title ?></strong> <?php _e('Entries', 'wpcf7-entries'); ?></h1>
            <form method="post">
                <?php //wp_nonce_field('_wpcf7_entries_action', 'wpcf7_entries_action'); ?>
                <?php $entry_table->display(); ?>
            </form>
        </div>
        <?php
    }

}