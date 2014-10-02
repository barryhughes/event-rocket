<?php
defined( 'ABSPATH' ) or exit();


/**
 * Avoid 404s on empty single day views.
 */
class EventRocket_404Helper
{
	public function __construct() {
		add_action( 'status_header', array( $this, 'http_status_radar' ) );
		add_action( 'activate_plugin', array( $this, 'plugin_listener' ) );
		add_action( 'wp_loaded', array( $this, 'proactive_cleanup' ) );
	}

	public function http_status_radar( $status ) {
		if ( ! $this->is_event_404( $status ) ) return $status;
		if ( ! $this->is_day() ) return $status;
		return str_replace( '404 Not Found', '200 OK', $status );
	}

	protected function is_event_404( $status ) {
		if ( false === strpos( $status, '404 Not Found' ) ) return false;
		if ( ! tribe_is_event_query() ) return false;
		return true;
	}

	protected function is_day() {
		global $wp_query;
		return isset( $wp_query->tribe_is_day ) && $wp_query->tribe_is_day;
	}

	/**
	 * Plugins may add their own rewrite rules and displace existing ones, causing problems.
	 * We listen for this, pause to count, and then flush the rewrite rules.
	 */
	public function plugin_listener() {
		set_transient( 'eventrocket_plugin_alert', 1, ( 60 * 30 ) );
	}

	public function proactive_cleanup() {
		$count = (int) get_transient( 'eventrocket_plugin_alert' );
		if ( 1 === $count ) set_transient( 'eventrocket_plugin_alert', 2, ( 60 * 30 ) );
		if ( 2 !== $count ) return; // We wait until a count of 2 so as not to cleanup too early
		delete_transient( 'eventrocket_plugin_alert' );
		flush_rewrite_rules();
	}
}

// Start blasting those 404s
new EventRocket_404Helper;