<?php
class EventRocket_VenueFinder
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
}