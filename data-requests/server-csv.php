<?php
class EventRocket_CSVRequests extends EventRocket_GenericDataRequests {
	protected $key = 'event_csv';

	protected function present() {
		header( 'Content-Type: text/csv' );
		eventrocket_get_template( 'data-csv', array( 'events' => $this->events ) );
		exit();
	}
}