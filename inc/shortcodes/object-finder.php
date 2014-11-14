<?php
abstract class EventRocket_ObjectFinder
{
	// Inputs
	protected $params = array();
	protected $content = '';

	// Internal
	protected $blog = false;
	protected $output  = '';
	protected $results = array();
	protected $args = array();
	protected $event_post;
	protected $fallback = '';

	// Nothing found fallbacks
	protected $nothing_found_text = '';
	protected $nothing_found_template = '';

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

	/**
	 * Take the query result set and build the actual output.
	 */
	protected function build() {
		if ( ! empty( $this->results ) ) $this->build_normal();
		else $this->build_no_results();
		$this->exit_blog();
	}

	/**
	 * Builds the output when we have some results from the query.
	 */
	protected function build_normal() {
		ob_start();
		foreach ( $this->results as $this->event_post ) $this->build_item();
		$this->output = ob_get_clean();
		$this->output = apply_filters( 'eventrocket_embed_event_output', $this->output );
		if ( $this->cache_expiry && $this->cache_key_html ) $this->cache_store();
	}

	/**
	 * Builds the output where no results were returned.
	 */
	protected function build_no_results() {
		if ( ! empty( $this->nothing_found_text ) )
			$this->output = apply_filters( 'eventrocket_embed_event_output', $this->nothing_found_text );

		elseif ( ! empty( $this->nothing_found_template ) ) {
			ob_start();
			include $this->nothing_found_template;
			$this->output = ob_get_clean();
			$this->output = apply_filters( 'eventrocket_embed_event_output', $this->output );
		}
	}

	/**
	 * Decide whether to pull in a template to render each event or to use
	 * an inline template.
	 */
	protected function build_item() {
		if ( ! is_a( $this->event_post, 'WP_Post' ) ) return;
		$GLOBALS['post'] = $this->event_post;
		setup_postdata( $GLOBALS['post'] );
		ob_start();

		if ( ! empty( $this->template ) ) include $this->template;
		elseif ( ! empty( $this->content ) ) $this->build_inline_output();

		echo apply_filters( 'eventrocket_embed_event_single_output', ob_get_clean(), get_the_ID() );
		wp_reset_postdata();
	}

	protected function build_inline_output() {
		static $parser = null;
		if ( null === $parser ) $parser = new EventRocket_EmbeddedEventTemplateParser;
		$parser->process( $this->content );
		print do_shortcode( $parser->output );
	}

	/**
	 * Set the template to use.
	 *
	 * The template can live in the core The Events Calendar views directory, or else in the
	 * theme/child theme, or can be an absolute path.
	 */
	protected function set_template() {
		$this->template = ''; // Wipe clean
		$fallback = $this->fallback;

		// If there is no template and no inner content, assume the regular single event template
		if (!isset($this->params['template']) && empty($this->content)) $this->template = $fallback;
		elseif (!isset($this->params['template'])) return;

		// If not an absolute filepath use Tribe's template finder
		if (isset($this->params['template']) && 0 !== strpos($this->params['template'], '/'))
			$this->template = TribeEventsTemplates::getTemplateHierarchy($this->params['template']);

		// Ensure the template exists
		if (!$this->template && file_exists($this->params['template']))
			$this->template = $this->params['template'];
	}

	/**
	 * Set the message to display - or template to pull in - should no results be found.
	 */
	protected function set_fallbacks() {
		// Has a (usually short) piece of text been provided, ie "Nothing found"?
		if ( isset( $this->params['nothing_found_text'] ) && is_string( $this->params['nothing_found_text'] ) )
			$this->nothing_found_text = $this->params['nothing_found_text'];

		// Has a template path been provided?
		if ( ! isset( $this->params['nothing_found_template'] ) ) return;

		// If not an absolute filepath use Tribe's template finder
		if ( isset( $this->params['nothing_found_template'] ) && 0 !== strpos( $this->params['nothing_found_template'], '/' ) )
			$this->nothing_found_template = TribeEventsTemplates::getTemplateHierarchy( $this->params['nothing_found_template'] );

		// Ensure the template exists
		if ( ! $this->nothing_found_template && file_exists( $this->params['nothing_found_template'] ) )
			$this->nothing_found_template = $this->params['nothing_found_template'];
	}

	/**
	 * Forces numeric values to ints and anything else to strings.
	 *
	 * @param $value
	 */
	protected function typify( &$value ) {
		$value = is_numeric( $value ) ? (int) $value : (string) $value;
	}

	/**
	 * Tests to see if a specific blog has been requested. Expected to be called
	 * during argument parsing (ie, when parse() runs).
	 */
	protected function set_blog() {
		if ( ! isset( $this->params['blog'] ) ) return;
		$this->blog = $this->params['blog'];
	}

	/**
	 * Switch to a different blog if required. Expected to be called when query()
	 * runs.
	 */
	protected function enter_blog() {
		if ( ! $this->blog ) return;
		switch_to_blog( $this->blog );
	}

	/**
	 * Restores the current blog, if necessary. Called at the end of build().
	 */
	protected function exit_blog() {
		if ( ! $this->blog ) return;
		restore_current_blog();
		$this->blog = false;
	}

	/**
	 * @return string
	 */
	public function output() {
		return (string) $this->output;
	}

	/**
	 * @return array
	 */
	public function results() {
		return (string) $this->output;
	}
}