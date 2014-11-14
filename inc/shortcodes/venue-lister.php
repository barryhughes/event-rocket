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
		$this->params = $params;
		$this->content = $content;
		$this->execute();
	}

	protected function execute() {
		$this->parse();

		if ( ! $this->cache_get() ) {
			$this->query();
			$this->build();
		}
	}

	protected function parse() {

	}

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

	protected function build() {

	}
}