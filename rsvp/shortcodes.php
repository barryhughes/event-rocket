<?php
add_shortcode( 'eventrocket_user_rsvps', 'eventrocket_rsvp_shortcode' );

function eventrocket_rsvp_shortcode( $args ) {
	// Default args
	$args = shortcode_atts( array(
		'user'           => get_current_user_id(),
		'attendances'   => true,
		'declines'      => true,
		'only_upcoming' => true,
	), $args );

	// Grab the user object (bail if it does not exist)
	$user = get_user_by( 'id', absint( $args['user'] ) );
	if ( ! $user ) return;
	else $user = new EventRocket_RSVPUser( $user->ID );

	$upcoming  = eventrocket_yes( $args['only_upcoming'] );
	$attending = eventrocket_yes( $args['attendances'] ) ? $user->confirmed_attendances( $upcoming ) : array();
	$declines  = eventrocket_yes( $args['declines'] ) ? $user->confirmed_non_attendances( $upcoming ) : array();

	ob_start();
	eventrocket_get_template( 'rsvp-user-attendance', array(
		'attending' => $attending,
		'declines'  => $declines
	) );
	return ob_get_clean();
}