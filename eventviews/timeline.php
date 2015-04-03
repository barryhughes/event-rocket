<?php
class EventRocket_Timeline {
	protected $name = '';
	protected $slug = '';


	public function __construct() {
		add_action( 'generate_rewrite_rules', array( $this, 'routes' ) );
		add_action( 'pre_get_posts',          array( $this, 'adapter' ) );
		add_filter( 'tribe-events-bar-views', array( $this, 'selector' ) );
		add_filter( 'tribe_events_current_template_class', array( $this, 'template_class' ) );
	}

	/**
	 * @param $rewrite
	 */
	public function routes( $rewrite ) {
		$tec  = Tribe__Events__Events::instance();
		$base = trailingslashit( $tec->rewriteSlug );
		$type = Tribe__Events__Events::POSTTYPE;
		$tax  = Tribe__Events__Events::TAXONOMY;
		$date = '(\d{4}-\d{2}-\d{2})';
		$slug = $this->slug();

		$base_rules      = 'index.php?post_type=' . $type . '&eventDisplay=' . $slug;
		$base_categories = '(.*)' . trailingslashit( $tec->taxRewriteSlug ) . '(?:[^/]+/)*';
		$base_tags       = '(.*)' . trailingslashit( $tec->tagRewriteSlug ) . '(?:[^/]+/)*';;
		$base_simple     = $base . $slug;

		$timeline_rules = array(
			$base_simple . "/?$"             => $base_rules,
			$base_simple . "/$date/?$"       => $base_rules . "&eventDate=" . $rewrite->preg_index( 1 ),
			$base_simple . "/page/(\d+)"     => $base_rules . "&paged=". $rewrite->preg_index( 1 ),
			$base_categories . "/?$"         => $base_rules . "&$tax=" . $rewrite->preg_index( 2 ),
			$base_categories . "/$date$"     => $base_rules . "&$tax=" . $rewrite->preg_index( 2 ) . "&eventDate=" . $rewrite->preg_index( 3 ),
			$base_categories . "/page/(\d+)" => $base_rules . "&$tax=" . $rewrite->preg_index( 2 ) . "&paged=". $rewrite->preg_index( 3 ),
			$base_tags . "/?$"               => $base_rules . "&tag=" . $rewrite->preg_index( 2 ),
			$base_tags . "/$date$"           => $base_rules . "&tag=" . $rewrite->preg_index( 2 ) . "&eventDate=" . $rewrite->preg_index( 3 ),
			$base_tags . "/page/(\d+)"       => $base_rules . "&tag=" . $rewrite->preg_index( 2 ) . "&paged=". $rewrite->preg_index( 3 )
		);

		$rewrite->rules = $timeline_rules + $rewrite->rules;
	}

	/**
	 * Timeline view piggy backs on the existing list view.
	 *
	 * @param $query
	 */
	public function adapter( $query ) {
		if ( 'timeline' !== $query->get( 'eventDisplay' ) ) return;
		$query->set( 'eventDisplay', 'list' );
		$query->set( 'eventrocket_view', 'timeline' );
		add_action( 'wp_head', array( $this, 'set_displaying' ) );
	}

	/**
	 * Once the page begins to be rendered (ie, wp_head has fired) lets ensure timeline view
	 * is set as the currently displaying view. We also need to unhook TEC's own setDisplay()
	 * callback which otherwise will be called repeatedly and undo this change.
	 */
	public function set_displaying() {
		$tec = Tribe__Events__Events::instance();
		$tec->displaying = $this->slug();
		remove_action( 'parse_query', array( $tec, 'setDisplay' ), 51, 0 );
	}

	/**
	 * @return bool
	 */
	public function is_timeline_view() {
		global $wp_query;
		return ( 'timeline' === $wp_query->get( 'eventrocket_view' ) ) ? true : false;
	}

	public function selector( $views ) {
		$views[] = array(
			'displaying' => $this->slug(),
			'anchor'     => $this->name(),
			'url'        => get_timeline_url()
		);
		return $views;
	}

	public function slug() {
		if ( ! empty( $this->slug ) ) return $this->slug;
		$this->slug = apply_filters( 'eventrocket_timeline_slug', _x( 'timeline', 'view slug', 'eventrocket' ) );
		return $this->slug;
	}
	
	public function name() {
		if ( ! empty( $this->name ) ) return $this->name;
		$this->name = apply_filters( 'eventrocket_timeline_name', _x( 'Timeline', 'view name', 'eventrocket' ) );
		return $this->name;
	}

	public function template_class( $class ) {
		if ( ! $this->is_timeline_view() ) return $class;
		return 'EventRocket_Timeline_View';
	}

	/**
	 * Wrapper around tribe_has_previous_events().
	 */
	public function has_previous_page() {
		global $wp_query;

		// Get paged value, force a 0/null value to 1
		$paged = $wp_query->get( 'paged' );
		if ( 0 == $paged) $wp_query->set( 'paged', 1 );
		$result = tribe_has_previous_event();

		// Restore the original value and return the result
		$wp_query->set( 'paged', $paged );
		return $result;
	}

	/**
	 * @todo confusion stemming from custom tribe_paged var overriding expected WP default for paged
	 * @return string
	 */
	public function previous_page_url() {
		global $wp_query;

		$page = absint( $wp_query->get( 'paged' ) );
		$page = ( 0 == $page ) ? 1 : $page;
		$past = tribe_is_past();

		// Go back a page in previous list?
		if ( $past ) {
			$prev_page = trailingslashit( get_timeline_url() ) . 'page/' . ( $page + 1 );
			return add_query_arg( 'tribe_event_display', 'past', $prev_page );
		}

		// Page 1 of the present+future list?
		if ( ! $past && 1 === $page ) {
			return add_query_arg( 'tribe_event_display', 'past', get_timeline_url() );
		}

		// Go back a page in current+future list?
		if ( ! $past && 1 < $page ) {
			return trailingslashit( get_timeline_url() ) . 'page/' . ( $page - 1 );
		}
	}

	/**
	 * @todo confusion stemming from custom tribe_paged var overriding expected WP default for paged
	 * @return string
	 */
	public function next_page_url() {
		global $wp_query;

		$page = absint( $wp_query->get( 'paged' ) );
		$page = ( 0 == $page ) ? 1 : $page;
		$past = tribe_is_past();

		// Page 2+ of the previous events list?
		if ( $past && 1 < $page ) {
			$next_page = trailingslashit( get_timeline_url() ) . 'page/' . ( $page - 1 );
			return add_query_arg( 'tribe_event_display', 'past', $next_page );
		}

		// Page 1 of the previous events list?
		if ( $past && 1 == $page ) {
			return get_timeline_url();
		}

		// Within the present+future list?
		if ( ! $past ) {
			return trailingslashit( get_timeline_url() ) . 'page/' . ( $page + 1 );
		}
	}
}