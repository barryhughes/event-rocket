<?php
class EventRocket_RSVPForm
{
	public function __construct() {
		add_action( 'tribe_events_single_event_after_the_meta', array( $this, 'show_form' ) );
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