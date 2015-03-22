<?php
class EventRocket_Timeline {
	protected $name = '';
	protected $slug = '';


	public function __construct() {
		add_action( 'generate_rewrite_rules', array( $this, 'routes' ) );
		add_action( 'pre_get_posts',          array( $this, 'adapter' ) );
		add_filter( 'tribe-events-bar-views', array( $this, 'selector' ) );
		add_filter( 'tribe_events_current_template_class', array( $this, 'template_class' ) );
		add_filter( 'tribe_events_current_view_template',  array( $this, 'template' ) );
	}

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

	public function adapter( $query ) {
		if ( 'timeline' !== $query->get( 'eventDisplay' ) ) return;
		$query->set( 'eventDisplay', 'list' );
		$query->set( 'eventrocket_view', 'timeline' );
		add_action( 'wp_head', array( $this, 'set_displaying' ) );
	}

	public function set_displaying() {
		$tec = Tribe__Events__Events::instance();
		$tec->displaying = $this->slug();
		remove_action( 'parse_query', array( $tec, 'setDisplay' ), 51, 0 );
	}

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

	public function template( $template ) {
		if ( ! $this->is_timeline_view() ) return $template;
		return eventrocket_template( 'timeline/list' );
	}

	public function template_class( $class ) {
		if ( ! $this->is_timeline_view() ) return $class;
		return 'EventRocket_Timeline_View';
	}
}