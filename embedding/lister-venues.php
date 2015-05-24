<?php
class EventRocket_VenueLister extends EventRocket_ObjectLister
{
	// Inputs
	protected $params = array();
	protected $content = '';

	// Positive posts/terms to query against
	protected $venues = array();

	// Negative posts/terms to query against
	protected $ignore_venues = array();

	// Other conditions
	protected $city = array();
	protected $state_province = array();
	protected $post_code = array();
	protected $country = array();
	protected $with_events = true;

	// Caching
	protected $cache_key_data = '';
	protected $cache_key_html = '';
	protected $cache_expiry = 0;

	// Nothing found fallbacks
	protected $nothing_found_text = '';
	protected $nothing_found_template = '';

	// Internal
	protected $args = array();
	protected $results = array();
	protected $event_post;
	protected $output = '';


	public function __construct( array $params, $content ) {
		$this->fallback = EVENTROCKET_INC . '/templates/embedded-venues.php';
		parent::__construct( $params, $content );
	}

	protected function execute() {
		$this->parse();

		if ( ! $this->cache_get() ) {
			$this->query();
			$this->build();
		}
	}

	protected function parse() {
		$this->collect_post_tax_refs();
		$this->separate_ignore_values();
		$this->set_cache();
		$this->set_limit();
		$this->set_template();
		$this->set_with_events();
		$this->set_geo_query();
	}

	protected function set_with_events() {
		if ( ! isset( $this->params['with_events'] ) ) return;
		$this->with_events = $this->is_on( $this->params['with_events'] );
	}

	/**
	 * Allows for restriction of events by city, state/province and so on.
	 */
	protected function set_geo_query() {
		$this->state_province = $this->prop_from_csv( 'state', 'states', 'province', 'provinces' );
		$this->city           = $this->prop_from_csv( 'city', 'cities' );
		$this->post_code      = $this->prop_from_csv( 'postcode', 'postcodes', 'zip', 'zips' );
		$this->country        = $this->prop_from_csv( 'country', 'countries' );
	}

	/**
	 * The user can use singular or plural forms to describe the venues.
	 *
	 * Venues don't support taxonomies at this time but we're following the
	 * template laid by the event lister here and could potentially add
	 * code to collect taxonomy params in here, too, as some future point.
	 */
	protected function collect_post_tax_refs() {
		$this->venues = $this->prop_from_csv( 'venue', 'venues' );
	}

	/**
	 * Venue and any taxonomy params can include "negative" or ignore values indicating
	 * posts or terms to ignore. This method separates the negatives out into a seperate
	 * set of lists.
	 */
	protected function separate_ignore_values() {
		$this->move_ignore_vals( $this->venues, $this->ignore_venues );
	}

	protected function query() {
		$this->enter_blog();
		$this->args = array(
			'post_type' => Tribe__Events__Main::VENUE_POST_TYPE,
			'suppress_filters' => false // We may need to modify the where clause
		);

		$this->args_post_tax();
		$this->args_with_events();
		$this->args_geo_query();
		$this->args = apply_filters( 'eventrocket_embed_venue_args', $this->args, $this->params );
		$this->results = get_posts( $this->args );
	}

	/**
	 * Populate the post (venue) and potentially any taxonomy query arguments (though
	 * taxonomies are not currently supported by venues, they may be in future).
	 */
	protected function args_post_tax() {
		if ( ! empty( $this->venues ) ) $this->args['post__in'] = $this->venues;
		if ( ! empty( $this->ignore_venues ) ) $this->args['post__not_in'] = $this->ignore_venues;
	}

	/**
	 * If we are only interested in venues with (current or upcoming) events we need to
	 * do some query voodoo.
	 */
	protected function args_with_events() {
		if ( $this->with_events )
			add_filter( 'posts_where', array( $this, 'add_where_events_clause' ) );
	}

	public function add_where_events_clause( $where_sql ) {
		global $wpdb;
		$right_now = date_i18n( Tribe__Events__Date_Utils::DBDATETIMEFORMAT );

		// We don't want this filter to be reused repeatedly
		remove_filter( 'posts_where', array( $this, 'add_where_events_clause' ) );

		// Form the subquery
		$subquery = "
			SELECT DISTINCT
			    venue_meta.meta_value
			FROM
			    $wpdb->posts
			        JOIN
			    $wpdb->postmeta AS venue_meta ON venue_meta.post_id = ID
			        JOIN
			    $wpdb->postmeta AS date_meta ON date_meta.post_id = ID
			WHERE
			    (venue_meta.meta_key = '_EventVenueID'
			        AND venue_meta.meta_value > 0)
			        AND (date_meta.meta_key = '_EventEndDate'
			        AND date_meta.meta_value >= %s)
		";

		$subquery = $wpdb->prepare( $subquery, $right_now );
		return $where_sql . " AND $wpdb->posts.ID IN ( $subquery ) ";
	}

	/**
	 * Adds meta queries needed to restrict the result set to specific countries/states/etc.
	 */
	protected function args_geo_query() {
		$meta_queries = array();
		if ( ! isset( $this->args['meta_query'] ) ) $this->args['meta_query'] = array();

		// City
		if ( ! empty( $this->city ) )
			$meta_queries[] = array( 'key' => '_VenueCity', 'value' => $this->city, 'compare' => 'IN' );

		// State/province
		if ( ! empty( $this->state_province ) )
			$meta_queries[] = array( 'key' => '_VenueStateProvince', 'value' => $this->state_province, 'compare' => 'IN' );

		// Country
		if ( ! empty( $this->country ) )
			$meta_queries[] = array( 'key' => '_VenueCountry', 'value' => $this->country, 'compare' => 'IN' );

		// Postcode (broken into multiple meta queries to allow for "fuzzy" matching, ie "902*")
		if ( ! empty( $this->post_code ) ) foreach ( $this->post_code as $postcode_match )
			$meta_queries[] = array( 'key' => '_VenueZip', 'value' => $postcode_match, 'compare' => 'LIKE' );

		// Merge in
		$this->args['meta_query'] = array_merge( $this->args['meta_query'], $meta_queries );
	}

	protected function get_inline_parser() {
		return new EventRocket_EmbeddedVenueTemplateParser;
	}
}