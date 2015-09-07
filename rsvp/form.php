<?php
class EventRocket_RSVPForm
{
	protected $anon_submission_errors = array();
	protected $anon_sub_accepted = 0;


	public function __construct() {
		add_filter( 'eventrocket_rsvp_accept_anon_submission', array( $this, 'assess_anon_submissions' ) );
		add_action( 'eventrocket_rsvp_anon_submission_form', array( $this, 'anon_submission_errors' ) );
		add_action( 'wp', array( $this, 'listen' ) );
		add_action( 'tribe_events_single_event_after_the_meta', array( $this, 'show_form' ) );
	}

	public function listen() {
		if ( ! isset( $_POST['rsvp_attend'] ) && ! isset( $_POST['rsvp_withdraw'] ) ) return;
		if ( ! wp_verify_nonce( $_POST['eventrocket_rsvp_check'], 'mark_attendance' . get_current_user_id() . get_the_ID() ) ) return;

		if ( is_user_logged_in() ) $this->authed_request();
		else $this->unauthed_request();
	}

	protected function authed_request() {
		$attendance = eventrocket_rsvp()->attendance( @$_POST['eventrocket_rsvp_event'] );

		if ( isset( $_POST['rsvp_attend'] ) )
			$attendance->set_to_attend( get_current_user_id() );

		if ( isset( $_POST['rsvp_withdraw'] ) )
			$attendance->set_to_not_attend( get_current_user_id() );
	}

	protected function unauthed_request() {
		if ( ! isset( $_POST['eventrocket_anon_id'] ) ) return;
		if ( ! apply_filters( 'eventrocket_rsvp_accept_anon_submission', false ) ) return;

		$attendance = eventrocket_rsvp()->attendance( @$_POST['eventrocket_rsvp_event'] );
		$attendance->set_anon_to_attend( $_POST['eventrocket_anon_id'] );

		$this->anon_sub_accepted = get_the_ID();
	}

	public function assess_anon_submissions() {
		$event_id = get_the_ID();

		if ( get_post_meta( $event_id, EventRocket_RSVPManager::RESTRICT_RSVP, true ) ) {
			$this->anon_submission_errors[ $event_id ][] = __( 'This event is not open to anonymous RSVP submissions', 'event-rocket' );
		}

		if ( ! filter_var( $_POST['eventrocket_anon_id'], FILTER_VALIDATE_EMAIL ) ) {
			$this->anon_submission_errors[ $event_id ][] = __( 'You must provide a valid email address!', 'event-rocket' );
		}

		if ( count( $this->anon_submission_errors[ $event_id ] ) > 0 ) return false;
		return true;
	}

	public function	anon_submission_errors() {
		$event_id = get_the_ID();
		if ( ! isset( $this->anon_submission_errors[ $event_id ] ) || 0 === count( $this->anon_submission_errors[ $event_id ] ) ) return;

		$error_txt = '<ul class="errors">';

		foreach ( $this->anon_submission_errors[ $event_id ] as $error )
			$error_txt = "<li> $error </li>";

		echo "$error_txt </ul>";
	}

	public function show_form() {
		// Locate the template, allow for The Events Calendar style overrides ... but don't
		// trust the returned filepath: at least as of TEC 3.8.x a non-existent path may be returned
		$template = Tribe__Events__Templates::getTemplateHierarchy( 'rsvp-form', array( 'disable_view_check' => true ) );
		if ( ! $template || ! file_exists( $template ) ) $template = EVENTROCKET_INC . '/templates/rsvp-form.php';

		// Load our settings
		$enabled        = get_post_meta( get_the_ID(), EventRocket_RSVPManager::ENABLE_RSVP, true );
		$restricted     = get_post_meta( get_the_ID(), EventRocket_RSVPManager::RESTRICT_RSVP, true );
		$limited 	    = get_post_meta( get_the_ID(), EventRocket_RSVPManager::LIMIT_RSVP, true );
		$show_attendees = get_post_meta( get_the_ID(), EventRocket_RSVPManager::SHOW_ATTENDEES_RSVP, true );

		$attendance    = eventrocket_rsvp()->attendance();
		$anon_accepted = ( get_the_ID() === $this->anon_sub_accepted );
		$attendees     = eventrocket_rsvp()->attendance( get_the_ID() );

		include $template;
	}
}
