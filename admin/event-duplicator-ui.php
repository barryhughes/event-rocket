<?php
class EventRocket_EventDuplicatorUI
{
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_ui_assets' ) );
	}

	public function add_ui_assets( $page_hook ) {
		global $post;
		if ( 'edit.php' !== $page_hook || Tribe__Events__Main::POSTTYPE !== get_post_type( $post ) ) return;

		$deps = array( 'jquery', 'jquery-ui-datepicker' );
		wp_enqueue_script( 'eventrocket_duplicator_ui', EVENTROCKET_URL . 'assets/duplicator.js', $deps );
		wp_localize_script( 'eventrocket_duplicator_ui', 'eventrocket_dup', $this->js_object() );
		wp_enqueue_style( 'eventrocket_duplicator_style', EVENTROCKET_URL . 'assets/duplicator.css' );
	}

	protected function js_object() {
		return array(
			'dialog_template' => $this->dialog_template()
		);
	}

	protected function dialog_template() {
		ob_start();
		include EVENTROCKET_INC . '/templates/duplicate-dialog.php';
		return ob_get_clean();
	}
}