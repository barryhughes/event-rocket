<?php
/**
 * @internal
 */
class EventRocket_RSVPUser
{
	const ATTENDANCE = '_eventrocket_attendance';

	protected $user_id = 0;
	protected $attendance = array();


	public function __construct( $user_id = 0 ) {
		$this->user_id = ( $user_id === 0 ) ? get_current_user_id() : absint( $user_id );
		$this->attendance = (array) get_user_meta( $this->user_id, self::ATTENDANCE, true );
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
			// Filter out events based on the attendance flag
			if ( $flag != $attendance_flag && -1 !== $flag ) continue;

			// Get the event: skip if it does not exist/has expired (only if $only_upcoming is true)
			if ( absint( $event_id ) <= 0 ) continue;
			$event = get_post( $event_id );
			if ( null === $event || ( $only_upcoming && $this->has_expired( $event_id ) ) ) continue;

			// Otherwise, add it!
			$event_list[] = absint( $event_id );
		}

		return $event_list;
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

		foreach ( $all_events as $event_id )
			if ( $this->has_expired( $event_id ) ) $this->clear_from_list( $event_id);

		$this->save();
	}

	protected function has_expired( $event_id ) {
		$now   = date_i18n( 'Y-m-d H:i:s' );
		$ended = tribe_get_end_date( $event_id, false, 'Y-m-d H:i:s' );
		return ( $ended < $now );
	}
}