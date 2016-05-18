<?php

/**
 * Plugin Name: FCC Debug
 * Plugin URI:  http://www.forumcomm.com/
 * Author:      Forum Communications Company (Ryan Veitch)
 * Author URI:  http://www.forumcomm.com/
 * Version:     0.0.1
 * Description: Custom debugging functions, helpers and wrappers.
 * License:     GPL v2 or later
 * Text Domain: fcc-debug
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/*--------------------------------------------------------------
# DEBUG HELPER FUNCTIONS
--------------------------------------------------------------*/

/**
 * Pretty prints a print_r/object dump
 */
if( ! function_exists('print_my_dump') ) {
	function print_my_dump( $response ) {
		echo '<pre>';
		print_r($response);
		echo '</pre>';
	}
}

if( ! function_exists('pmd') ) {
	function pmd( $response ) {
		echo '<pre>';
		print_r($response);
		echo '</pre>';
	}
}

/**
 * Pretty prints a json dump
 */
if( ! function_exists('json_my_dump') ) {
	function json_my_dump( $response ) {
		echo '<pre>';
		print_r( json_encode($response,JSON_PRETTY_PRINT) );
		echo '</pre>';
	}
}

if( ! function_exists('jmd') ) {
	function jmd( $response ) {
		echo '<pre>';
		print_r( json_encode($response,JSON_PRETTY_PRINT) );
		echo '</pre>';
	}
}

/**
 * Flush all the things
 */
if( ! function_exists('flush_it_all') ) {
	function flush_it_all() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		flush_rewrite_rules();
		wp_cache_delete ( 'alloptions', 'options' );
		wp_cache_flush();
		echo 'Flushed all the things!';
	}
}

/**
 * Admin WP_Screen Object tester (wp-php-console)
 */
 if( ! function_exists('fcc_admin_screen_test') ) {
	 function fcc_admin_screen_test() {
		 global $my_admin_page;
		 $screen = get_current_screen();
		 PC::debug( $screen, 'Screen:' );
	}
} #Detect plugin. For use in Admin area only.
if ( is_plugin_active( 'wp-php-console/wp-php-console.php' ) ) {
  add_action( 'admin_enqueue_scripts', 'fcc_admin_screen_test' );
}
