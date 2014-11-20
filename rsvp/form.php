<?php
class EventRocket_RSVPForm
{
	public function __construct() {
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

	}

	public function show_form() {
		// Locate the template, allow for The Events Calendar style overrides ... but don't
		// trust the returned filepath: at least as of TEC 3.8.x a non-existent path may be returned
		$template = TribeEventsTemplates::getTemplateHierarchy( 'rsvp-form', array( 'disable_view_check' => true ) );
		if ( ! $template || ! file_exists( $template ) ) $template = EVENTROCKET_INC . '/templates/rsvp-form.php';

		// Load our settings
		$enabled    = get_post_meta( get_the_ID(), EventRocket_RSVPManager::ENABLE_RSVP, true );
		$restricted = get_post_meta( get_the_ID(), EventRocket_RSVPManager::RESTRICT_RSVP, true );
		$attendance = eventrocket_rsvp()->attendance();

		include $template;
	}
}