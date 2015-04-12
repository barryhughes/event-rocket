<?php
defined( 'ABSPATH' ) or exit();


class EventRocket_EmbedOrganizersShortcode
{
	/**
	 * @var EventRocket_OrganizerLister
	 */
	protected $finder;


	/**
	 * Sets up the [event_embed] shortcode.
	 *
	 * The actual shortcode name can be changed from "event_embed" to pretty much anything, using
	 * the eventrocket_embed_events_shortcode_name filter.
	 */
	public function __construct() {
		$shortcodes = (array) apply_filters( 'eventrocket_embed_organizers_shortcode_name', array( 'organizer_embed', 'embed_organizer' ) );
		foreach ( $shortcodes as $shortcode ) add_shortcode( $shortcode, array( $this, 'embed' ) );
	}

	/**
	 * Provides an alternative means of querying for events: any results that are found are
	 * returned in an array (which may be empty, if nothing is found).
	 *
	 * @param array $params
	 * @param string $content
	 * @return array
	 */
	public function obtain( array $params, $content = '' ) {
		$this->embed( $params, $content );
		return $this->finder->results();
	}

	/**
	 * Provides a programmatic means of embedding events. The output is returned as a string.
	 *
	 * @param array $params
	 * @param string $content
	 * @return string
	 */
	public function get( array $params, $content = '' ) {
		return $this->embed( $params, $content );
	}

	/**
	 * Provides a programmatic means of embedding events. The output is printed directly.
	 *
	 * @param array $params
	 * @param string $content
	 */
	public function render( array $params, $content = '' ) {
		echo $this->embed( $params, $content );
	}

	/**
	 * Embedded events request and shortcode handler.
	 *
	 * @param $params
	 * @param $content
	 * @return string
	 */
	public function embed( $params, $content ) {
		$params = ! empty( $params ) && is_array( $params ) ? $params : array();
		$content = trim( $content );

		$this->finder = new EventRocket_OrganizerLister( $params, $content );
		return $this->finder->output();
	}
}


/**
 * @return EventRocket_EmbedVenuesShortcode
 */
function organizer_embed() {
	static $object = null;
	if ( null === $object ) $object = new EventRocket_EmbedOrganizersShortcode;
	return $object;
}

// Call once to ensure the [event-embed] object is created
organizer_embed();