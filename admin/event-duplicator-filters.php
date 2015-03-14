<?php
class EventRocket_EventDuplicatorFilters
{
	public function __construct() {
		add_filter( 'eventrocket_duplicated_post_data', array( $this, 'customize_post_fields' ) );
		add_filter( 'eventrocket_duplicated_post_meta', array( $this, 'customize_date_fields' ), 10, 2 );
	}

	public function customize_post_fields( array $post ) {
		$overrides = array(
			'duplicate_title'   => 'post_title',
			'duplicate_content' => 'post_content',
			'duplicate_excerpt' => 'post_excerpt',
			'duplicate_status'  => 'post_status'
		);

		foreach ( $overrides as $requested => $post_field )
			if ( isset( $_REQUEST[$requested] ) && ! empty( $_REQUEST[$requested] ) )
				$post[$post_field] = (string) $_REQUEST[$requested];

		return $post;
	}

	public function customize_date_fields( array $meta, WP_Post $original ) {
		$start_date_set = false;
		$end_date_set   = false;

		if ( isset( $_REQUEST['duplicate_start'] ) && ! empty( $_REQUEST['duplicate_start'] ) )
			$start_date_set = $this->set_date( $meta, '_EventStartDate', $this->add_start_time( $_REQUEST['duplicate_start'], $original ) );

		if ( isset( $_REQUEST['duplicate_end'] ) && ! empty( $_REQUEST['duplicate_end'] ) )
			$end_date_set = $this->set_date( $meta, '_EventEndDate', $_REQUEST['duplicate_end'] );

		if ( $start_date_set && ! $end_date_set )
			$this->maintain_gap( $meta, $original );

		return $meta;
	}

	protected function add_start_time( $datetime, $original ) {
		$date = date( 'Y-m-d', strtotime( $datetime ) );
		$time = tribe_get_start_date( $original->ID, false, 'H:i:s' );
		return "$date $time";
	}

	protected function set_date( array &$meta, $field, $input ) {
		$formatted_date = date( 'Y-m-d H:i:s', strtotime( $input ) );

		if ( false !== $formatted_date ) {
			$meta[$field] = $formatted_date;
			return true;
		}
		return false;
	}

	protected function maintain_gap( array &$meta, $original ) {
		// Original event start/end times and difference between them
		$start = tribe_get_start_date( $original->ID, false, Tribe__Events__Date_Utils::DBDATETIMEFORMAT );
		$end   = tribe_get_end_date( $original->ID, false, Tribe__Events__Date_Utils::DBDATETIMEFORMAT );
		$diff  = strtotime( $end ) - strtotime( $start );

		// Adjust new event end time to maintain the same difference
		$start = strtotime( $meta['_EventStartDate'] );
		$end   = $start + $diff;
		$meta['_EventEndDate'] = date( Tribe__Events__Date_Utils::DBDATETIMEFORMAT, $end );
	}
}