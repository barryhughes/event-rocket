<?php
/**
 * Loads and displays the specified template using the same rules as eventrocket_template.
 *
 * If the optional $vars are provided those are extracted into the symbol table of the
 * included template.
 *
 * @param string $template
 * @param array  $vars
 */
function eventrocket_get_template( $template, array $vars = array() ) {
	$path = eventrocket_template( $template );
	if ( empty( $path ) ) return;

	extract( $vars );
	include $path;
}

/**
 * @param  string $template
 * @return string absolute filepath - may be empty if template could not be located
 */
function eventrocket_template( $template ) {
	if ( false === strpos( $template, '.php' ) ) $template .= '.php';

	// Look in the theme (+child theme) tribe-events directories first
	$path = locate_template( 'tribe-events' . DIRECTORY_SEPARATOR . $template );

	// Fallback on the plugin's default template
	$path = empty( $path )
		? EVENTROCKET_INC . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template
		: $path;

	// Test existence and return
	return file_exists( $path ) ? $path : '';
}

/**
 * Determines if the current event (or optionally specified event) has been
 * tagged with the specified tag.
 *
 * @param  mixed $tag
 * @param  null  $event_id
 * @return bool
 */
function event_is_tagged( $tag, $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );

	foreach ( wp_get_post_terms( $event_id ) as $term ) {
		if ( is_int( $tag ) && $tag === $term->term_id ) return true;
		if ( $tag === $term->name || $tag === $term->slug ) return true;
	}

	return false;
}

/**
 * Returns the next event after the current or specified one that shares the
 * specified tag, else returns boolean false if none can be found.
 *
 * @param  mixed $tag
 * @param  int   $event_id
 * @return mixed bool|WP_Post
 */
function next_tagged_event( $tag, $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );

	$current_event = get_post( $event_id );
	if ( null === $current_event ) return false;

	$current_date = tribe_get_start_date( $current_event->ID, false, 'Y-m-d H:i:s' );
	$later = date( 'Y-m-d H:i:s', strtotime( $current_date ) + 1 );

	$next_event = event_embed()->obtain( array(
		'tag'   => $tag,
		'event' => 0 - $current_event->ID,
		'start' => $later,
		'limit' => 1
	) );

	if ( empty( $next_event ) ) return false;
	return array_shift( $next_event );
}

function is_timeline_view() {
	return eventrocket()->timeline->is_timeline_view();
}

/**
 * @todo   support specifying the date, category and tag and support default permalinks
 * @param  array $properties
 * @return string
 */
function get_timeline_url( array $properties = array() ){
	$tec = Tribe__Events__Main::instance();
	$url = get_site_url() . '/' . $tec->rewriteSlug . '/' . trailingslashit( eventrocket()->timeline->slug() );
	return $url;
}

/**
 * @return bool
 */
function timeline_has_previous_page() {
	return eventrocket()->timeline->has_previous_page();
}

/**
 * Returns a URL pointing to the previous page of results for timeline view.
 *
 * @return string
 */
function get_timeline_prev_page_url() {
	return eventrocket()->timeline->previous_page_url();
}

/**
 * @return bool
 */
function timeline_has_next_page() {
	return tribe_has_next_event();
}

/**
 * Returns a URL pointing to the next page of results for timeline view.
 *
 * @return string
 */
function get_timeline_next_page_url() {
	return eventrocket()->timeline->next_page_url();
}