<?php
/**
 * Adds support for direct manipulation of venue co-ordinates via the venue editor.
 */
class EventRocketGPS
{
	/**
	 * Script handle for Event Rocket's own embedded map script.
	 */
	const MAP_HANDLER = 'eventrocket_embedded_map';

	/**
	 * Script handle for the Google Maps API script.
	 */
	const GMAP_API = 'eventrocket_google_maps';

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
	 * Give each set of long/lat coordinates an index, potentially allowing for
	 * multiple embedded maps per page.
	 *
	 * @var array
	 */
	protected $embedded_maps = array();

	/**
	 * Working longitude value (for map embeds).
	 *
	 * @var int
	 */
	protected $lng = 0;

	/**
	 * Working latitude value (for map embeds).
	 *
	 * @var int
	 */
	protected $lat = 0;

	/**
	 * Used to track if Event Rocket's own map script (and Google Maps) have been
	 * enqueued.
	 *
	 * @var bool
	 */
	protected $map_script_enqueued = false;



	/**
	 * Set up meta box and listeners for updates.
	 */
	public function __construct() {
		$this->coords_metabox();
		$this->embedded_maps();
	}

	protected function coords_metabox() {
		if ( ! apply_filters( 'eventrocket_gps_metabox', true ) ) return;
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
		add_meta_box( 'eventrocket_gps_coords', $title, $callback, TribeEvents::VENUE_POST_TYPE, 'side' );
	}

	/**
	 * Display the meta box.
	 */
	public function metabox( $post ) {
		$template = apply_filters( 'eventrocket_metabox_template', EVENTROCKET_INC . '/gps/metabox.php' );
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
		if ( ! isset( $_POST['eventrocket_gps'] ) ) return false;
		if ( ! wp_verify_nonce( $_POST['eventrocket_gps'], 'event_rocket_save_long_lat' ) ) return false;
		return current_user_can( 'edit_post', $id );
	}

	protected function embedded_maps() {
		if ( apply_filters( 'eventrocket_replace_embedded_maps', true ) )
			add_filter( 'tribe_get_embedded_map', array( $this, 'single_post_map' ) );
	}

	/**
	 * Replaces the embedded map on single post/venues with one that uses lat/long rather
	 * than the street address.
	 *
	 * @param $map
	 * @return mixed
	 */
	public function single_post_map( $map ) {
		$post_id = get_the_ID();

		// If it's neither a venue nor an event, bail
		if ( ! ( tribe_is_venue( $post_id ) || tribe_is_event( $post_id ) ) ) return $map;
		$venue = tribe_get_venue_id( $post_id );

		// Try to load the coordinates
		$this->lat = get_post_meta( $venue, $this->lat_key, true );
		$this->lng = get_post_meta( $venue, $this->lng_key, true );

		// No valid coordinates? Bail
		if ( ! is_numeric( $this->lat ) || $this->lat < -90 || $this->lat > 90 ) return $map;
		if ( ! is_numeric( $this->lng ) || $this->lng < -180 || $this->lng > 180 ) return $map;

		// Add coordinate-based map
		return $this->create_map();
	}

	/**
	 * Adds embedded map markup and sets up supporting scripts/script data.
	 *
	 * @return string
	 */
	protected function create_map() {
		$this->embedded_maps[] = array( $this->lat, $this->lng );

		end( $this->embedded_maps );
		$index = key( $this->embedded_maps );

		$template = locate_template( 'tribe-events/eventrocket/embedded-map.php' );
		if ( empty( $template ) ) $template = EVENTROCKET_INC . '/gps/embedded-map.php';

		ob_start();
		include $template;
		$html = ob_get_clean();

		$this->setup_script();
		return $html;
	}

	protected function setup_script() {
		if ( ! $this->map_script_enqueued ) $this->enqueue_map_scripts();
		wp_localize_script( self::MAP_HANDLER, 'eventrocket_map_data', $this->embedded_maps );
	}

	/**
	 * Sets up Event Rocket's map handling script - and the Google Maps JS API
	 * script - in the footer.
	 *
	 * It's possible to prevent the Google Maps API from being pulled in by
	 * setting up a filter as following:
	 *
	 *     add_filter( 'eventrocket_gps_add_gmap_api', '__return_false' );
	 *
	 * This could be useful in the event of a conflict with some other theme or
	 * plugin which also works with Google Maps. Similarly, the actual Google
	 * Maps API URL can be altered using the eventrocket_google_maps hook: this
	 * could be useful to force it to a new address or to append an API key.
	 */
	protected function enqueue_map_scripts() {
		$url = EVENTROCKET_URL . 'inc/gps/map-embed.js';
		wp_enqueue_script( self::MAP_HANDLER, $url, array( 'jquery' ), '1.4.4', true );

		if ( apply_filters( 'eventrocket_gps_add_gmap_api', true ) ) {
			$url = apply_filters( 'eventrocket_google_maps_api_url', '//maps.googleapis.com/maps/api/js' );
			wp_enqueue_script( self::GMAP_API, $url, array(), '1.0', true );
		}

		$this->map_script_enqueued = true;
	}
}

// Project GPS adds editing of long/lat coords for venues through the editor screen
new EventRocketGPS;