<?php
defined( 'ABSPATH' ) or exit();


/**
 * Facilitates a means of positioning the main events page on the blog front page.
 */
class EventRocket_FrontPageEvents
{
	/**
	 * Our fake post ID used to indicate that the main events page should be
	 * accessible via the frontpage.
	 *
	 * We'll use 2^30 for this value since we can reasonably expect the majority
	 * of blogs to not have exceeded one billion rows in the posts table. Use of
	 * a negative or unique string value would in many ways be preferable but is
	 * inhibited by sanitize_option() which runs the page_on_front option through
	 * absint() before committing it to the database.
	 */
	const FAKE_POST_ID = 1073741824;


	/**
	 * Set things in motion to allow the main events page to be set as front page,
	 * and to force that setting to be respected.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_frontpage_option' ) );
		$this->events_on_front();
	}

	/**
	 * Add the events page as a frontpage option.
	 */
	public function add_frontpage_option() {
		global $pagenow;
		if ( 'options-reading.php' !== $pagenow ) return;
		add_filter( 'wp_dropdown_pages', array( $this, 'add_custom_option' ) );
	}

	/**
	 * Takes the HTML from the wp_dropdown_pages() function and wedges in our new option,
	 * if appropriate to do so. Not uber-graceful, but this is a rocket-themed plugin and
	 * sometimes it's just about blasting into space and to hell with the consequences.
	 *
	 * @param $html
	 * @return string
	 */
	public function add_custom_option( $html ) {
		// We only want to meddle with the page_on_front options
		if ( false === strpos( $html, "name='page_on_front'" ) ) return $html;

		// Form our custom page_on_front option
		$selected = self::FAKE_POST_ID === (int) get_option( 'page_on_front', 0 ) ? ' selected="selected"' : '';
		$new_option = '<option value="' . self::FAKE_POST_ID . '"' . $selected . '>' . __( 'Main events page', 'event-rocket' ) . '</option>';

		// Insert and return
		return str_replace( '</select>', $new_option . '</select>', $html);
	}

	/**
	 * If the front page is set to show events, listen out for and intercept the main query.
	 */
	protected function events_on_front() {
		if ( self::FAKE_POST_ID !== (int) get_option( 'page_on_front', 0 ) ) return;
		add_action( 'parse_query', array( $this, 'parse_query' ), 5 );
		add_filter( 'tribe_events_getLink', array( $this, 'main_event_page_links' ) );
		add_filter( 'tribe_events_current_view_template', array( $this, 'list_view_helper' ) );
	}

	/**
	 * Inspect and possibly adapt the main query in order to force the main events page to the
	 * front of the house.
	 *
	 * @param $query
	 * @return WP_Query
	 */
	public function parse_query( WP_Query $query ) {
		// Is this the main query / is it trying to find our front events page? If not, don't interfere
		$events_on_front = self::FAKE_POST_ID === (int) $query->get( 'page_id' );
		if ( ! $events_on_front || ! $query->is_main_query() ) return $query;

		// We don't need this to run again after this point
		remove_action( 'parse_query', array( $this, 'parse_query' ), 25 );

		// Let's set the relevant flags in order to cause the main events page to show
		$query->set( 'page_id', 0 );
		$query->set( 'post_type', Tribe__Events__Main::POSTTYPE );
		$query->set( 'eventDisplay', 'default' );
		$query->set( 'eventrocket_frontpage', true );

		// Some extra tricks required to help avoid problems when the default view is list view
		$query->is_page = false;
		$query->is_singular = false;

		return $query;
	}

	/**
	 * Where TEC generates a link to the nominal main events page replace it with a link to the
	 * front page instead.
	 *
	 * We'll only do this if pretty permalinks are in use: those sites aren't cool enough to ride
	 * rockets.
	 *
	 * @param string $url
	 * @return string
	 */
	public function main_event_page_links( $url ) {
		// Capture the main events URL and break it into its consituent pieces for future comparison
		static $event_url;
		static $baseline = array();

		if ( ! isset( $event_url ) ) {
			$event_url = $this->get_main_events_url();
			$baseline = parse_url( $event_url );
		}

		// Don't interfere if we're using ugly permalinks
		if ( '' === get_option( 'permalink_structure' ) ) return $url;

		// Break apart the requested URL
		$current = parse_url( $url );

		// If the URLs can't be inspected then bail
		if ( false === $baseline || false === $current ) return $url;

		// If this is not a request for the main events URL, bail
		if ( $baseline['path'] !== $current['path'] || $baseline['host'] !== $current['host'] ) return $url;

		// Reform the query
		$query = ! empty( $current['query'] ) ? '?' . $current['query'] : '';
		return home_url() . $query;
	}

	/**
	 * Supplies the nominal main events page URL (ie, if it was not positioned on the front page
	 * by Event Rocket).
	 *
	 * @return string
	 */
	protected function get_main_events_url() {
		$tribe_events = Tribe__Events__Main::instance();

		if ( false !== strpos( get_option( 'permalink_structure' ), 'index.php' ) )
			return trailingslashit( home_url() . '/index.php/' . sanitize_title( $tribe_events->getOption( 'eventsSlug', 'events' ) ) );

		else return trailingslashit( home_url() . '/' . sanitize_title( $tribe_events->getOption( 'eventsSlug', 'events' ) ) );
	}

	/**
	 * Help to ensure the list view works on the front page (when it is set to be the
	 * default view).
	 *
	 * @param $template
	 * @return string
	 */
	public function list_view_helper( $template ) {
		global $wp_query;

		// Determine if it's appropriate to interfere
		$events_frontpage = $wp_query->get( 'eventrocket_frontpage' );
		$is_list = tribe_is_list_view();
		$single_template_chosen = ( false !== strpos( $template, 'single-event.php' ) );

		// Bow out gracefully if we're not needed here
		if ( ! ( $events_frontpage && $is_list && $single_template_chosen )	) return $template;

		// Otherwise, try to enforce use of the list view template
		return Tribe__Events__Templates::getTemplateHierarchy( 'list', array( 'disable_view_check' => true ) );
	}
}

// Nosecone is an experiment to facilitate frontpage support for events
new EventRocket_FrontPageEvents;