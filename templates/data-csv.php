<?php
/**
 * @var array $events
 */

$columns = array(
	_x( 'ID', 'csv-data', 'eventrocket' ),
	_x( 'Title', 'csv-data', 'eventrocket' ),
	_x( 'Description', 'csv-data', 'eventrocket' ),
	_x( 'Start Date', 'csv-data', 'eventrocket' ),
	_x( 'Start Time', 'csv-data', 'eventrocket' ),
	_x( 'End Date', 'csv-data', 'eventrocket' ),
	_x( 'End Time', 'csv-data', 'eventrocket' ),
	_x( 'Venue', 'csv-data', 'eventrocket' ),
	_x( 'Organizer', 'csv-data', 'eventrocket' ),
	_x( 'All Day', 'csv-data', 'eventrocket' ),
	_x( 'Recurring', 'csv-data', 'eventrocket' )
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
