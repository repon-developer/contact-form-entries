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
        require_once WPCF7_ENTRIES_PLUGIN_DIR. '/includes/class-wpcf7-entries-table.php';
		$this->entry_table = new WPCF7_Entries_Table();

		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 20, 3 );
        $this->export_entries();
	}

    /**
	 * set screen option $value.
     * @since  1.0.1
	 */
	public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    /**
	 * add options for screen setting.
     * @since  1.0.1
	 */
	public function screen_option() {
        add_screen_option( 'per_page', [
            'label' => __('Form Per Page', 'wpcf7-entries'),
            'default' => 15,
            'option' => 'entry_per_page'
        ] );
    }

    /**
	 * export all emails as a csv file
     * @since  1.0.1
	 */
    public function export_entries() {
        if (!wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) {
            return;
        }

        if ( $_REQUEST['action'] != 'Export' ) {
            return;
        }

        global $wpdb;

        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = " . $_GET['form']);
        if ( !$result ) {
            return;
        }
                
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="entries.csv"');

        $output = fopen('php://output', 'wb');
        fputcsv($output, array('email'));

        while ($row = current($result)) {
            next($result);
            fputcsv($output, array($row->email) );
        }

        fclose($output);
        exit;
    }

    /**
	 * admin page for form entries
     * @since  1.0.1
	 */
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

		
		$this->entry_table->prepare_items(); ?>

        <div class="wrap">
			<h1 class="wp-heading-inline"><strong><?php echo $form->post_title ?></strong> <?php _e('Entries', 'wpcf7-entries'); ?></h1>
            <form method="post">
                <?php $this->entry_table->search_box( __( 'Search Contacts', 'wpcf7-entries' ), 'search-box-id' ); ?>
                <?php $this->entry_table->display(); ?>
            </form>
        </div>
        <?php
    }

}