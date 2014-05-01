<?php
// Dependencies
require_once __DIR__ . '/radio/receiver.php';
require_once __DIR__ . '/radio/transmitter.php';

/**
 * Provides an ability to query for events that live on other blogs, whether in the sense of
 * a multisite network or from one standalone installation to another.
 */
class EventRocketRadio
{
	/**
	 * @var EventRocketReceiver
	 */
	protected static $receiver;

	/**
	 * @var EventRocketTransmitter
	 */
	protected static $transmitter;

	protected static $ready = false;


	public static function initialize() {
		if ( self::$ready ) return;
		self::receiver();
		self::transmitter();
	}

	public static function receiver() {
		if ( ! isset( self::$receiver ) ) self::$receiver = new EventRocketReceiver;
		return self::$receiver;
	}

	public static function transmitter() {
		if ( ! isset( self::$transmitter ) ) self::$transmitter = new EventRocketTransmitter;
		return self::$transmitter;
	}
}

EventRocketRadio::initialize();