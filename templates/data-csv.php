<?php
/**
 * @var array $events
 */

$columns = array(
	_x( 'ID', 'csv-data', 'event-rocket' ),
	_x( 'Title', 'csv-data', 'event-rocket' ),
	_x( 'Description', 'csv-data', 'event-rocket' ),
	_x( 'Start Date', 'csv-data', 'event-rocket' ),
	_x( 'Start Time', 'csv-data', 'event-rocket' ),
	_x( 'End Date', 'csv-data', 'event-rocket' ),
	_x( 'End Time', 'csv-data', 'event-rocket' ),
	_x( 'Venue', 'csv-data', 'event-rocket' ),
	_x( 'Organizer', 'csv-data', 'event-rocket' ),
	_x( 'All Day', 'csv-data', 'event-rocket' ),
	_x( 'Recurring', 'csv-data', 'event-rocket' )
);

echo join( ', ', $columns ) . "\n";

foreach ( $events as $event ) {
	$fields = array(
		absint( $event->ID ),
		get_the_title( $event ),
		$event->post_content,
		tribe_get_start_date( $event->ID, false, 'Y-m-d' ),
		tribe_get_start_date( $event->ID, false, 'H:i:s' ),
		tribe_get_end_date( $event->ID, false, 'Y-m-d' ),
		tribe_get_end_date( $event->ID, false, 'H:i:s' ),
		tribe_get_venue( $event->ID ),
		tribe_get_organizer( $event->ID ),
		tribe_event_is_all_day( $event->ID ) ? '1' : '0',
		tribe_is_recurring_event( $event->ID ) ? '1' : '0'
	);

	foreach ( $fields as &$csv_field )
		$csv_field = '"' . str_replace('"', '""', $csv_field) . '"';

	echo join( ', ', $fields ) . "\n";
}
