<?php
class EventRocketWidgetShortcodes
{
	/**
	 * The widget class we are wrapping.
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * Default instance arguments to pass to the widget.
	 *
	 * @var array
	 */
	protected $defaults = array();


	/**
	 * @param $widget_class
	 * @param $shortcode_name
	 * @param array $defaults
	 */
	public function __construct( $widget_class, $shortcode_name, array $defaults = array() ) {
		if ( ! class_exists( $widget_class ) ) return;
		$this->class = $widget_class;
		$this->defaults = $defaults;
		add_shortcode( $shortcode_name, array( $this, 'shortcode' ) );
	}

	/**
	 * Render the widget and return.
	 *
	 * @param $attributes
	 * @return string
	 */
	public function shortcode( $attributes ) {
		ob_start();

		$base_hook = 'event_rocket_shortcode_' . strtolower( $this->class ) . '_';
		$instance = (array) apply_filters( $base_hook . 'attributes', $this->attributes( $attributes ) );
		$arguments = (array) apply_filters( $base_hook . 'arguments', array() );
		the_widget( $this->class, $instance, $arguments );

		return ob_get_clean();
	}

	/**
	 * Parses the shortcode attributes and returns an array of two arrays - the first
	 * containing widget instance data and the second any other widget arguments.
	 *
	 * @param $src_attrs
	 * @return array
	 */
	protected function attributes( $src_attrs ) {
		return array_merge( $this->defaults, $src_attrs );
	}
}


new EventRocketWidgetShortcodes( 'TribeEventsMiniCalendarWidget', 'event_rocket_calendar' );
new EventRocketWidgetShortcodes( 'TribeEventsListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeEventsAdvancedListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeCountdownWidget', 'event_rocket_countdown' );
new EventRocketWidgetShortcodes( 'TribeVenueWidget', 'event_rocket_venue' );
