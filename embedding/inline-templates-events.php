<?php
defined( 'ABSPATH' ) or exit();


/**
 * Extremely basic templating engine for embedding templates inline between opening and
 * closing shortcodes.
 */
class EventRocket_EmbeddedEventTemplateParser implements EventRocket_iInlineParser
{
	protected $output = '';

	protected $placeholders = array(
		'{link}'               => 'get_permalink',
		'{url}'                => 'get_permalink',
		'{title}'              => 'get_the_title',
		'{name}'               => 'get_the_title',
		'{content}'            => array( '__this__', 'content' ),
		'{content:unfiltered}' => 'get_the_content',
		'{description}'        => array( '__this__', 'content' ),
		'{excerpt}'            => 'get_the_excerpt',
		'{thumbnail}'          => 'tribe_event_featured_image',
		'{author}'             => 'get_the_author',
		'{start_date}'         => array( '__this__', 'start_date' ),
		'{start_time}'         => array( '__this__', 'start_time' ),
		'{end_date}'           => array( '__this__', 'end_date' ),
		'{end_time}'           => array( '__this__', 'end_time' ),
		'{venue}'              => 'tribe_get_venue',
		'{venue:name}'         => 'tribe_get_venue',
		'{venue:link}'         => array( '__this__', 'tribe_get_venue_link' ),
		'{venue:url}'          => array( '__this__', 'tribe_get_venue_url' ),
		'{organizer}'          => 'tribe_get_organizer',
		'{organizer:link}'     => array( '__this__', 'tribe_get_organizer_link' ),
		'{organizer:url}'      => array( '__this__', 'tribe_get_organizer_url' )
	);


	public function __construct() {
		$this->placeholders = apply_filters( 'eventrocket_embedded_event_placeholders', $this->placeholders );
		$this->adjust_callbacks();
	}

	protected function adjust_callbacks() {
		foreach ( $this->placeholders as &$callback )
			if ( is_array( $callback ) && '__this__' === $callback[0] ) $callback[0] = $this;
	}

	public function process( $content ) {
		$this->output = ''; // Reset
		foreach ( $this->placeholders as $tag => $handler ) {
			if ( false === strpos( $content, $tag ) ) continue;
			$value = call_user_func( $handler );
			$content = str_replace( $tag, $value, $content );
		}
		$this->output = apply_filters( 'eventrocket_embedded_event_output', $content );
	}

	public function output() {
		return $this->output;
	}

	public function content() {
		return apply_filters( 'the_content', get_the_content() );
	}

	public function start_date() {
		return tribe_get_start_date( null, false, get_option( 'date_format', 'j F Y' ) );
	}

	public function start_time() {
		return tribe_get_start_date( null, false, get_option( 'time_format', 'H:i' ) );
	}

	public function end_date() {
		return tribe_get_end_date( null, false, get_option( 'date_format', 'j F Y' ) );
	}

	public function end_time() {
		return tribe_get_end_date( null, false, get_option( 'time_format', 'H:i' ) );
	}

	public function tribe_get_venue_link() {
		// This looks like an oddway to fetch a linked venue title - but it works around quirkiness in
		// TEC's template tag system. Calling tribe_get_venue_link() without args prints what we want
		// to the screen but we want to return it; setting its optional $display arg to false returns
		// the URL, but not as well formed HTML
		ob_start();
		tribe_get_venue_link( null, true );
		return ob_get_clean();
	}

	/** @see $this->tribe_get_venue_link() */
	public function tribe_get_venue_url() {
		return tribe_get_venue_link( null, false );
	}

	public function tribe_get_organizer_link() {
		return tribe_get_organizer_link( null, true, false );
	}

	public function tribe_get_organizer_url() {
		return tribe_get_organizer_link( null, false, false );
	}
}