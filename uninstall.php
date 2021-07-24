<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wpcf7_entries_delete_plugin() {
	global $wpdb;

	delete_option( 'wpcf7_entries' );
	
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'contact_form_7_entries' ) );
}

wpcf7_entries_delete_plugin();
