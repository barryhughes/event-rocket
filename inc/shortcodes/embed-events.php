<?php
class EventRocketEmbedEventsShortcode
{
	protected $params = array();
	protected $content = '';
	protected $events = array();
	protected $categories = array();
	protected $tags = array();


	public function __construct() {
		$shortcode = apply_filters( 'eventrocket_embed_events_shortcode_name', 'event_embed' );
		add_shortcode( 'event_embed', array( $this, 'shortcode' ) );
	}

	public function shortcode( $params, $content ) {
		if ( ! empty( $params ) && is_array( $params ) ) $this->params = $params;
		$this->content = $content;
		$this->parse();
	}

	protected function parse() {
		$this->collect_post_tax_refs();
		$this->parse_post_tax_refs();
	}

	protected function collect_post_tax_refs() {
		$this->events = $this->plural_prop_csv( 'event', 'events' );
		$this->categories = $this->plural_prop_csv( 'category', 'categories' );
		$this->tags = $this->plural_prop_csv( 'tag', 'tags' );
	}

	protected function parse_post_tax_refs() {
		$this->parse_post_refs();
		$this->parse_tax_refs( $this->categories, TribeEvents::TAXONOMY );
		$this->parse_tax_refs( $this->tags, 'tag' );
	}

	protected function parse_post_refs() {
		foreach ( $this->events as $index => $reference ) {
			$this->typify( $reference );
			if ( ! is_string( $reference ) ) continue;

			$event = get_posts( array(
				'name' => $reference,
				'post_type' => TribeEvents::POSTTYPE,
				'eventDisplay' => 'custom',
				'posts_per_page' => 1
			) );

			if ( empty( $event ) || ! is_array( $event ) ) $this->events[$index] = 0;
			$this->events[$index] = $event[0]->ID;
		}
	}

	protected function parse_tax_refs( &$list, $taxonomy ) {

	}

	/**
	 * Inspect the properties array for values assigned with either the $singular or $plural
	 * key: combine and extract the values, returning them as an array (may be empty if
	 * for instance no such shortcode params were supplied).
	 *
	 * @param $singular
	 * @param $plural
	 * @return array
	 */
	protected function plural_prop_csv( $singular, $plural ) {
		$singular = isset( $this->params[$singular] ) ? (string) $this->params[$singular] : '';
		$plural = isset( $this->params[$plural] ) ? (string) $this->params[$plural] : '';
		$combined = "$singular,$plural";

		$values = explode( ',', $combined );
		$result_set = array();

		foreach ( $values as $value ) {
			$value = trim( $value );
			if ( ! empty($value) && ! in_array( $value, $result_set ) )
				$result_set[] = trim($value);
		}

		return $result_set;
	}

	/**
	 * Accepts a value and if it appears to be a string it is returned as-is. If it
	 * appears to be a number expressed as a string then it is converted to an int
	 * and, if it is numeric, it is simply returned as an int.
	 *
	 * @param $value
	 */
	protected function typify( &$value ) {
		$value = is_numeric( $value ) ? (int) $value : (string) $value;
	}
}

new EventRocketEmbedEventsShortcode;