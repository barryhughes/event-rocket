<?php
defined( 'ABSPATH' ) or exit();


/**
 * Adds support for direct manipulation of venue co-ordinates via the venue editor.
 */
class EventRocket_VenuePositioning
{
	/**
	 * Venue post meta key for latitude.
	 *
	 * @var string
	 */
	protected $lat_key;

	/**
	 * Venue post meta key for longitude.
	 *
	 * @var string
	 */
	protected $lng_key;


	/**
	 * Set up meta box and listeners for updates.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'coords_metabox' ), 5 );
	}

	public function coords_metabox() {
		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) return;
		if ( ! apply_filters( 'eventrocket_venue_positioning_metabox', true ) ) return;

		add_action( 'add_meta_boxes', array( $this, 'setup_metabox' ) );
		add_action( 'init', array( $this, 'set_long_lat_keys' ) );
		add_action( 'tribe_events_venue_created', array( $this, 'save' ), 4 );
		add_action( 'tribe_events_venue_updated', array( $this, 'save' ), 4 );
		add_action( 'tribe_events_map_embedded', array( $this, 'use_coords' ), 10, 2 );
	}

	/**
	 * Use the long/lat post meta keys defined by PRO if available.
	 */
	public function set_long_lat_keys() {
		$this->lat_key = '_VenueLat';
		$this->lng_key = '_VenueLng';
	}

	/**
	 * Register the meta box.
	 */
	public function setup_metabox() {
		$title = __( 'Coordinates', 'event-rocket');
		$callback = array( $this, 'metabox' );
		add_meta_box( 'eventrocket_venue_coords', $title, $callback, Tribe__Events__Main::VENUE_POST_TYPE, 'side' );
	}

	/**
	 * Display the meta box.
	 */
	public function metabox( $post ) {
		$template = apply_filters( 'eventrocket_metabox_template', EVENTROCKET_INC . '/templates/venue-positioning-metabox.php' );
		$latitude = (float) get_post_meta( $post->ID, $this->lat_key, true );
		$longitude = (float) get_post_meta( $post->ID, $this->lng_key, true );
		include $template;
	}

	/**
	 * Save our new long/lat data when submitted.
	 *
	 * @param $post_id
	 * @return mixed
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['eventrocket_latitude'] ) || ! isset( $_POST['eventrocket_longitude'] ) ) return;
		if ( ! $this->safety( $post_id ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		$lat = (float) $_POST['eventrocket_latitude'];
		$lng = (float) $_POST['eventrocket_longitude'];

		if ( $lat > 90 ) $lat = 90;
		if ( $lat < -90 ) $lat = -90;
		if ( $lng > 180 ) $lng = 180;
		if ( $lng < -180 ) $lng = -180;

		update_post_meta( $post_id, $this->lat_key, $lat );
		update_post_meta( $post_id, $this->lng_key, $lng );
	}

	/**
	 * Check our nonce was set and checks out.
	 *
	 * @param int $id
	 * @return bool
	 */
	protected function safety( $id ) {
		if ( ! isset( $_POST['eventrocket_venue_positioning'] ) ) return false;
		if ( ! wp_verify_nonce( $_POST['eventrocket_venue_positioning'], 'event_rocket_save_long_lat' ) ) return false;
		return current_user_can( 'edit_post', $id );
	}

	/**
	 * Update the location information associated with the venue map to use coordinates
	 * in place of a street address, if possible.
	 *
	 * @param $map_index
	 * @param $venue_id
	 */
	public function use_coords( $map_index, $venue_id ) {
		// Sanity checks: we need a venue to work with and the correct version of TEC
		if ( ! tribe_is_venue( $venue_id ) ) return;
		if ( ! class_exists( 'Tribe__Events__Embedded_Maps' ) ) return;

		// Try to load the coordinates - it's possible none will be set
		$lat = get_post_meta( $venue_id, $this->lat_key, true );
		$lng = get_post_meta( $venue_id, $this->lng_key, true );
		if ( ! $this->valid_coords( $lat, $lng ) ) return;

		// If we have valid coordinates let's put them to work
		$mapping = Tribe__Events__Embedded_Maps::instance();
		$venue_data = $mapping->get_map_data( $map_index );
		$venue_data['coords'] = array( $lat, $lng );
		$mapping->update_map_data( $map_index, $venue_data );
	}


	protected function valid_coords( $lat, $lng ) {
		if ( ! is_numeric( $lat ) || $lat < -90  || $lat > 90 )  return false;
		if ( ! is_numeric( $lng ) || $lng < -180 || $lng > 180 ) return false;
		if ( 0 == $lat || 0 == $lng ) return false;
		return true;
	}
}

// Project GPS adds editing of long/lat coords for venues through the editor screen
new EventRocket_VenuePositioning;