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

/**
 * Returns a list of upcoming events the specified user has confirmed attendance for (if
 * no user is specified, it defaults to the currently logged in user).
 *
 * @param  int   $user_id
 * @return array
 */
function eventrocket_rsvp_all_upcoming_attendances( $user_id = 0 ) {
	$user = new EventRocket_RSVPUser( $user_id );
	return $user->confirmed_attendances();
}

/**
 * Returns a list of upcoming events the specified user has confirmed they will not
 * be in attendance for (if no user is specified, it defaults to the currently logged
 * in user).
 *
 * @param  int   $user_id
 * @return array
 */
function eventrocket_rsvp_all_upcoming_refusals( $user_id = 0 ) {
	$user = new EventRocket_RSVPUser( $user_id );
	return $user->confirmed_non_attendances();
}

/**
 * Evaluates $value and determines if it represents a positive or negative.
 *
 * @param  mixed $value
 * @return boolean
 */
function eventrocket_yes( $value ) {
	$pos_terms = apply_filters( 'eventrocket_positive_strs', array( 'on', 'true', 'yes', 'y' ) );

	if ( in_array( strtolower( $value ), $pos_terms ) ) return true;
	if ( is_numeric( $value ) && $value ) return true;
	if ( is_bool( $value ) ) return $value;

	return false;
}