<?php
defined( 'ABSPATH' ) or exit();

// Dependencies
require_once __DIR__ . '/radio/queryrequest.php';
require_once __DIR__ . '/radio/transmitter.php';


/**
 * Provides an ability to query for events that live on other blogs, whether in the sense of
 * a multisite network or from one standalone installation to another.
 */
class EventRocketRadio
{
	/**
	 * Indicate if the class has initialized.
	 *
	 * @var bool
	 */
	protected static $ready = false;

	/**
	 * @var EventRocketQueryRequest
	 */
	protected static $incoming_request;

	/**
	 * @var EventRocketQueryRequest
	 */
	protected static $outgoing_request;

	/**
	 * @var array
	 */
	protected static $response = array();


	/**
	 * Ensure the transmitter and receiver objects are set up (but do so only once).
	 */
	public static function initialize() {
		if ( self::$ready ) return;
		self::$ready = true;
		add_action( 'wp_loaded', array( __CLASS__, 'listener' ) );
	}

	public static function listener() {
		if ( ! isset( $_GET['eventrocketradio'] ) ) return;
		self::process();
		self::respond();
		exit();
	}

	protected function process() {
		self::$incoming_request = new EventRocketQueryRequest( $_GET );
		$query_args = self::$incoming_request->to_query();
	}

	protected function respond() {
		echo json_encode( $this->response );
	}

}

EventRocketRadio::initialize();