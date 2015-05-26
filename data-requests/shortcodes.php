<?php
/**
 * @param  array  $args
 * @param  string $content
 * @return string
 */
function eventrocket_csv_link( $args, $content ) {
	$args = array_intersect_key( $args, array_flip( array(
		'event', 'from', 'to', 'category', 'categories', 'tag', 'tags', 'limit'
	) ) );

	$args['event_csv'] = true;
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