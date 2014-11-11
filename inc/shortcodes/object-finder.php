<?php
abstract class EventRocket_ObjectFinder
{
	// Inputs
	protected $params = array();
	protected $content = '';

	// Internal
	protected $output  = '';
	protected $results = array();

	// Caching
	protected $cache_key_html = '';
	protected $cache_key_data = '';
	protected $cache_expiry   = 0;


	public function __construct( array $params, $content ) {
		$this->params  = $params;
		$this->content = $content;
		$this->execute();
	}

	/**
	 * Parse the provided parameters, run the resulting query and build the output.
	 * Allows for retrieval of cached results where appropriate.
	 */
	protected function execute() {
		$this->parse();

		if ( ! $this->cache_get() ) {
			$this->query();
			$this->build();
		}
	}

	abstract protected function parse();
	abstract protected function query();
	abstract protected function build();

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
	 * Stores the generated output in the cache.
	 */
	protected function cache_store() {
		set_transient( $this->cache_key_html, $this->output, $this->cache_expiry );
		set_transient( $this->cache_key_data, $this->results, $this->cache_expiry );
	}

	/**
	 * @return bool
	 */
	protected function cache_get() {
		if ( ! $this->cache_expiry ) return false;

		$cached_output = get_transient( $this->cache_key_html );
		$cached_data   = get_transient( $this->cache_key_data );
		if ( ! $cached_output || ! $cached_data ) return false;

		$this->output  = $cached_output;
		$this->results = $cached_data;
		return true;
	}
}