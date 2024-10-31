<?php
//Goodbye Mate

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

//Delete options
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'picosearch\_%'" );

//Delete tables
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}picosearch_index" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}picosearch_docs" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}picosearch_log" );


	