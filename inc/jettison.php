<?php
/**
 * The jettison module provides tools to remove all events data from a WordPress
 * installation.
 */
class EventRocketJettisonTool
{
	const EVENTS = 'tribe_events';
	const VENUES = 'tribe_venue';
	const ORGANIZERS = 'tribe_organizer';


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_screen' ) );
	}

	public function register_screen() {
		$title = __( 'Event Data Cleanup', 'eventrocket' );
		$capability = apply_filters( 'eventrocket_cleanup_data_cap', 'delete_plugins' );
		$callback = array( $this, 'screen' );
		add_management_page( $title, $title, $capability, 'eventrocket_jettison', $callback );
	}

	public function screen() {
		$current_data = $this->existing_data();
		include EVENTROCKET_INC . '/views/jettison.php';
	}

	protected function existing_data() {
		return array(
			'events' => $this->count_up( self::EVENTS ),
			'venues' => $this->count_up( self::VENUES ),
			'organizers' => $this->count_up( self::ORGANIZERS )
		);
	}

	protected function count_up( $post_type ) {
		global $wpdb;
		$query = "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = '%s' LIMIT 1;";
		return $wpdb->get_var( $wpdb->prepare( $query, $post_type	) );
	}
}

new EventRocketJettisonTool;