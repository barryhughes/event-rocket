<?php
/**
 * Plugin Name: Event Rocket
 * Description: Adds shortcodes and other tools to help build sites with The Events Calendar/Events Calendar PRO.
 * Version: 3.3
 * Author: Barry Hughes
 * Author URI: http://codingkills.me
 * License: GPLv3 or later
 */

defined( 'ABSPATH' ) or exit();


add_action( 'plugins_loaded', 'eventrocket_launch' );

function eventrocket_launch() {
	define( 'EVENTROCKET_INC', dirname( __FILE__ ) );
	define( 'EVENTROCKET_URL', plugin_dir_url( __FILE__ ) );

	// @todo we'll bump the min required TEC ver and switch to the new classnames across the board
	if ( class_exists( 'Tribe__Events__Main' ) ) $version = Tribe__Events__Main::VERSION;

	if ( ! isset( $version ) || version_compare( $version, '3.10', '<' ) ) {
		eventrocket_abort_launch();
		return;
	}

	load_plugin_textdomain( 'event-rocket', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	require_once EVENTROCKET_INC . '/embedding/embedding.php';
	require_once EVENTROCKET_INC . '/data-requests/data-requests.php';
	require_once EVENTROCKET_INC . '/admin/admin.php';
	require_once EVENTROCKET_INC . '/misc/load.php';
	require_once EVENTROCKET_INC . '/rsvp/rsvp.php';
}

function eventrocket_abort_launch() {
	global $pagenow;

	require_once EVENTROCKET_INC . '/misc/clean-up.php';
	if ( 'plugins.php' === $pagenow ) add_action( 'admin_notices', 'eventrocket_explain_failure' );
}

function eventrocket_explain_failure() {
	$msg =  __( 'Event Rocket requires a suitable version of The Events Calendar to be activated in order to provide '
		. 'full functionality (data cleanup tools will still be available, though).', 'event-rocket' );
	echo '<div class="error"> <p> ' . $msg . ' </p> </div>';
}

function eventrocket() {
	static $register = null;
	if ( null === $register ) $register = new stdClass;
	return $register;
}