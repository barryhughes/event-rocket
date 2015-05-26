<?php
/**
 * @param  array  $args
 * @param  string $content
 * @return string
 */
function eventrocket_csv_link( $args, $content ) {
	return eventrocket_data_link( 'event_csv', $args, $content );
}

/**
 * @param  array  $args
 * @param  string $content
 * @return string
 */
function eventrocket_ical_link( $args, $content ) {
	return eventrocket_data_link( 'event_ical', $args, $content );
}

/**
 * @param  array  $args
 * @param  string $content
 * @return string
 */
function eventrocket_data_link( $key, $args, $content ) {
	$args = array_intersect_key( $args, array_flip( array(
		'event', 'from', 'to', 'category', 'categories', 'tag', 'tags', 'limit'
	) ) );

	$args[$key] = true;
	$query = http_build_query( array_map( 'urlencode', $args ) );
	$url   = trailingslashit( home_url() ) . "?$query";

	return '<a href="' . esc_url( $url ) . '" target="_blank">' . $content . '</a>';
}


/**
 * Example:
 *
 *   [eventrocket_csv_link from="today" to="+1 week"] Download in CSV Format! [/eventrocket_csv_link]
 *
 * Creates a link allowing users to download 1 week of upcoming event data in CSV format.
 */
add_shortcode( 'eventrocket_csv_link', 'eventrocket_csv_link' );

/**
 * Example:
 *
 *   [eventrocket_ical_link from="today" to="+1 week"] Download in iCal Format! [/eventrocket_ical_link]
 *
 * Creates a link allowing users to download 1 week of upcoming event data in iCal format.
 */
	add_shortcode( 'eventrocket_ical_link', 'eventrocket_ical_link' );