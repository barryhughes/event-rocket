<?php
/**
 * Allow the venue or ID for widgets to be specified using any of these forms, according
 * to what makes sense in a given context:
 *
 *     event_id="123"
 *     venue_id="123"
 *     id="123"
 *
 * This is needed because WordPress lowercases the attribute keys, however the ECP widget
 * classes expect the ID to be specified with in the form event_ID.
 *
 * @param $atts
 * @return mixed
 */
function eventrocket_widget_id_atts( $atts ) {
	if ( isset( $atts['id'] ) ) {
		$atts['venue_ID'] = $atts['id'];
		$atts['event_ID'] = $atts['id'];
	}

	if ( isset( $atts['event_id'] ) )
		$atts['event_ID'] = $atts['event_id'];

	if ( isset( $atts['venue_id'] ) )
		$atts['venue_ID'] = $atts['venue_id'];

	return $atts;
}

add_filter( 'event_rocket_shortcode_tribecountdownwidget_attributes', 'eventrocket_widget_id_atts', 10 );
add_filter( 'event_rocket_shortcode_tribevenuewidget_attributes', 'eventrocket_widget_id_atts', 10 );

/**
 * Ensure the minimum expected attributes are available to the countdown widget.
 *
 * @param $atts
 * @return array
 */
function eventrocket_countdown_atts( $atts ) {
	$defaults = array( 'title' => '', 'complete' => false, 'show_seconds' => false );
	return array_merge( $defaults, (array) $atts );
}

add_filter( 'event_rocket_shortcode_tribecountdownwidget_attributes', 'eventrocket_countdown_atts', 10 );