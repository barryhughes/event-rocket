<?php
class EventRocket_EventLister extends EventRocket_ObjectLister
{
	// Positive posts/terms to query against
	protected $events = array();
	protected $venues = array();
	protected $organizers = array();
	protected $categories = array();
	protected $tags = array();

	// Negative posts/terms to query against
	protected $ignore_events = array();
	protected $ignore_venues = array();
	protected $ignore_organizers = array();
	protected $ignore_categories = array();
	protected $ignore_tags = array();

	// Miscellaneous conditions
	protected $tax_logic = 'OR';
	protected $from = '';
	protected $to = '';
	protected $limit = 20;
	protected $template = '';


	public function __construct( array $params, $content ) {
		$this->fallback = EVENTROCKET_INC . '/templates/embedded-events.php';
		parent::__construct( $params, $content );
	}

	/**
	 * Pre-process and get what we need from any parameters that were provided.
	 */
	protected function parse() {
		$this->collect_post_tax_refs();
		$this->separate_ignore_values();
		$this->parse_post_tax_refs();
		$this->set_time_constraints();
		$this->set_limit();
		$this->set_template();
		$this->set_fallbacks();
		$this->set_cache();
		$this->set_blog();
	}

	/**
	 * The user can use singular or plural forms to describe the events, categories
	 * and tags they are interested in querying against: this method simply looks
	 * for one or other - or both - and forms a single list of each.
	 */
	protected function collect_post_tax_refs() {
		$this->events = $this->plural_prop_csv( 'event', 'events' );
		$this->venues = $this->plural_prop_csv( 'venue', 'venues' );
		$this->organizers = $this->plural_prop_csv( 'organizer', 'organizers' );
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
		$this->move_ignore_vals( $this->venues, $this->ignore_venues );
		$this->move_ignore_vals( $this->organizers, $this->ignore_organizers );
		$this->move_ignore_vals( $this->categories, $this->ignore_categories );
		$this->move_ignore_vals( $this->tags, $this->ignore_tags );
	}

	/**
	 * The event and taxonomy params all accept a mix of IDs and slugs:
	 * this method converts any slugs in those params back into IDs.
	 */
	protected function parse_post_tax_refs() {
		$this->parse_post_refs( $this->events );
		$this->parse_post_refs( $this->ignore_events );

		$this->parse_post_refs( $this->venues, TribeEvents::VENUE_POST_TYPE );
		$this->parse_post_refs( $this->ignore_venues, TribeEvents::VENUE_POST_TYPE );

		$this->parse_post_refs( $this->organizers, TribeEvents::ORGANIZER_POST_TYPE );
		$this->parse_post_refs( $this->ignore_organizers, TribeEvents::ORGANIZER_POST_TYPE );

		$this->parse_tax_refs( $this->categories, TribeEvents::TAXONOMY );
		$this->parse_tax_refs( $this->ignore_categories, TribeEvents::TAXONOMY );

		$this->parse_tax_refs( $this->tags, 'post_tag' );
		$this->parse_tax_refs( $this->ignore_tags, 'post_tag' );

		// Default to an "OR" relationship between different tax queries, but allow for "AND"
		if ( isset( $this->params['logic'] ) && 'and' === strtolower( $this->params['logic'] ) )
			$this->tax_logic = 'AND';
	}

	/**
	 * Process the list of terms for the specified taxonomy, converting
	 * any term slugs into term IDs and grouping terms together where
	 * an AND condition should be applied.
	 *
	 * @param $list
	 * @param $taxonomy
	 */
	protected function parse_tax_refs( &$list, $taxonomy ) {
		foreach ( $list as $index => $term ) {
			// Convert each list item to an array
			$list[$index] = array();

			// Each "term" may actually be multiple terms, joined via the "+" symbol to mark an "AND" condition
			$terms = explode( '+', $term );

			// Look at each term reference: convert slugs to numeric IDs and group terms together as needed
			foreach ( $terms as $term_ref ) {
				$this->typify( $term_ref ); // Convert numeric strings to actual integers, etc

				// If an integer, do not process further - just add it to the list
				if ( is_int( $term_ref ) ) {
					$list[$index][] = $term_ref;
				}
				// If a string, convert to an integer (ie, get the term ID) - then add to the list
				else {
					$term = get_term_by( 'slug', $term_ref, $taxonomy);
					if ( false === $term ) $list[$index][] = 0;
					else $list[$index][] = (int) $term->term_id;
				}
			}
		}
	}

	/**
	 * Looks for time (from/to) parameters, ensuring they are in a form we like.
	 */
	protected function set_time_constraints() {
		if ( isset( $this->params['from'] ) ) $this->time_from();
		if ( isset( $this->params['to' ] ) ) $this->time_to();
	}

