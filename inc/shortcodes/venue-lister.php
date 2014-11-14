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
		$this->set_template();
	}

	protected function query() {
		$this->enter_blog();
		$this->args = array( 'post_type' => TribeEvents::VENUE_POST_TYPE ); // Reset

		$this->args = apply_filters( 'eventrocket_embed_venue_args', $this->args, $this->params );
		$this->results = get_posts( $this->args );
	}
}