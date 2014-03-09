<?php
/**
 * Facilitates a means of positioning the main events page on the blog front page.
 */
class EventRocketNosecone
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
		if ( self::FAKE_POST_ID === (int) get_option( 'page_on_front', 0 ) )
			add_action( 'parse_query', array( $this, 'parse_query' ), 5 );
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
		$query->set( 'post_type', TribeEvents::POSTTYPE );
		$query->set( 'eventDisplay', 'default' );

		return $query;
	}
}

// Nosecone is an experiment to facilitate frontpage support for events
new EventRocketNosecone;