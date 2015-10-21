<?php
abstract class EventRocket_ObjectLister
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
	protected $template = '';

	// Nothing found fallbacks
	protected $nothing_found_text = '';
	protected $nothing_found_template = '';

	// Caching
	protected $cache_key_html = '';
	protected $cache_key_data = '';
	protected $cache_expiry   = 0;

	// Other conditions
	protected $limit  = 20;
	protected $offset = 0;
	protected $page   = 0; // Relates to the "paged" query var
	protected $author = -1;

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
	abstract protected function get_inline_parser();

	/**
	 * Inspect the properties array for values assigned with any of the provided keys:
	 * combines and extracts the values, returning them as an array (may be empty if
	 * for instance no such shortcode params were supplied).
	 *
	 * @return array
	 */
	protected function prop_from_csv() {
		$accumulator = '';

		foreach ( func_get_args() as $key )
			$accumulator .= isset( $this->params[$key] ) ? $this->params[$key] . ',' : '';

		$values = explode( ',', $accumulator );
		$result_set = array();

		foreach ( $values as $value ) {
			$value = trim( $value );
			if ( ! empty($value) && ! in_array( $value, $result_set ) ) $result_set[] = $value;
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
	 * Determines if the output should be cached.
	 */
	protected function set_cache() {
		// Has a cache param been set?
		$cache = isset( $this->params['cache'] ) ? $this->params['cache'] : null;
		$cache = apply_filters( 'eventrocket_embeded_posts_cache_expiry', $cache, $this->params );

		// No caching? Bail
		if ( null === $cache ) return;

		// Cache for the default period?
		if ( 'auto' === strtolower( $cache ) || 'on' === strtolower( $cache ) )
			$this->cache_expiry = (int) apply_filters( 'eventrocket_embedded_posts_cache_default_value', HOUR_IN_SECONDS * 2 );

		// Cache for a specified amount of time?
		elseif ( is_numeric( $cache ) && $cache == absint( $cache ) )
			$this->cache_expiry = absint( $cache );

		// Create the cache keys
		$this->cache_key_data = 'EREmbedData' . hash( 'md5', join( '|', $this->params ) );
		$this->cache_key_html = 'EREmbedHtml' . hash( 'md5', join( '|', $this->params ) );
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

		$this->build_header();
		$this->build_each_item();
		$this->build_footer();

		$this->output = ob_get_clean();
		$this->output = apply_filters( 'eventrocket_embed_post_output', $this->output );
		if ( $this->cache_expiry && $this->cache_key_html ) $this->cache_store();
	}

	/**
	 * Builds the output where no results were returned.
	 */
	protected function build_no_results() {
		if ( ! empty( $this->nothing_found_text ) )
			$this->output = apply_filters( 'eventrocket_embed_post_output', $this->nothing_found_text );

		elseif ( ! empty( $this->nothing_found_template ) ) {
			ob_start();
			include $this->nothing_found_template;
			$this->output = ob_get_clean();
			$this->output = apply_filters( 'eventrocket_embed_post_output', $this->output );
		}
	}

	protected function build_header() {
		$header = apply_filters( 'eventrocket_embed_post_header', '', $this->params, $this->results );
		$this->output = $header . $this->output;
	}

	protected function build_each_item() {
		foreach ( $this->results as $this->event_post )
			$this->build_item();
	}

	protected function build_footer() {
		$footer = apply_filters( 'eventrocket_embed_post_footer', '', $this->params, $this->results );
		$this->output .= $footer;
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

		echo apply_filters( 'eventrocket_embed_post_single_output', ob_get_clean(), get_the_ID() );
		wp_reset_postdata();
	}

	protected function build_inline_output() {
		static $parser = null;

		if ( null === $parser ) $parser = $this->get_inline_parser();
		if ( ! $parser instanceof EventRocket_iInlineParser ) return '';

		$parser->process( $this->content );
		print do_shortcode( $parser->output() );
	}

	/**
	 * Set the template to use.
	 *
	 * The template can live in the core The Events Calendar views directory, or else in the
	 * theme/child theme, or can be an absolute path.
	 */
	protected function set_template() {
		// If there is no template and no inner content, assume the regular single event template
		if ( ! isset( $this->params['template'] ) && empty( $this->content ) ) $this->template = $this->fallback;
		elseif ( ! isset( $this->params['template'] ) ) return;

		// If not an absolute filepath use Tribe's template finder
		if ( isset( $this->params['template'] ) && 0 !== strpos( $this->params['template'], '/' ) )
			$this->template = eventrocket_template( $this->params['template'] );

		// Ensure the template exists
		if ( ! $this->template && file_exists( $this->params['template'] ) )
			$this->template = $this->params['template'];
	}

	/**
	 * Set the number of posts to retreive.
	 */
	protected function set_limit() {
		$this->limit = isset( $this->params['limit'] )
			? (int) $this->params['limit']
			: (int) get_option( 'posts_per_page', 20 );
	}

	/**
	 * Set the page.
	 */
	protected function set_page() {
		global $wp_query;

		if ( ! isset( $this->params['page'] ) ) return;

		if ( 'auto' === strtolower( $this->params['page'] ) && $wp_query->get( 'paged' ) )
			$this->page = (int) $wp_query->get( 'paged' );

		if ( (int) $this->params['page'] > 1 )
			$this->page = (int) $this->params['page'];
	}

	/**
	 * Set the offset: should not ordinarily be used in concert with the page param.
	 */
	protected function set_offset() {
		if ( ! isset( $this->params['offset'] ) ) return;
		$this->offset = (int) $this->params['offset'];
	}

	/**
	 * Set the author ID.
	 */
	protected function set_author() {
		$this->author = isset( $this->params['author'] ) ? (int) $this->params['author'] : -1;
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
			$this->nothing_found_template = Tribe__Events__Templates::getTemplateHierarchy( $this->params['nothing_found_template'] );

		// Ensure the template exists
		if ( ! $this->nothing_found_template && file_exists( $this->params['nothing_found_template'] ) )
			$this->nothing_found_template = $this->params['nothing_found_template'];
	}

	protected function args_limit() {
		$this->args['posts_per_page'] = $this->limit;
		if ( $this->page > 0 )    $this->args['paged'] = $this->page;
		if ( $this->offset != 0 ) $this->args['offset'] = $this->offset;
	}

	protected function args_author() {
		if ( $this->author > 0 ) $this->args['author'] = $this->author;
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
	 * Evaluates the parameter and assesses if it means "on" or is a positive indication.
	 * For instance, by default, "1", "yes" and "on" would eval as positive.
	 *
	 * @param  $param
	 * @return bool
	 */
	protected function is_on( $param ) {
		return eventrocket_yes( $param );
	}

	/**
	 * Moves any values in $list prefixed with a negative operator ("-") to the
	 * ignore list.
	 *
	 * @param array $list
	 * @param array $ignore_list
	 */
	protected function move_ignore_vals( array &$list, array &$ignore_list ) {
		$keep_list = array();

		foreach ( $list as $value ) {
			if ( 0 === strpos( $value, '-') ) $ignore_list[] = substr( $value, 1 );
			else $keep_list[] = $value;
		}

		$list = $keep_list;
	}

	/**
	 * Process the list of posts, turning any slugs into IDs.
	 *
	 * @param $list
	 * @param string $type
	 */
	protected function parse_post_refs( &$list, $type = Tribe__Events__Main::POSTTYPE ) {
		foreach ( $list as $index => $reference ) {
			$this->typify( $reference );
			if ( ! is_string( $reference ) ) continue;

			$event = $this->load_post_by_slug( $reference, $type );

			if ( $event ) $list[$index] = $event->ID;
		}
	}

	/**
	 * @param  $slug
	 * @param  $post_type
	 * @return array
	 */
	protected function load_post_by_slug( $slug, $post_type ) {
		$event_set = get_posts( array(
			'name'           => $slug,
			'post_type'      => $post_type,
			'eventDisplay'   => 'custom',
			'posts_per_page' => 1
		) );

		if ( empty( $event_set ) ) return false;
		return current( $event_set );
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
	 *
	 * It could also be called as one of the final statements within query(),
	 * however that would preclude template tags etc from running within the same
	 * context which or may not be an issue depending on the specific use case.
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
		return $this->results;
	}
}