<?php
defined( 'ABSPATH' ) or exit();


/**
 * Extremely basic templating engine for embedding templates inline between opening and
 * closing shortcodes.
 */
class EventRocket_EmbeddedEventTemplateParser
{
	public $output = '';

	protected $placeholders = array(
		'{link}' => 'get_permalink',
		'{url}' => 'get_permalink',
		'{title}' => 'get_the_title',
		'{name}' => 'get_the_title',
		'{content}' => 'get_the_content',
		'{description}' => 'get_the_content',
		'{excerpt}' => 'get_the_excerpt',
		'{thumbnail}' => 'tribe_event_featured_image',
		'{start_date}' => array( '__this__', 'start_date' ),
		'{start_time}' => array( '__this__', 'start_time' ),
		'{end_date}' => array( '__this__', 'end_date' ),
		'{end_time}' => array( '__this__', 'end_time' )
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
}