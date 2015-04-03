<?php
class EventRocket_Timeline_DayMarker {
	/** var DateTime */
	protected $last_date;

	/** var DateTime */
	protected $current_date;

	/** @var bool */
	protected $has_previous;

	/** @var bool */
	protected $has_next;

	/** @var int */
	protected $count = 0;


	public function display() {
		$this->setup();
		$this->current_date = new DateTime( tribe_get_start_date( null, true, 'Y-m-d H:i:s' ) );

		eventrocket_get_template( 'timeline/marker', array(
			'show_date'    => $this->show_date(),
			'show_time'    => $this->show_time(),
			'date'         => $this->current_date,
			'timestamp'    => $this->current_date->format( 'U' ),
			'has_previous' => $this->has_previous,
			'has_next'     => $this->has_next,
			'is_first'     => $this->is_first(),
			'is_last'      => $this->is_last()
		) );

		$this->last_date = $this->current_date;
	}

	protected function setup() {
		if ( 0 < $this->count++ ) return;
		$this->has_previous = timeline_has_previous_page();
		$this->has_next = timeline_has_next_page();
	}

	protected function show_date() {
		if ( ! is_a( $this->last_date, 'DateTime' ) ) return true;
		$last = $this->last_date->format( 'Y-m-d' );
		$current = $this->current_date->format( 'Y-m-d' );
		return ( $last !== $current );
	}

	protected function show_time() {
		if ( ! is_a( $this->last_date, 'DateTime' ) ) return true;
		$last = $this->last_date->format( 'H:i:s' );
		$current = $this->current_date->format( 'H:i:s' );
		return ( $last !== $current ||  $this->show_date() );
	}

	protected function is_first() {
		return ( 1 === $this->count );
	}

	protected function is_last() {
		global $wp_query;
		return ( $wp_query->post_count === $this->count );
	}
}