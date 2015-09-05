<?php
class EventRocket_EventDuplicatorUI
{
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_ui_assets' ) );
	}

	public function add_ui_assets( $page_hook ) {
		global $post;
		if ( 'edit.php' !== $page_hook || Tribe__Events__Main::POSTTYPE !== get_post_type( $post ) ) return;

		$deps = array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' );
		wp_enqueue_script( 'eventrocket_timepicker', EVENTROCKET_URL . 'assets/timepicker.js', $deps );
		wp_enqueue_script( 'eventrocket_duplicator_ui', EVENTROCKET_URL . 'assets/duplicator.js', $deps );
		wp_localize_script( 'eventrocket_duplicator_ui', 'eventrocket_dup', $this->js_object() );

		wp_enqueue_style( 'eventrocket_timepicker_style', EVENTROCKET_URL . 'assets/timepicker.css' );
		wp_enqueue_style( 'eventrocket_duplicator_style', EVENTROCKET_URL . 'assets/duplicator.css' );
	}

	protected function js_object() {
		return array(
			'dialog_template' => $this->dialog_template(),
			'date_format'     => $this->date_format(),
			'time_format'     => $this->time_format()
		);
	}

	protected function dialog_template() {
		ob_start();
		include EVENTROCKET_INC . '/templates/duplicate-dialog.php';
		return ob_get_clean();
	}

	protected function date_format() {
		$format = 'yy-mm-dd';
		return apply_filters( 'eventrocket_duplicator_js_datepicker_format', $format );
	}

	protected function time_format() {
		$format = 'HH:mm'; // Default to 24hr time
		$wp_time_format = get_option( 'time_format' );

		// Check for 12hr time formats
		foreach ( array( 'a', 'A', 'g', 'G' ) as $indicates_12hrs ) {
			if ( false !== strpos( $wp_time_format, $indicates_12hrs ) ) {
				$format = 'hh:mm tt';
				break;
			}
		}

		return apply_filters( 'eventrocket_duplicator_js_timepicker_format', $format );
	}
}