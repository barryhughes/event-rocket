<?php
/**
 * Plugin Name: Event Rocket
 * Description: Adds shortcodes and other tools to help build sites with The Events Calendar/Events Calendar PRO.
 * Version: 2.5dev
 * Author: Barry Hughes
 * Author URI: http://codingkills.me
 * License: GPLv3 or later
 */

defined( 'ABSPATH' ) or exit();


add_action( 'plugins_loaded', 'eventrocket_launch' );

function eventrocket_launch() {
	define( 'EVENTROCKET_INC', dirname( __FILE__ ) );
	define( 'EVENTROCKET_URL', plugin_dir_url( __FILE__ ) );

	if ( ! class_exists( 'TribeEvents' ) || version_compare( TribeEvents::VERSION, '3.8', '<' ) ) {
		eventrocket_abort_launch();
		return;
	}

	require_once EVENTROCKET_INC . '/embedding/embedding.php';
	require_once EVENTROCKET_INC . '/admin/admin.php';
	require_once EVENTROCKET_INC . '/misc/helpers.php';
}

function eventrocket_abort_launch() {
	global $pagenow;

	require_once EVENTROCKET_INC . '/misc/clean-up.php';
	if ( 'plugins.php' === $pagenow ) add_action( 'admin_notices', 'eventrocket_explain_failure' );
}

function eventrocket_explain_failure() {
	$msg =  __( 'Event Rocket requires a suitable version of The Events Calendar to be activated in order to provide '
		. 'full functionality (data cleanup tools will still be available, though).', 'eventrocket' );
	echo '<div class="error"> <p> ' . $msg . ' </p> </div>';
}