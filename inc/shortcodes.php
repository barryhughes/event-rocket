<?php
/**
 * Generic shortcode wrapper: allows widgets to be bundled up and delivered via
 * shortcodes.
 */
class EventRocketWidgetShortcodes
{
	/**
	 * The widget class we are wrapping.
	 *
	 * @var string
	 */
	protected $class = '';

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = '';

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
		$this->class = $widget_class;
		$this->defaults = $defaults;
		$this->name = $shortcode_name;
		add_action( 'init', array( $this, 'register' ), 50 );

	}

	/**
	 * Register lateish during init, so the wrapper can be set up before the implementing class
	 * is actually defined.
	 */
	public function register() {
		if ( ! class_exists( $this->class ) ) return;
		$shortcode = apply_filters( 'event_rocket_shortcode_name', $this->name );
		add_shortcode( $shortcode, array( $this, 'shortcode' ) );
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
		$arguments = (array) apply_filters( $base_hook . 'arguments', array(), $instance );
		the_widget( $this->class, $instance, $arguments );

		return ob_get_clean();
	}

	/**
	 * Return the shortcode attributes array.
	 *
	 * @param $src_attrs
	 * @return array
	 */
	protected function attributes( $src_attrs ) {
		return array_merge( $this->defaults, $src_attrs );
	}
}

/**
 * Set up our widget-based shortcodes: if PRO is not enabled those shortcodes won't actually
 * be registered with WordPress. If PRO *is* enabled, the PRO list widget supercedes the
 * core equivalent.
 */
new EventRocketWidgetShortcodes( 'TribeEventsMiniCalendarWidget', 'event_rocket_calendar' );
new EventRocketWidgetShortcodes( 'TribeEventsListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeEventsAdvancedListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeCountdownWidget', 'event_rocket_countdown' );
new EventRocketWidgetShortcodes( 'TribeVenueWidget', 'event_rocket_venue' );


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

