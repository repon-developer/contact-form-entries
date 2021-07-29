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
	 * Default value for per page.
	 * @since  1.0.1
	 */
    var $per_page = 15;

    /**
	 * store columns.
	 * @since  1.0.1
	 */
    var $columns = [];

    /**
	 * Constructor.
	 * @since  1.0.1
	 */
	public function __construct() {
        $this->per_page = $this->get_items_per_page( 'entry_per_page', 15 );
		parent::__construct();
	}

    private function query() {
        global $wpdb;
        
        $offset = ($this->per_page * ($this->get_pagenum() - 1));

        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = %s", $_GET['form']);

        if ( !empty($_REQUEST['s']) ) {
            $sql .= 'AND name LIKE "%'. trim($_REQUEST['s']).'%"';
        }

        $orderby = "ID DESC";
        if ( !empty($_GET['orderby']) ) {
            $orderby = sprintf("%s %s", $_GET['orderby'], strtoupper($_GET['order']));
        }

        $sql .= sprintf(" ORDER BY %s LIMIT %d, %d", $orderby, $offset, $this->per_page);

        $results = $wpdb->get_results($sql);

        array_walk($results, function(&$item) use($wpdb) {
            $fields = $wpdb->get_results(sprintf("SELECT * FROM {$wpdb->prefix}wpcf7_entries_fields WHERE entry_id = %s", $item->ID));
            while ( $field = current($fields) ) {
                next($fields);
                $item->{$field->field_id} = $field->value;
            }
        });

        $this->items = $results;
    }

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
     * add export button at top
     * @since 1.0.1
     */
    function extra_tablenav( $which ) {
        if ( 'top' !== $which ) {
            return;
        } 
        
        ?>
        <div class="alignleft actions">
            <?php submit_button( __( 'Export', 'wpcf7-entries' ), '', 'action', false); ?>
        </div>
        <?php
    }

    /**
     * Sortable columns
     * @since 1.0.1
     */
    public function get_sortable_columns() {
        return array(
            'id' => array('id', false),
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
        global $wpdb;
        $this->process_bulk_action();
        $this->query();

        $sortable = $this->get_sortable_columns(); 
        $this->_column_headers = array($this->get_columns(), [], $sortable);
        
        $this->set_pagination_args( array(
            'total_items' => $wpdb->get_var(sprintf("SELECT count(*) FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = %s", $_GET['form'])),
            'per_page'    => $this->per_page
        ) );
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     * @since 1.0.1
     */
    public function get_columns() {
        $columns = [];
        foreach ($this->items as $entry) {
            $columns = array_merge($columns, array_keys((array)$entry));            
        }

        $columns = array_unique($columns);
        foreach (['ID', 'form_id', 'submitted_date', 'spam'] as $key) {
            unset($columns[array_search($key, $columns)]);
        }
     
        foreach ($columns as $key) {
            $values = array_filter(wp_list_pluck($this->items, $key));
            if ( empty($values) ) {
                unset($columns[array_search($key, $columns)]);
            }
        }

        if ( sizeof($columns) > 3) {
            $columns = array_slice($columns, 0, 3);
        }

        if ( sizeof($columns) == 0) {
            array_unshift($columns, 'id');
        }

        array_unshift($columns, 'cb');
        array_push($columns, 'submitted_date');

        $headers = [];
        foreach ($columns as $key) {
            $headers[$key] = $key;
        }

        if ( isset($headers['cb']) ) {
            $headers['cb'] = '<input type="checkbox" />';
        }

        if ( isset($headers['id']) ) {
            $headers['id'] = __('ID', 'wpcf7-entries');
        }

        if ( isset($headers['email']) ) {
            $headers['email'] = __('Email', 'wpcf7-entries');
        }

        if ( isset($headers['name']) ) {
            $headers['name'] = __('Name', 'wpcf7-entries');
        }

        if ( isset($headers['subject']) ) {
            $headers['subject'] = __('Subject', 'wpcf7-entries');
        }

        if ( isset($headers['submitted_date']) ) {
            $headers['submitted_date'] = __('Date', 'wpcf7-entries');
        }

        $this->columns = $headers;
        return $headers;
    }

    /**
     * Define what data to show on each column of the table
     * @param  Object $entry        Data
     * @param  String $column_name - Current column name
     * @since 1.0.1
     */
    public function column_default( $entry, $column_name ) {
        $permalink = admin_url('admin.php?page=wpcf7-entry&id='.$entry->ID);

        if ( 'id' == $column_name ) {
            return sprintf('<strong><a href="%s">%s</a></strong>', $permalink, $entry->ID);
        }

        if ( 'email' == $column_name ) {
            return sprintf('<strong><a href="%s">%s</a></strong>', $permalink, $entry->$column_name);
        }

        if ( in_array($column_name, ['name', 'subject'] ) ) {
            return sprintf('<a href="%s">%s</a>', $permalink, $entry->$column_name);
        }

        if ( 'submitted_date' == $column_name ) {
            return date(get_option( 'date_format'), strtotime($entry->$column_name));
        }

        $value = $entry->$column_name;

        if ( str_word_count($value) > 20) {
            return wp_trim_words( $value, 20);
        }

        if ( is_array($entry->$column_name) || is_object($entry->$column_name) ) {
            return print_r($entry->$column_name, true);
        }

        return sprintf('<a href="%s">%s</a>', $permalink, $entry->$column_name);
    }

    /**
     * checkbox column 
     * @since 1.0.1
     */
    function column_cb( $form ) {
        return sprintf('<input type="checkbox" name="entries[]" value="%s" />', $form->ID);
    }    
}