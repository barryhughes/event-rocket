<?php
defined( 'ABSPATH' ) or exit();


/**
 * Generic shortcode wrapper: allows widgets to be bundled up and delivered via
 * shortcodes.
 */
class EventRocket_WidgetShortcodes
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
		return array_merge( $this->defaults, (array) $src_attrs );
	}
}