<?php
/**
 * Plugin Name: Event Rocket
 * Description: Adds shortcodes and other tools to help build sites with The Events Calendar/Events Calendar PRO.
 * Version: 1.3
 * Author: Barry Hughes
 * Author URI: http://codingkills.me
 * License: GPLv3 or later
 */

defined( 'ABSPATH' ) or exit();


add_action( 'plugins_loaded', 'eventrocket_launch' );

function eventrocket_launch() {
	if ( ! class_exists( 'TribeEvents' ) || version_compare( TribeEvents::VERSION, '3.4', '<' ) ) {
		eventrocket_abort_launch();
		return;
	}

	define( 'EVENT_ROCKET_INC', dirname( __FILE__ ) . '/inc' );
	require EVENT_ROCKET_INC . '/orbit.php';
}

function eventrocket_abort_launch() {
	add_action( 'admin_notices', 'eventrocket_explain_failure' );
}

function eventrocket_explain_failure() {
	$msg =  __( 'A suitable version of The Events Calendar does not seem to be installed. Event Rocket cannot launch.', 'event-rocket' );
	echo '<div class="error"> <p> ' . $msg . ' </p> </div>';
}