<?php
/**
 * File containing the class WPCF7_Entries_Table.
 *
 * @package wpcf7-entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles WP_List_Table for Contact Form 7.
 *
 * @since 1.0.1
 */

class WPCF7_Entries_Table extends WP_List_Table {
    /**
     * set bulk action for table
     * @since 1.0.1
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', 'wpcf7-entries')
        ];

        return $actions;
    }

    /**
     * handle bulk action for table
     * @since 1.0.1
     */    
    public function process_bulk_action() {        
        if (!wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) {
            return;
        }

        global $wpdb;

        if ( $_REQUEST['action'] == 'bulk-delete' && !empty($_REQUEST['entries']) ) {
            $wpdb->query(sprintf("DELETE entries, entry_fields FROM {$wpdb->prefix}wpcf7_entries entries LEFT JOIN {$wpdb->prefix}wpcf7_entries_fields entry_fields ON entries.ID = entry_fields.entry_id WHERE entries.ID IN (%s)", implode(', ', $_REQUEST['entries'])));
            exit(wp_safe_redirect(admin_url('admin.php?page=wpcf7-entries&form=' . $_GET['form'])));
        }
    }

    /**
     * add export button at top
     * @since 1.0.1
     */
    function extra_tablenav( $which ) {
        if ( 'top' !== $which ) {
            return;
        } 
        
        ?>
        <div class="alignleft actions">
            <?php submit_button( __( 'Export' ), '', 'action', false); ?>
        </div>
        <?php
    }

    /**
     * Sortable columns
     * @since 1.0.1
     */
    public function get_sortable_columns() {
        return array(
            'email' => array('email', false),
            'name' => array('name', false),
            'submitted_date' => array('submitted_date', false),
        );
    } 

    /**
     * Prepare the items for the table to process
     * @since 1.0.1
     * @return Void
     */
    public function prepare_items() {
        $this->process_bulk_action();

        global $wpdb;
        
        $columns = $this->get_columns();

        $per_page = $this->get_items_per_page( 'entry_per_page', 15 );
        $offset = ($per_page * ($this->get_pagenum() - 1));

        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = %s", $_GET['form']);

        if ( !empty($_REQUEST['s']) ) {
            $sql .= 'AND name LIKE "%'. trim($_REQUEST['s']).'%"';
        }

        $orderby = "ID DESC";
        if ( !empty($_GET['orderby']) ) {
            $orderby = sprintf("%s %s", $_GET['orderby'], strtoupper($_GET['order']));
        }

        $sql .= sprintf(" ORDER BY %s LIMIT %d, %d", $orderby, $offset, $per_page);

        $entries = $wpdb->get_results($sql);

        $this->set_pagination_args( array(
            'total_items' => $wpdb->get_var(sprintf("SELECT count(*) FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = %s", $_GET['form'])),
            'per_page'    => $per_page
        ) );

        $sortable = $this->get_sortable_columns(); 
        $this->_column_headers = array($columns, [], $sortable);
        $this->items = $entries;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     * @since 1.0.1
     */
    public function get_columns() {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'email'     => __('Email', 'wpcf7-entries'),
            'name'      => __('Name', 'wpcf7-entries'),
            'subject'   => __('Subject', 'wpcf7-entries'),
            'submitted_date'      => __('Date', 'wpcf7-entries'),
        );

        return $columns;
    }

    /**
     * Define what data to show on each column of the table
     * @param  Object $entry        Data
     * @param  String $column_name - Current column name
     * @since 1.0.1
     */
    public function column_default( $entry, $column_name ) {
        switch( $column_name ) {
            case 'email':
                return sprintf('<strong><a href="%s">%s</a></strong>', admin_url('admin.php?page=wpcf7-entry&id='.$entry->ID), $entry->$column_name);

            case 'name':
            case 'subject':
                return $entry->$column_name;

            case 'submitted_date':
                return date(get_option( 'date_format'), strtotime($entry->$column_name));

            default:
                return print_r( $entry, true ) ;
        }
    }

    /**
     * checkbox column 
     * @since 1.0.1
     */
    function column_cb( $form ) {
        return sprintf('<input type="checkbox" name="entries[]" value="%s" />', $form->ID);
    }    
}