<?php
/**
 * Facilitates a means of positioning the main events page on the blog front page.
 *
 * INCOMPLETE! This is a work-in-progress.
 */
class EventRocketNosecone
{
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_frontpage_option' ) );
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
		if ( false === strpos( $html, "name='page_on_front'" ) ) return $html;
		$new_option = '<option value="main-events">' . __( 'Main events page', 'event-rocket' ) . '</option>';
		return str_replace( '</select>', $new_option . '</select>', $html);
	}
}

// Nosecone is an experiment to facilitate frontpage support for events
new EventRocketNosecone;