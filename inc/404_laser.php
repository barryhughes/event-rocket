<?php
/**
 * Avoid 404s on empty single day views.
 */
class EventRocket404Laser
{
	public function __construct() {
		if ( true === get_option( 'event_rocket_404_laser_on', true ) )
			add_action( 'status_header', array( $this, 'http_status_radar' ) );
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
}

// Start blasting those 404s
new EventRocket404Laser;