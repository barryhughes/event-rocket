<?php
class EventRocket_EventDuplicatorFilters
{
	public function __construct() {
		add_filter( 'eventrocket_duplicated_post_data', array( $this, 'customize_post_fields' ) );
		add_filter( 'eventrocket_duplicated_post_meta', array( $this, 'customize_meta_fields' ) );
	}

	public function customize_post_fields( array $post ) {
		return $post;
	}

	public function customize_meta_fields( array $meta ) {
		return $meta;
	}
}