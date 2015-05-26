<?php
abstract class EventRocket_GenericDataRequests {
	protected $key;
	protected $events = array();
	protected $params = array(
		'from'       => '',
		'to'         => '',
		'event'      => '',
		'category'   => '',
		'categories' => '',
		'tag'        => '',
		'tags'       => '',
		'limit'      => '-1'
	);


	public function __construct() {
		add_action( 'wp', array( $this, 'listen' ) );
	}

	public function listen() {
		if ( ! isset( $this->key ) || ! isset( $_REQUEST[$this->key] ) ) return;
		$this->populate();
		$this->query();
		$this->present();
	}

	protected function populate() {
		foreach ( $this->params as $key => $value )
			$this->params[$key] = $this->get_sanitized_field( $key );
	}

	protected function get_sanitized_field( $key ) {
		if ( ! isset( $_REQUEST[$key] ) ) return '';
		$value = urldecode( $_REQUEST[$key] );

		switch ( $key ) {
			case 'from':
			case 'to':
				$time = strtotime( $value );
				if ( $time ) return date( 'Y-m-d H:i:s', $time );
				else return '';
			break;

			case 'event':
			case 'category':
			case 'categories':
			case 'tag':
			case 'tags':
				return preg_replace( '#[^a-zA-Z0-9\.-_ ]#', '', $value );
			break;

			case 'limit':
				return absint( $value );
			break;
		}

		return '';
	}

	protected function query() {
		$this->events = event_embed()->obtain( $this->params );
	}

	abstract protected function present();
}