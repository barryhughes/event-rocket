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

	$args['check'] = hash( 'md5', join( '|', $args ) . eventrocket_data_security_token() );
	$args[$key] = true;

	$query = http_build_query( array_map( 'urlencode', $args ) );
	$url   = trailingslashit( home_url() ) . "?$query";

	return '<a href="' . esc_url( $url ) . '" target="_blank">' . $content . '</a>';
}

/**
 * Returns a unique-to-this-site token.
 *
 * @return string
 */
function eventrocket_data_security_token() {
	$token = get_option( 'eventrocket_data_token', false );

	if ( false === $token ) {
		$token = hash( 'md5', date( 'YmdHisU' ) . uniqid( get_site_url() ) );
		update_option( 'eventrocket_data_token', $token );
	}

	return $token;
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