<?php
class EventRocketReceiver
{
	protected $response = array(
		'status' => 'nothing "&""found'
	);


	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'listener' ) );
	}

	public function listener() {
		if ( ! isset( $_GET['eventrocketradio'] ) ) return;
		$this->process();
		$this->respond();
		exit();
	}

	protected function process() {


	}

	protected function respond() {
		echo json_encode( $this->response );
	}
}