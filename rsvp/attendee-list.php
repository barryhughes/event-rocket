<?php
class EventRocket_RSVPAttendeeList
{
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_assets' ) );
		add_action( 'wp_ajax_rsvp_attendance', array( $this, 'listen' ) );
		add_action( 'wp_ajax_rsvp_email', array( $this, 'email' ) );
		add_action( 'eventrocket_dispatch_emails', array( $this, 'send_emails' ), 10, 3 );
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
			'email_title'     	  => _x( 'Email attendees', 'dialog title', 'eventrocket' ),
			'email_subject'       => _x( 'Subject', 'dialog text', 'eventrocket' ),
			'email_body'          => _x( 'Body', 'dialog text', 'eventrocket' ),
			'email_send'          => _x( 'Send', 'dialog text', 'eventrocket' ),
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
			wp_send_json( array( 'msg' => 'failure' ) );

		$attendees = eventrocket_rsvp()->attendance( $_POST['event_id'] );

		wp_send_json( array(
			'msg'           => 'success',
			'attendees'     => $attendees->list_positives(),
			'non_attendees' => $attendees->list_negatives()
		) );
	}

	public function email() {
		if ( ! wp_verify_nonce( @$_POST['check'], 'list_attendees_' . get_current_user_id() . @$_POST['event_id'] ) )
			wp_send_json( array( 'msg' => 'failure' ) );

		$attendees = eventrocket_rsvp()->attendance( $_POST['event_id'] );
		$emails    = $this->build_attendee_email_list_positives( $attendees );
		$subject   = filter_var( $_POST['subject'], FILTER_SANITIZE_STRING );
		$body      = filter_var( $_POST['body'], FILTER_SANITIZE_STRING );
		do_action( 'eventrocket_dispatch_emails', $emails, $subject, $body );

		wp_send_json( array(
			'msg'           => 'success'
		) );
	}

	/**
	 * Dispatch an email to confirmed attendees.
	 *
	 * @param EventRocket_RSVPAttendance $attendees
	 *
	 * @return array
	 */
	public function build_attendee_email_list_positives( EventRocket_RSVPAttendance $attendees ) {
		$emails = array();

		foreach ( $attendees->list_positives( true ) as $attendee ) {
			$email = isset( $attendee->user_email )
				? $attendee->user_email // authenticated users
				: $attendee->identifier; // unauthenticated users

			// Sanity check
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) continue;

			$emails[] = $email;
		}

		return $emails;
	}

	/**
	 * Dispatches emails to one or more addresses.
	 *
	 * This method can be unhooked from the eventrocket_dispatch_emails action and
	 * replaced with an alternative: this makes it possible to send the emails by
	 * different strategies such as a single wp_mail() call with many bcc entries,
	 * if preferred.
	 *
	 * @param array|string $email_addresses
	 * @param string       $subject
	 * @param string       $body
	 */
	public function send_emails( $email_addresses, $subject, $body ) {
		$email_addresses = is_array( $email_addresses )
			? $email_addresses
			: array( $email_addresses );

		foreach ( $email_addresses as $to )
			wp_mail( $to, $subject, $body );
	}
}
