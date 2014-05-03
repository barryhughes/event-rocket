<?php
class EventRocketEmbedEventsShortcode
{
	protected $params = array();
	protected $content = '';
	protected $events = array();
	protected $ignore_events = array();
	protected $categories = array();
	protected $ignore_categories = array();
	protected $tags = array();
	protected $ignore_tags = array();
	protected $from = '';
	protected $to = '';
	protected $limit = 20;
	protected $template = '';


	public function __construct() {
		$shortcode = apply_filters( 'eventrocket_embed_events_shortcode_name', 'event_embed' );
		add_shortcode( $shortcode, array( $this, 'shortcode' ) );
	}

	/**
	 * Provides a programmatic means of using the event embed shortcode, returning
	 * the shortcode output as a string.
	 *
	 * @param array $params
	 * @param string $content
	 */
	public function get( array $params, $content = '' ) {
		return $this->shortcode( $params, $content );
	}

	/**
	 * Provides a programmatic means of using the event embed shortcode, printing
	 * the shortcode output directly.
	 *
	 * @param array $params
	 * @param string $content
	 */
	public function render( array $params, $content = '' ) {
		echo $this->shortcode( $params, $content );
	}

	public function shortcode( $params, $content ) {
		if ( ! empty( $params ) && is_array( $params ) ) $this->params = $params;
		$this->content = $content;
		$this->parse();
		print_r($this->events);
		print_r($this->ignore_events);
		print_r($this->categories);
		print_r($this->ignore_categories);
	}

	protected function parse() {
		$this->collect_post_tax_refs();
		$this->separate_ignore_values();
		$this->parse_post_tax_refs();
	}

	/**
	 * The user can use singular or plural forms to describe the events, categories
	 * and tags they are interested in querying against: this method simply looks
	 * for one or other - or both - and forms a single list of each.
	 */
	protected function collect_post_tax_refs() {
		$this->events = $this->plural_prop_csv( 'event', 'events' );
		$this->categories = $this->plural_prop_csv( 'category', 'categories' );
		$this->tags = $this->plural_prop_csv( 'tag', 'tags' );
	}

	/**
	 * The event and taxonomy params can include "negative" or ignore values indicating
	 * posts or terms to ignore. This method separates the negatives out into a seperate
	 * set of lists.
	 */
	protected function separate_ignore_values() {
		$this->move_ignore_vals( $this->events, $this->ignore_events );
		$this->move_ignore_vals( $this->categories, $this->ignore_categories );
		$this->move_ignore_vals( $this->tags, $this->ignore_tags );
	}

	/**
	 * Moves any values in $list prefixed with a negative operator ("-") to the
	 * ignore list.
	 *
	 * @param array $list
	 * @param array $ignore_list
	 */
	protected function move_ignore_vals( array &$list, array &$ignore_list ) {
		$keep_list = array();

		foreach ( $list as $value ) {
			if ( 0 === strpos( $value, '-') ) $ignore_list[] = substr( $value, 1 );
			else $keep_list[] = $value;
		}

		$list = $keep_list;
	}

	/**
	 * The event and taxonomy params all accept a mix of IDs and slugs:
	 * this method converts any slugs in those params back into IDs.
	 */
	protected function parse_post_tax_refs() {
		$this->parse_post_refs( $this->events );
		$this->parse_post_refs( $this->ignore_events );

		$this->parse_tax_refs( $this->categories, TribeEvents::TAXONOMY );
		$this->parse_tax_refs( $this->ignore_categories, TribeEvents::TAXONOMY );

		$this->parse_tax_refs( $this->tags, 'post_tag' );
		$this->parse_tax_refs( $this->ignore_tags, 'post_tag' );
	}

	/**
	 * Process the list of posts, turning any slugs into IDs.
	 *
	 * @param $list
	 */
	protected function parse_post_refs( &$list ) {
		foreach ( $list as $index => $reference ) {
			$this->typify( $reference );
			if ( ! is_string( $reference ) ) continue;

			$event = get_posts( array(
				'name' => $reference,
				'post_type' => TribeEvents::POSTTYPE,
				'eventDisplay' => 'custom',
				'posts_per_page' => 1
			) );

			if ( empty( $event ) || ! is_array( $event ) ) $list[$index] = 0;
			else $list[$index] = $event[0]->ID;
		}
	}

	/**
	 * Process the list of terms for the specified taxonomy, converting
	 * any term slugs into term IDs.
	 *
	 * @param $list
	 * @param $taxonomy
	 */
	protected function parse_tax_refs( &$list, $taxonomy ) {
		foreach ( $list as $index => $reference ) {
			$this->typify( $reference );
			if ( ! is_string( $reference ) ) continue;

			$term = get_term_by( 'slug', $reference, $taxonomy );
			if ( false === $term ) $list[$index] = 0;
			else $list[$index] = (int) $term->term_id;
		}
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

// Set the shortcode up and/or possibly define the event_embed() helper
if ( ! function_exists( 'event_embed' ) ) new EventRocketEmbedEventsShortcode;
else return;

/**
 * @return EventRocketEmbedEventsShortcode
 */
function event_embed() {
	static $object = null;
	if ( null === $object ) $object = new EventRocketEmbedEventsShortcode;
	return $object;
}

// Call once to ensure the [event-embed] object is created
event_embed();