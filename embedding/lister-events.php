<?php
class EventRocket_EventLister extends EventRocket_ObjectLister
{
	// Positive posts/terms to query against
	protected $events = array();
	protected $parent_events = array();
	protected $venues = array();
	protected $organizers = array();
	protected $categories = array();
	protected $tags = array();

	// Negative posts/terms to query against
	protected $ignore_events = array();
	protected $ignore_parent_events = array();
	protected $ignore_venues = array();
	protected $ignore_organizers = array();
	protected $ignore_categories = array();
	protected $ignore_tags = array();

	// Recurring event handling
	protected $recurring_events = array();

	// Miscellaneous conditions
	protected $tax_logic = 'OR';
	protected $from = '';
	protected $to = '';
	protected $template = '';
	protected $order = 'ASC';
	protected $where = '';


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
		$this->set_author();
		$this->set_template();
		$this->set_fallbacks();
		$this->set_order();
		$this->set_where();
		$this->set_cache();
		$this->set_blog();
	}

	/**
	 * The user can use singular or plural forms to describe the events, categories
	 * and tags they are interested in querying against: this method simply looks
	 * for one or other - or both - and forms a single list of each.
	 */
	protected function collect_post_tax_refs() {
		$this->events = $this->prop_from_csv( 'event', 'events' );
		$this->venues = $this->prop_from_csv( 'venue', 'venues' );
		$this->organizers = $this->prop_from_csv( 'organizer', 'organizers' );
		$this->categories = $this->prop_from_csv( 'category', 'categories' );
		$this->tags = $this->prop_from_csv( 'tag', 'tags' );
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
		$this->rebuild_event_lists();

		$this->parse_post_refs( $this->venues, Tribe__Events__Main::VENUE_POST_TYPE );
		$this->parse_post_refs( $this->ignore_venues, Tribe__Events__Main::VENUE_POST_TYPE );

		$this->parse_post_refs( $this->organizers, Tribe__Events__Main::ORGANIZER_POST_TYPE );
		$this->parse_post_refs( $this->ignore_organizers, Tribe__Events__Main::ORGANIZER_POST_TYPE );

		$this->parse_tax_refs( $this->categories, Tribe__Events__Main::TAXONOMY );
		$this->parse_tax_refs( $this->ignore_categories, Tribe__Events__Main::TAXONOMY );

		$this->parse_tax_refs( $this->tags, 'post_tag' );
		$this->parse_tax_refs( $this->ignore_tags, 'post_tag' );

		// Default to an "OR" relationship between different tax queries, but allow for "AND"
		if ( isset( $this->params['logic'] ) && 'and' === strtolower( $this->params['logic'] ) )
			$this->tax_logic = 'AND';
	}

	/**
	 * Copies recurring event parent IDs into lists of their own.
	 */
	protected function rebuild_event_lists() {
		// Inclusive
		foreach ( $this->events as $index => $event_id ) {
			if ( ! in_array( $event_id, $this->recurring_events ) ) continue;
			$this->parent_events[] = $event_id;
		}

		// Exclusive
		foreach ( $this->ignore_events as $index => $event_id ) {
			if ( ! in_array( $event_id, $this->recurring_events ) ) continue;
			$this->ignore_parent_events[] = $event_id;
		}
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
	 * We override the parent load_post_by_slug() method in order to better support recurring
	 * events.
	 *
	 * It appears to an end user that a recurring event has a slug like "my-event", as they
	 * have URLs in the form "example.com/events/my-event/(date)". However, the true slug/post
	 * name is actually more like "my-event-yyyy-mm-dd".
	 *
	 * This causes a bit of a disconnect which this method tries to solve.
	 *
	 * @param  $slug
	 * @param  $post_type
	 * @return array
	 */
	protected function load_post_by_slug( $slug, $post_type ) {
		$event = parent::load_post_by_slug( $slug, $post_type );

		// If PRO is not activated/there are no recurrence facilities, let's do nothing more
		if ( ! function_exists( 'tribe_is_recurring_event' ) ) return $event;

		// If this specific event is not in fact recurring in nature, let's do nothing more
		if ( ! tribe_is_recurring_event( $event->ID ) ) return $event;

		// Add to list of recurring events before returning parent ID
		$rec_id = $event->post_parent ? $event->post_parent : $event->ID;
		$this->recurring_events[] = $rec_id;
		return $event;
	}

	/**
	 * Looks for time (from/to) parameters, ensuring they are in a form we like.
	 */
	protected function set_time_constraints() {
		if ( isset( $this->params['from'] ) ) $this->time_from();
		if ( isset( $this->params['to' ] ) ) $this->time_to();
	}

	/**
	 * Allow for events to be ordered reverse chronologically (farthest in the future
	 * first).
	 */
	protected function set_order() {
		if ( ! isset( $this->params['order'] ) ) return;

		$keywords = array( 'desc', 'descending', 'backwards', 'reverse' );
		$keywords[] = _x( 'desc', 'embedded event sequence', 'eventrocket' );
		$keywords[] = _x( 'descending', 'embedded event sequence', 'eventrocket' );
		$keywords[] = _x( 'backwards', 'embedded event sequence', 'eventrocket' );
		$keywords[] = _x( 'reverse', 'embedded event sequence', 'eventrocket' );

		$direction = strtolower( $this->params['order'] );
		if ( in_array( $direction, $keywords ) ) $this->order = 'DESC';
	}

	/**
	 * Sets a specialist condition. Currently supported: "ongoing" which means an event
	 * that is currently in progress. More specialist cases may be added.
	 */
	protected function set_where() {
		if ( ! isset( $this->params['where'] ) ) return;

		$keywords = array( 'ongoing', 'current', 'in-progress', 'in progress' );
		$keywords[] = _x( 'ongoing', 'embedded event condition', 'eventrocket' );
		$keywords[] = _x( 'current', 'embedded event condition', 'eventrocket' );
		$keywords[] = _x( 'in-progress', 'embedded event condition', 'eventrocket' );
		$keywords[] = _x( 'in progress', 'embedded event condition', 'eventrocket' );

		$where = strtolower( $this->params['where'] );
		if ( in_array( $where, $keywords ) ) $this->where = 'ongoing';
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
		$this->args = array( 'post_type' => Tribe__Events__Main::POSTTYPE ); // Reset
		$this->args_post_tax();
		$this->args_venue_organizer();
		$this->args_time();
		$this->args_author();
		$this->args_limit();
		$this->args_display_type();
		$this->args_order();
		$this->args_where();
		$this->args = apply_filters( 'eventrocket_embed_event_args', $this->args, $this->params );
		$this->results = tribe_get_events( $this->args );
	}

	/**
	 * Populate the post (event) and taxonomy query arguments.
	 */
	protected function args_post_tax() {
		$tax_args = array();

		add_filter( 'posts_where', array( $this, 'event_filtering' ), 50, 2 );

		if ( ! empty( $this->categories ) )
			$this->build_tax_args( $tax_args, Tribe__Events__Main::TAXONOMY, $this->categories );

		if ( ! empty( $this->ignore_categories ) )
			$this->build_tax_args( $tax_args, Tribe__Events__Main::TAXONOMY, $this->ignore_categories, true );

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
	 * Including and excluding normal and recurring events could be done with post__in,
	 * post__not_in, post_parent__in arguments, etc, however in anything but simple cases
	 * this fails - and so we implement our own where clause to handle this.
	 */
	public function event_filtering( $where_sql ) {
		global $wpdb;

		// We don't want this to impact other queries that might run later
		remove_filter( 'posts_where', array( $this, 'event_filtering' ), 50 );

		// Refine event lists to
		$include_posts    = join( ' , ', $this->events );
		$exclude_posts    = join( ' , ', $this->ignore_events );
		$include_children = join( ' , ', $this->parent_events );
		$exclude_children = join( ' , ', $this->ignore_parent_events );
		$positives = '';
		$negatives = '';
		$condition = '';

		// Positives/inclusions
		if ( ! empty( $include_posts ) ) $positives .= " $wpdb->posts.ID IN ( $include_posts ) ";
		if ( ! empty( $include_posts ) && ! empty( $include_children ) ) $positives .= " OR ";
		if ( ! empty( $include_children ) ) $positives .= " $wpdb->posts.post_parent IN ( $include_children ) ";
		if ( ! empty( $include_posts ) || ! empty( $include_children ) ) $positives = " ( $positives ) ";

		// Negatives/exclusions
		if ( ! empty( $exclude_posts ) ) $negatives .= " $wpdb->posts.ID NOT IN ( $exclude_posts ) ";
		if ( ! empty( $exclude_posts ) && ! empty( $exclude_children ) ) $negatives .= " AND ";
		if ( ! empty( $exclude_children ) ) $negatives .= " $wpdb->posts.post_parent NOT IN ( $exclude_children ) ";
		if ( ! empty( $exclude_posts ) || ! empty( $exclude_children ) ) $negatives = " ( $negatives ) ";

		// Join both sets of conditions
		if ( ! empty( $positives ) && ! empty( $negatives ) ) $condition = " ( $positives AND $negatives ) ";
		elseif ( ! empty( $positives ) && empty( $negatives ) ) $condition = $positives;
		elseif ( ! empty( $negatives ) && empty( $positives ) ) $condition = $negatives;

		// If we have a condition to add, let's add it!
		return empty( $condition ) ? $where_sql : " $where_sql AND $condition ";
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
		if ( ! empty( $this->from ) ) $this->args['start_date'] = $this->from;
		if ( ! empty( $this->to ) ) $this->args['end_date'] = $this->to;
	}

	/**
	 * Set the eventDisplay query argument appropriately.
	 */
	protected function args_display_type() {
		$has_start    = isset( $this->args['start_date'] );
		$has_end      = isset( $this->args['end_date'] );
		$specific_ids = ! empty( $this->events );

		$this->args['eventDisplay'] = ( $has_start || $has_end || $specific_ids )
			? 'custom'
			: 'list';
	}

	/**
	 * Optionally forces the order to DESC.
	 */
	protected function args_order() {
		if ( 'DESC' !== $this->order ) return;
		add_filter( 'tribe_events_query_posts_orderby', array( $this, 'force_desc_order' ) );
	}

	/**
	 * Optionally enforce some special condition.
	 */
	protected function args_where() {
		if ( empty( $this->where ) ) return;
		if ( 'ongoing' === $this->where ) {
			$this->args['start_date']   = '1000-01-01 00:00';
			$this->args['end_date']     = '9999-12-31 23:59';
			$this->args['eventDisplay'] = 'custom';
			add_filter( 'posts_where', array( $this, 'force_ongoing' ), 50, 2 );
		}
	}

	/**
	 * Restrict result set to ongoing/in-progress events.
	 */
	public function force_ongoing( $where_sql ) {
		global $wpdb;
		$now = date_i18n( Tribe__Events__Date_Utils::DBDATETIMEFORMAT, time() );

		// Tear this filter down, we don't want it running on subsequent event queries unless explicitly set up
		remove_filter( 'posts_where', array( $this, 'force_ongoing' ), 50, 2 );

		// Form our ongoing events conditions
		$extra_conditions = $wpdb->prepare( " AND
				( $wpdb->postmeta.meta_value <= %s AND tribe_event_end_date.meta_value >= %s )
			", $now, $now
		);

		// Append and return
		return $where_sql . $extra_conditions;
	}

	/**
	 * Slightly hackish but straightforward means of forcing descending order results.
	 *
	 * @param $order_sql
	 * @return mixed
	 */
	public function force_desc_order( $order_sql ) {
		remove_filter( 'tribe_events_query_posts_orderby', array( $this, 'force_desc_order' ) );
		return str_replace( 'ASC', 'DESC', $order_sql );
	}

	protected function get_inline_parser() {
		return new EventRocket_EmbeddedEventTemplateParser;
	}
}