<?php
class EventRocket_Timeline_View extends Tribe__Events__Template_Factory {
	public function __construct() {
		parent::__construct();
		add_filter( 'tribe_events_current_view_template', array( $this, 'template' ) );
		add_filter( 'tribe_events_event_classes', array( $this, 'event_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_css' ), 100 );
	}

	public function template( $template ) {
		if ( ! is_timeline_view() ) return $template;
		return eventrocket_template( 'timeline/list' );
	}

	public function event_classes( $classes ) {
		return array_merge( (array) $classes, array( 'eventrocket', 'timeline' ) );
	}

	public function add_css() {
		if ( ! is_timeline_view() ) return;
		wp_enqueue_style( 'eventrocket_timeline_style', EVENTROCKET_URL . '/assets/timeline.css' );
	}
}