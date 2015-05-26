<?php
class EventRocket_iCalRequests extends EventRocket_GenericDataRequests {
	protected $key = 'event_ical';

	protected function present() {
		global $wp_query;
		$suggested_filename = date_i18n( 'YmdHis-' ) . uniqid() . '.ics';

		header( 'Content-Type: text/calendar' );
		header( 'Content-Disposition: attachment; filename=' . $suggested_filename );

		// Trick TEC into using our post list even within month view
		$wp_query->posts = $this->events;
		add_filter( 'tribe_is_month', '__return_false' );

		Tribe__Events__iCal::generate_ical_feed();
		exit();
	}
}