	/**
	 * Ensure the from param is a well formed date. Convert to a standard format where possible
	 * and store.
	 */
	protected function time_from() {
		$datetime = strtotime( $this->params['from'] );
		if ( ! $datetime ) $this->from = '';
		else $this->from = date( 'Y-m-d H:i:s', $datetime );
	}

	/**
	 * Ensure the to param is a well formed date. Convert to a standard format where possible
	 * and store.
	 */
	protected function time_to() {
		$datetime = strtotime( $this->params['to'] );
		if ( ! $datetime ) $this->to = '';
		else $this->to = date( 'Y-m-d H:i:s', $datetime );
	}

	/**
	 * Retrieve the events based on the parameters provided.
	 */
	protected function query() {
		$this->enter_blog();
		$this->args = array( 'post_type' => TribeEvents::POSTTYPE ); // Reset
		$this->args_post_tax();
		$this->args_venue_organizer();
		$this->args_time();
		$this->args_limit();
		$this->args_display_type();
		$this->args = apply_filters( 'eventrocket_embed_event_args', $this->args, $this->params );
		$this->results = tribe_get_events( $this->args );
	}

	/**
	 * Populate the post (event) and taxonomy query arguments.
	 */
	protected function args_post_tax() {
		$tax_args = array();

		if ( ! empty( $this->events ) ) $this->args['post__in'] = $this->events;
		if ( ! empty( $this->ignore_events ) ) $this->args['post__not_in'] = $this->ignore_events;

		if ( ! empty( $this->categories ) )
			$this->build_tax_args( $tax_args, TribeEvents::TAXONOMY, $this->categories );

		if ( ! empty( $this->ignore_categories ) )
			$this->build_tax_args( $tax_args, TribeEvents::TAXONOMY, $this->ignore_categories, true );

		if ( ! empty( $this->tags) )
			$this->build_tax_args( $tax_args, 'post_tag', $this->tags );

		if ( ! empty( $this->ignore_tags ) )
			$this->build_tax_args( $tax_args, 'post_tag', $this->ignore_tags, true );

		if ( ! empty( $tax_args ) ) {
			$tax_args['relation'] = $this->tax_logic;
			$this->args['tax_query'] = $tax_args;
		}
	}

	/**
	 * Helper that puts together a set of tax query arguments for a term or group of terms.
	 *
	 * @param array $tax_args
	 * @param $taxonomy
	 * @param $term_set
	 * @param $exclude
	 */
	protected function build_tax_args( array &$tax_args, $taxonomy, $term_set, $exclude = false ) {
		foreach ( $term_set as $terms ) {
			$operator = $exclude ? 'NOT IN' : ( count( $terms ) > 1 ? 'AND' : 'IN' );

			$tax_args[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'id',
				'terms'    => $terms,
				'operator' => $operator
			);
		}
	}

	protected function args_venue_organizer() {
		$meta_queries = array();

		if ( ! empty( $this->venues ) )
			$meta_queries[] = $this->form_meta_arg( '_EventVenueID', $this->venues, 'IN' );

		if ( ! empty( $this->ignore_venues ) )
			$meta_queries[] = $this->form_meta_arg( '_EventVenueID', $this->ignore_venues, 'NOT IN' );

		if ( ! empty( $this->organizers ) )
			$meta_queries[] = $this->form_meta_arg( '_EventOrganizerID', $this->organizers, 'IN' );

		if ( ! empty( $this->ignore_organizers ) )
			$meta_queries[] = $this->form_meta_arg( '_EventOrganizerID', $this->ignore_organizers, 'NOT IN' );

		if ( ! isset( $this->args['meta_query'] ) ) $this->args['meta_query'] = $meta_queries;
		else $this->args['meta_query'] = array_merge( $meta_queries, $this->args['meta_query'] );
	}

	protected function form_meta_arg( $key, $value, $compare ) {
		return array(
			'key' => $key,
			'value' => $value,
			'compare' => $compare
		);
	}

	protected function args_time() {
		if (!empty($this->from)) $this->args['start_date'] = $this->from;
		if (!empty($this->to)) $this->args['end_date'] = $this->to;
	}

	/**
	 * Set the eventDisplay query argument appropriately.
	 */
	protected function args_display_type() {
		$this->args['eventDisplay'] = ( isset( $this->args['start_date'] ) || isset( $this->args['end_date'] ) || isset( $this->args['post__in'] ) )
			? 'custom' : 'list';
	}

	protected function get_inline_parser() {
		return new EventRocket_EmbeddedEventTemplateParser;
	}
}