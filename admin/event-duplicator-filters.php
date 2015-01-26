<?php
class EventRocket_EventDuplicatorFilters
{
	public function __construct() {
		add_filter( 'eventrocket_duplicated_post_data', array( $this, 'customize_post_fields' ) );
		add_filter( 'eventrocket_duplicated_post_meta', array( $this, 'customize_meta_fields' ) );
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

	public function customize_meta_fields( array $meta ) {
		$date_overrides = array(
			'duplicate_start' => '_EventStartDate',
			'duplicate_end'   => '_EventEndDate'
		);

		foreach ( $date_overrides as $requested => $meta_field ) {
			if ( ! isset( $_REQUEST[$requested] ) || empty( $_REQUEST[$requested] ) ) continue;
			$formatted_date = date( 'Y-m-d H:i:s', strtotime( $_REQUEST[$requested] ) );
			if ( false === $formatted_date ) continue;
			$meta[$meta_field] = $formatted_date;
		}

		return $meta;
	}
}