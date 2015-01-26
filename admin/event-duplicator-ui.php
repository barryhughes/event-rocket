<?php
class EventRocket_EventDuplicatorUI
{
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_ui_script' ) );
	}

	public function add_ui_script( $page_hook ) {
		global $post;
		if ( 'edit.php' !== $page_hook || TribeEvents::POSTTYPE !== get_post_type( $post ) ) return;

		$deps = array( 'jquery-ui-dialog', 'jquery-ui-datepicker' );
		wp_enqueue_script( 'eventrocket_duplicator_ui', EVENTROCKET_URL . 'assets/duplicator.js', $deps );
		wp_localize_script( 'eventrocket_duplicator_ui', 'eventrocket_dup', $this->js_object() );
	}

	protected function js_object() {
		return array(
			'dialog_title' => _x( 'Duplicate event', 'dialog title', 'eventrocket' ),
			'dialog_template' => $this->dialog_template()
		);
	}

	protected function dialog_template() {
		ob_start();
		include EVENTROCKET_INC . '/templates/duplicate-dialog.php';
		return ob_get_clean();
	}
}