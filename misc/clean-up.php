<?php
defined( 'ABSPATH' ) or exit();


/**
 * The jettison module provides tools to remove all events data from a WordPress
 * installation.
 */
class EventRocket_CleanUpTool
{
	const EVENTS = 'tribe_events';
	const VENUES = 'tribe_venue';
	const ORGANIZERS = 'tribe_organizer';

	protected $min_capability = 'delete_plugins';
	protected $timeout = 8;
	protected $clock = 0;
	protected $batch = 40;
	protected $in_progress = false;

	protected $cap_count = 0;


	public function __construct() {
		$this->min_capability = apply_filters( 'eventrocket_cleanup_data_cap', $this->min_capability );
		$this->timeout = apply_filters( 'eventrocket_cleanup_timeout', $this->timeout );
		$this->batch = apply_filters( 'eventrocket_cleanup_batch_size', $this->batch );
		$this->actions();
	}

	protected function actions() {
		add_action( 'admin_menu', array( $this, 'register_screen' ) );
		add_action( 'admin_init', array( $this, 'cleanup' ) );
	}

	public function register_screen() {
		$title = __( 'Event Data Cleanup', 'eventrocket' );
		$capability = $this->min_capability;
		$callback = array( $this, 'screen' );
		add_management_page( $title, $title, $capability, 'eventrocket_jettison', $callback );
	}

	public function screen() {
		wp_enqueue_script( 'eventrocket_cleanup', EVENTROCKET_URL . 'assets/cleanup.js', 'jquery', false, true );
		$current_data = $this->existing_data();
		$action_url = $this->action_url();
		$in_progress = $this->in_progress;
		include EVENTROCKET_INC . '/templates/cleanup-screen.php';
	}

	protected function existing_data() {
		return array(
			'events' => $this->count_up( self::EVENTS ),
			'venues' => $this->count_up( self::VENUES ),
			'organizers' => $this->count_up( self::ORGANIZERS ),
			'options' => $this->settings_count(),
			'capabilities' => $this->caps_count()
		);
	}

	protected function count_up( $post_type ) {
		global $wpdb;
		$query = "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = '%s' LIMIT 1;";
		return absint( $wpdb->get_var( $wpdb->prepare( $query, $post_type	) ) );
	}

	protected function settings_count() {
		global $wpdb;
		$query = "SELECT COUNT(*) FROM $wpdb->options WHERE `option_name` LIKE '%tribe%events%';";
		return absint( $wpdb->get_var( $query ) );
	}

	protected function caps_count() {
		$this->cap_count = 0;
		$this->foreach_cap( array( $this, 'count_each_cap' ) );
		return $this->cap_count;
	}

	protected function action_url() {
		$token = wp_create_nonce( 'cleanup' . get_current_user_id() );
		$current_page = admin_url( $GLOBALS['pagenow'] . '?page=eventrocket_jettison' );
		$url = add_query_arg( 'eventrocket_cleanup', $token, $current_page );
		return esc_url( $url );
	}

	public function cleanup() {
		if ( ! $this->sanity_checks() ) return;
		while ( $this->time_left() && $this->job_incomplete() ) $this->keep_cleaning();
	}

	protected function sanity_checks() {
		if ( ! is_admin() || ! current_user_can( $this->min_capability ) ) return false;
		if ( ! isset( $_GET['eventrocket_cleanup'] ) ) return false;
		if ( ! wp_verify_nonce( $_GET['eventrocket_cleanup'], 'cleanup' . get_current_user_id() ) ) return false;
		return true;
	}

	protected function job_incomplete() {
		$counts = $this->existing_data();
		return max( $counts ) > 0;
	}

	protected function time_left() {
		if ( 0 === $this->clock ) $this->clock = time();
		return ( time() - $this->clock < $this->timeout );
	}

	protected function keep_cleaning() {
		$this->in_progress = true;
		$counts = $this->existing_data();

		if ( $counts['events'] > 0 ) $this->clean( self::EVENTS );
		elseif ( $counts['venues'] > 0 ) $this->clean( self::VENUES );
		elseif ( $counts['organizers'] > 0 ) $this->clean( self::ORGANIZERS );
		elseif ( $counts['capabilities'] > 0 ) $this->clean_caps();
		elseif ( $counts['options'] > 0 ) $this->clean_options();
	}

	protected function clean( $post_type ) {
		global $wpdb;
		$query = "SELECT ID FROM $wpdb->posts WHERE `post_type` = '%s' LIMIT %d;";
		$post_ids = $wpdb->get_col( $wpdb->prepare( $query, $post_type, $this->batch ) );
		if ( !is_array( $post_ids ) || empty( $post_ids) ) return;
		foreach ( $post_ids as $id ) wp_delete_post( $id, true );
	}

	protected function clean_caps() {
		$this->foreach_cap( array( $this, 'clean_cap' ) );
	}

	protected function foreach_cap( $callback ) {
		global $wp_roles;

		foreach ( $wp_roles->roles as $role_slug => $user_role )
			foreach ( $user_role['capabilities'] as $capability => $on )
				if ($this->looks_tribal($capability)) call_user_func($callback, $role_slug, $capability);
	}

	protected function looks_tribal( $string ) {
		$string = strtolower( $string );
		if ( false === strpos( $string, 'tribe' ) ) return false;
		if ( false !== strpos( $string, 'event' ) ) return true;
		if ( false !== strpos( $string, 'venue' ) ) return true;
		if ( false !== strpos( $string, 'organizer' ) ) return true;
		return false;
	}

	protected function count_each_cap() {
		$this->cap_count++;
	}

	protected function clean_cap( $role, $capability ) {
		$role = get_role( $role );
		if ( null === $role ) return;
		$role->remove_cap( $capability );
	}

	protected function clean_options() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '%tribe%events%';" );
	}
}

new EventRocket_CleanUpTool;