<?php
/**
 * Determines if the user (assumed to be the current user if not specified) is attending
 * the event (defaults to the current event, if set, unless one is specified).
 *
 * @param  int|null $event_id
 * @param  int|null $user_id
 * @return bool
 */
function eventrocket_rsvp_user_is_attending( $event_id = null, $user_id = null ) {
	$event_id = Tribe__Events__Main::instance()->postIdHelper( $event_id );
	$user_id  = ( null !== $user_id ) ? $user_id : get_current_user_id();
	return eventrocket_rsvp()->attendance( $event_id )->is_user_attending( $user_id );
}

/**
 * Determines if the user (assumed to be the current user if not specified) has declined
 * to attend the event (defaults to the current event, if set, unless one is specified).
 *
 * @param  int|null $event_id
 * @param  int|null $user_id
 * @return bool
 */
function eventrocket_rsvp_user_has_declined( $event_id = null, $user_id = null ) {
	$event_id = Tribe__Events__Main::instance()->postIdHelper( $event_id );
	$user_id  = ( null !== $user_id ) ? $user_id : get_current_user_id();
	return eventrocket_rsvp()->attendance( $event_id )->is_user_not_attending( $user_id );
}

