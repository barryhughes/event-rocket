<?php
/**
 * @internal
 */
class EventRocket_RSVPUser
{
	const ATTENDANCE = '_eventrocket_attendance';

	protected $user_id = 0;
	protected $attendance = array();


	public function __construct( $user_id ) {
		$this->user_id = $user_id;
		$this->attendance = (array) get_user_meta( $this->user_id, self::ATTENDANCE );
	}

	public function set_to_attend( $event_id ) {
		$event_id = absint( $event_id );
		$this->attendance[$event_id] = 1;
		$this->save();
	}

	public function set_to_not_attend( $event_id ) {
		$event_id = absint( $event_id );
		$this->attendance[$event_id] = 0;
		$this->save();
	}

	public function clear_from_list( $event_id ) {
		$event_id = absint( $event_id );
		unset( $this->attendance[$event_id] );
		$this->save();
	}

	protected function save() {
		update_user_meta( $this->user_id, self::ATTENDANCE, $this->attendance );
	}

	/**
	 * @param  bool  $only_upcoming
	 * @param  int   $attendance_flag
	 * @return array
	 */
	public function confirmed_attendances( $only_upcoming = true, $attendance_flag = 1 ) {
		$event_list = array();

		foreach ( $this->attendance as $event_id => $flag ) {
			if ( $flag != $attendance_flag && -1 !== $flag ) continue;
			$args = array( 'post_id' => $event_id );
			if ( $only_upcoming ) $args['eventDisplay'] = 'list';
			$events = tribe_get_event( $args );
			if ( isset( $events[0] ) ) $event_list[] = absint( $event_id );
		}

		return $event_id;
	}

	/**
	 * @param  bool $only_upcoming
	 * @return array
	 */
	public function confirmed_non_attendances( $only_upcoming = true ) {
		return $this->confirmed_attendances( $only_upcoming, 0 );
	}

	public function clear_expired_responses() {
		$all_events = $this->confirmed_attendances( false, -1 );
		$now        = date_i18n( 'Y-m-d H:i:s' );

		foreach ( $all_events as $event_id ) {
			$ended = tribe_get_end_date( $event_id, false, 'Y-m-d H:i:s' );
			if ( $ended < $now ) $this->clear_from_list( $event_id);
		}

		$this->save();
	}
}