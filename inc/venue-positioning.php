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
		$this->coords_metabox();
	}

	protected function coords_metabox() {
		if ( ! apply_filters( 'eventrocket_venue_positioning_metabox', true ) ) return;
		add_action( 'add_meta_boxes', array( $this, 'setup_metabox' ) );
		add_action( 'init', array( $this, 'set_long_lat_keys' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Use the long/lat post meta keys defined by PRO if available.
	 */
	public function set_long_lat_keys() {
		$this->lat_key = '_VenueLat';
		$this->lng_key = '_VenueLng';

		if ( class_exists( 'TribeEventsGeoLoc') ) {
			$this->lat_key = TribeEventsGeoLoc::LAT;
			$this->lng_key = TribeEventsGeoLoc::LNG;
		}
	}

	/**
	 * Register the meta box.
	 */
	public function setup_metabox() {
		$title = __( 'Coordinates', 'eventrocket');
		$callback = array( $this, 'metabox' );
		add_meta_box( 'eventrocket_venue_coords', $title, $callback, TribeEvents::VENUE_POST_TYPE, 'side' );
	}

	/**
	 * Display the meta box.
	 */
	public function metabox( $post ) {
		$template = apply_filters( 'eventrocket_metabox_template', EVENTROCKET_INC . '/venue-positioning/metabox.php' );
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
}

// Project GPS adds editing of long/lat coords for venues through the editor screen
new EventRocket_VenuePositioning;