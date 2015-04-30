<?php
defined( 'ABSPATH' ) or exit();


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
 * Support category and tag attributes when setting up calendar/list widgets.
 *
 * From within the shortcode, users can supply tag/category IDs or slugs using "category",
 * "categories", "tag" and "tags" attributes. All of these are potentially valid examples:
 *
 *     [event_rocket_calendar category="43"]
 *     [event_rocket_calendar category="special-events"]
 *     [event_rocket_calendar categories="871, featured-events" tag="staff-only"]
 *
 * @param $atts
 * @return mixed
 */
function eventrocket_tax_atts( $atts ) {
	$filters = array();

	$expect = array(
		'categories' => '',
		'category' => '',
		'tags' => '',
		'tag' => ''
	);

	$relationship = array(
		'categories' => Tribe__Events__Main::TAXONOMY,
		'tags' => 'post_tag'
	);

	$params = array_merge( $expect, $atts );
	$params['categories'] = $params['categories'] . ',' . $params['category'];
	$params['tags'] = $params['tags'] . ',' . $params['tag'];

	foreach ( $relationship as $param => $tax ) {
		$terms = explode( ',', $params[$param] );
		foreach ( $terms as $term ) {
			$term = trim( $term );
			if ( empty( $term ) ) continue;
			if ( is_numeric( $term ) && $term == absint( $term ) ) $filters[$tax][] = $term;
			else {
				$term_obj = get_term_by( 'slug', $term, $tax );
				if ( false === $term_obj ) continue;
				$filters[$tax][] = $term_obj->term_id;
			}
		}
	}

	if ( ! empty( $filters ) ) $atts['filters'] = json_encode( $filters );
	return $atts;
}

add_filter( 'event_rocket_shortcode_tribeeventsminicalendarwidget_attributes', 'eventrocket_tax_atts', 10 );
add_filter( 'event_rocket_shortcode_tribeeventsadvancedlistwidget_attributes', 'eventrocket_tax_atts', 10 );

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