<?php
class EventRocketQueryRequest
{
	protected $data = array(
		'type' => 'custom',
		'destination' => '',
		'from' => '',
		'to' => '',
		'category' => '',
		'tag' => '',
		'limit' => ''
	);

	protected $acceptable_types = array(
		'custom',
		'past',
		'upcoming'
	);

	/**
	 * Accepts either an array of query parameters or a JSON-encoded map of the same.
	 *
	 * @param $input
	 */
	public function __construct( $input ) {
		if ( is_array( $input ) ) $this->interpret_params( $input );
		if ( is_string( $input ) ) $this->interpret_message( $input );
	}

	/**
	 * Takes a JSON string and interprets it if possible.
	 *
	 * @param $json
	 */
	protected function interpret_message( $json ) {
		$json = json_decode( $json, true );
		if ( null === $json ) return;
		$this->interpret_params( $json );
	}

	/**
	 * Takes an array of parameters and tries to map them to the class's own
	 * set of expected query params.
	 *
	 * @param array $params
	 */
	protected function interpret_params( array $params ) {
		foreach ( $params as $key => $value )
			$this->__set( $key, $value );
	}

	public function __get( $key ) {
		if ( ! isset( $this->data[$key] ) ) return false;
		return $this->data[$key];
	}

	public function __set( $key, $value ) {
		$updater = "update_$key";
		if ( ! method_exists( $this, $updater ) ) return;
		call_user_func( array( $this, $updater ), $value );
	}

	protected function update_type( $value ) {
		if ( ! in_array( $value, $this->acceptable_types ) ) return;
		$this->data['type'] = $value;
	}

	protected function update_destination( $value ) {
		// Reference to another blog on the network?
		if ( is_numeric( $value ) && absint( $value ) == $value && is_multisite() )
			$this->data['destination'] = absint( $value );

		// URL for an external site?
		elseif ( filter_var( $value, FILTER_VALIDATE_URL ) )
			$this->data['destination'] = $value;
	}

	protected function update_from( $value ) {
		$time = strtotime( $value );
		if ( false !== $time ) $this->data['from'] = date( 'Y-m-d H:i:s', $time );
	}

	protected function update_to( $value ) {
		$time = strtotime( $value );
		if ( false !== $time ) $this->data['to'] = date( 'Y-m-d H:i:s', $time );
	}

	protected function update_category( $value ) {
		$this->update_taxonomy_list( 'category', $value );
	}

	protected function update_tag( $value ) {
		$this->update_taxonomy_list( 'tag', $value );
	}

	protected function update_taxonomy_list( $property, $value ) {
		$current_list = explode( ',', $this->data[$property] );
		$new_list = explode( ',', $value );

		foreach ( $new_list as $item ) {
			$item = trim( $item );
			if ( in_array( $item, $current_list ) ) continue;
			$current_list[] = $item;
		}

		$this->data[$property] = implode( ',', $current_list );
	}

	protected function update_limit( $value ) {
		$value = absint( $value );
		if ( $value !== 0 ) $this->data['limit'] = $value;
	}

	/**
	 * Converts the current set of parameters to an array of query params.
	 *
	 * @return array
	 */
	public function to_query() {
		return array();
	}

}