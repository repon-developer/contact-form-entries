<?php
/**
 * File containing the class WPCF7_Forms_Table.
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

class WPCF7_Forms_Table extends WP_List_Table {
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', 'wpcf7-entries')
        ];

        return $actions;
    }

    public function get_sortable_columns() {
        return array(
            'form' => array('form', false),
            'entries' => array('entries', false),
            'author' => array('author', false),
            'post_date' => array('post_date', false),
        );
    }   

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'form'         => __('Form', 'wpcf7-entries'),
            'entries'       => __('Entries', 'wpcf7-entries'),
            'author'        => __('Author', 'wpcf7-entries'),
            'post_date'    => __('Date', 'wpcf7-entries'),
        );

        return $columns;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $form, $column_name ) {
        switch( $column_name ) {
            case 'form':
                return sprintf('<strong><a href="%s">%s</a></strong>', admin_url('admin.php?page=wpcf7-entries&form='.$form->ID), $form->post_title);

            case 'entries':
            case 'author':
                return $form->$column_name;

            case 'post_date':
                return date(get_option( 'date_format'), strtotime($form->$column_name));

            default:
                return print_r( $form, true ) ;
        }
    }

    function column_cb( $form ) {
        return sprintf('<input type="checkbox" name="forms[]" value="%s" />', $form->id);
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $sortable = $this->get_sortable_columns(); 
        $columns = $this->get_columns();

        $per_page = $this->get_items_per_page( 'form_per_page', 15 );

        $query_args = [
            'offset' => ($per_page * ($this->get_pagenum() - 1)),
            'posts_per_page' => $per_page,
            'post_type' => 'wpcf7_contact_form',
        ];

        if ( !empty($_REQUEST['s']) ) {
            $query_args['s'] = $_REQUEST['s'];
        }

        if ( !empty($_GET['orderby']) && ($_GET['orderby'] == 'form' || $_GET['orderby'] == 'post_date') ) {
            $orderby = $_GET['orderby'];
            if ( $orderby == 'form') {
                $orderby = 'title';
            }
            $query_args['orderby'] = $orderby;
            $query_args['order'] = strtoupper($_GET['order']);
        }

        $contact_forms = new WP_Query($query_args);

        array_walk($contact_forms->posts, function(&$form){
            $form->author = get_the_author_meta('display_name', $form->post_author);

            global $wpdb;
            $form->entries = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpcf7_entries WHERE form_id = $form->ID");
        });

        if ( !empty($_GET['orderby'])) {
            if ($_GET['orderby'] == 'entries') {
                usort($contact_forms->posts, function($a, $b){
                    if ( $_GET['order'] === 'asc') {
                        return $a->entries - $b->entries;
                    }
                    return $b->entries - $a->entries;
                });
            }

            if ($_GET['orderby'] == 'author') {
                usort($contact_forms->posts, function($a, $b){

                    if ( $_GET['order'] === 'asc') {
                        return strcmp($a->author, $b->author);
                    }

                    return strcmp($b->author, $a->author);
                });
            }            
        }

        $this->set_pagination_args( array(
            'total_items' => $contact_forms->found_posts,
            'per_page'    => $per_page
        ) );

        $this->_column_headers = array($columns, [], $sortable);

        $this->items = $contact_forms->posts;
    }
}