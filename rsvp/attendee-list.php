<?php
class EventRocket_RSVPAttendeeList
{
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_assets' ) );
		add_action( 'wp_ajax_rsvp_attendance', array( $this, 'listen' ) );
	}

	public function add_assets() {
		global $pagenow, $post;
		if ( 'post.php' !== $pagenow || Tribe__Events__Main::POSTTYPE !== $post->post_type ) return;

		$dependencies = array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-dialog',
			'jquery-ui-tabs'
		);

		wp_enqueue_style( 'eventrocket_rsvp', EVENTROCKET_URL . 'assets/rsvp.css' );
		wp_enqueue_script( 'eventrocket_rsvp', EVENTROCKET_URL . 'assets/rsvp.js', $dependencies, false, true );

		wp_localize_script( 'eventrocket_rsvp', 'eventrocket_rsvp', array(
			'check'               => wp_create_nonce( 'list_attendees_' . get_current_user_id() . $post->ID ),
			'event_id'            => $post->ID,
			'loading_msg'         => $this->dialog_placeholder(),
			'attending_title'     => _x( 'Attending', 'tab title', 'eventrocket' ),
			'not_attending_title' => _x( 'Not Attending', 'tab title', 'eventrocket' ),
			'none_found_text'     => _x( 'No matching responses yet.', 'attendee list', 'eventrocket' ),
			'title'               => esc_attr( _x( 'RSVP Attendee List', 'dialog title', 'eventrocket' ) )
		) );
	}

	protected function dialog_placeholder() {
		$spinner = '<img src="' . get_admin_url( null, '/images/spinner.gif' ) . '" />';
		return '<p class="aligncenter loading">' . $spinner . '<br/>' . __( 'Loading', 'eventrocket' ) .'</p>';
	}

	public function listen() {
		if ( ! wp_verify_nonce( @$_POST['check'], 'list_attendees_' . get_current_user_id() . @$_POST['event_id'] ) )
			exit( json_encode( array( 'msg' => 'failure' ) ) );

		$attendees = eventrocket_rsvp()->attendance( $_POST['event_id'] );

		exit( json_encode( array(
			'msg'           => 'success',
			'attendees'     => $attendees->list_positives(),
			'non_attendees' => $attendees->list_negatives()
		) ) );
	}
}