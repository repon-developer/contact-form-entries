<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wpcf7_entries_delete_plugin() {
	global $wpdb;	
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'wpcf7_entries' ) );
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'wpcf7_entries_fields' ) );
}

//wpcf7_entries_delete_plugin();
