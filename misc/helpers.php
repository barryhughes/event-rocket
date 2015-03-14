<?php
/**
 * Determines if the current event (or optionally specified event) has been
 * tagged with the specified tag.
 *
 * @param  mixed $tag
 * @param  null  $event_id
 * @return bool
 */
function event_is_tagged( $tag, $event_id = null ) {
	$event_id = Tribe__Events__Events::postIdHelper( $event_id );

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
	$event_id = Tribe__Events__Events::postIdHelper( $event_id );

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