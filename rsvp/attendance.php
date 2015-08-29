<?php
class EventRocket_RSVPAttendance
{
	const ATTENDEES = '_eventrocket_attendance';
	const ANONYMOUS = -1;

	protected $event_id  = 0;
	protected $attendees = array();


	public function __construct( $event_id ) {
		$this->event_id  = $event_id;
		$this->attendees = (array) get_post_meta( $this->event_id, self::ATTENDEES, true );
		unset( $this->attendees[0] );
	}

	public function is_user_attending( $user_id ) {
		$user_id = absint( $user_id );
		return isset( $this->attendees[$user_id] ) && $this->attendees[$user_id];
	}

	public function is_user_not_attending( $user_id ) {
		$user_id = absint( $user_id );
		return isset( $this->attendees[$user_id] ) && ! $this->attendees[$user_id];
	}

	public function is_user_undetermined( $user_id ) {
		$user_id = absint( $user_id );
		return ! isset( $this->attendees[$user_id] );
	}

	public function set_to_attend( $user_id ) {
		$user_id = absint( $user_id );
		$user = new EventRocket_RSVPUser( $user_id );
		$user->set_to_attend( $this->event_id );

		$this->attendees[$user_id] = 1;
		$this->save();
	}

	public function set_to_not_attend( $user_id ) {
		$user_id = absint( $user_id );
		$user = new EventRocket_RSVPUser( $user_id );
		$user->set_to_not_attend( $this->event_id );

		$this->attendees[$user_id] = 0;
		$this->save();
	}

	public function clear_from_list( $user_id  ) {
		$user_id = absint( $user_id );
		$user = new EventRocket_RSVPUser( $user_id );
		$user->clear_from_list( $this->event_id );

		unset( $this->attendees[$user_id] );
		$this->save();
	}

	public function set_anon_to_attend( $identifier ) {
		$this->attendees[self::ANONYMOUS][$identifier] = 1;
		$this->save();
	}

	public function set_anon_to_not_attend( $identifier ) {
		$this->attendees[self::ANONYMOUS][$identifier] = 0;
		$this->save();
	}

	public function clear_anon_from_list( $identifier ) {
		unset( $this->attendees[self::ANONYMOUS][$identifier] );
		$this->save();
	}

	protected function save() {
		update_post_meta( $this->event_id, self::ATTENDEES, $this->attendees );
	}

	public function count_total_responses() {
		return $this->count_all_authed_responses() + $this->count_all_anon_responses();
	}

	public function count_all_authed_responses() {
		$total = count( $this->attendees );
		if ( isset( $this->attendees[self::ANONYMOUS] ) ) $total--;
		return $total;
	}

	public function count_all_anon_responses() {
		if ( ! isset( $this->attendees[self::ANONYMOUS] ) ) return 0;
		return count( $this->attendees[self::ANONYMOUS] );
	}

	public function count_total_positive_responses() {
		return $this->count_positive_authed_responses() + $this->count_positive_anon_responses();
	}

	public function count_total_negative_responses() {
		return $this->count_negative_authed_responses() + $this->count_negative_anon_responses();
	}

	public function count_positive_authed_responses() {
		$attendees = $this->attendees;

		if ( isset( $attendees[self::ANONYMOUS] ) )
			unset( $attendees[self::ANONYMOUS] );

		$responses = array_count_values( $attendees );
		if ( ! isset( $responses[1] ) ) return 0;
		else return $responses[1];
	}

	public function count_negative_authed_responses() {
		$attendees = $this->attendees;

		if ( isset( $attendees[self::ANONYMOUS] ) )
			unset( $attendees[self::ANONYMOUS] );

		$responses = array_count_values( $attendees );
		if ( ! isset( $responses[0] ) ) return 0;
		else return $responses[0];
	}

	public function count_positive_anon_responses() {
		if ( ! isset( $this->attendees[self::ANONYMOUS] ) ) return 0;
		$responses = array_count_values( $this->attendees[self::ANONYMOUS] );
		if ( ! isset( $responses[1] ) ) return 0;
		else return $responses[1];
	}

	public function count_negative_anon_responses() {
		if ( ! isset( $this->attendees[self::ANONYMOUS] ) ) return 0;
		$responses = array_count_values( $this->attendees[self::ANONYMOUS] );
		if ( ! isset( $responses[0] ) ) return 0;
		else return $responses[0];
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_positives( $raw = false ) {
		return array_merge( $this->list_authed_positives( $raw ), $this->list_anon_positives( $raw ) );
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_negatives( $raw = false ) {
		return array_merge( $this->list_authed_negatives( $raw ), $this->list_anon_negatives( $raw ) );
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_authed_positives( $raw = false ) {
		$attendees = $this->attendees;
		$user_list = array();

		if ( isset( $attendees[self::ANONYMOUS] ) )
			unset( $attendees[self::ANONYMOUS] );

		foreach ( $attendees as $user_id => $is_attending ) {
			if ( ! $is_attending ) continue;
			if ( ! ( $user = get_user_by( 'id', $user_id ) ) ) continue;

			$user_list[] = $raw
				? apply_filters( 'eventrocket_attendee_entry_raw', $user )
				: apply_filters( 'eventrocket_attendee_entry', "$user->display_name ($user->user_email)", $user, $this->event_id );
		}

		return $user_list;
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_anon_positives( $raw = false ) {
		$user_list = array();
		if ( ! isset($this->attendees[self::ANONYMOUS] ) ) return $user_list;

		foreach ( $this->attendees[self::ANONYMOUS] as $attendee => $is_attending ) {
			if ( ! $is_attending ) continue;
			$user_list[] = $raw
				? (object) apply_filters( 'eventrocket_anon_attendee_entry_raw', array( 'type' => 'anon', 'identifier' => $attendee ) )
				: apply_filters( 'eventrocket_anon_attendee_entry', sprintf( __( 'Anonymous (%s)', 'eventrocket' ), $attendee ) );
		}

		return $user_list;
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_authed_negatives( $raw = false ) {
		$attendees = $this->attendees;
		$user_list = array();

		if ( isset( $attendees[self::ANONYMOUS] ) )
			unset( $attendees[self::ANONYMOUS] );

		foreach ( $attendees as $user_id => $is_attending ) {
			if ( $is_attending ) continue;
			if ( ! ( $user = get_user_by( 'id', $user_id ) ) ) continue;

			$user_list[] = $raw
				? apply_filters( 'eventrocket_non_attendee_entry_raw', $user )
				: apply_filters( 'eventrocket_non_attendee_entry', "$user->display_name ($user->user_email)", $user, $this->event_id );
		}

		return $user_list;
	}

	/**
	 * @param bool $raw (if the raw user object should be returned instead of a formatted string)
	 *
	 * @return array
	 */
	public function list_anon_negatives( $raw = false ) {
		$user_list = array();
		if ( ! isset($this->attendees[self::ANONYMOUS] ) ) return $user_list;

		foreach ( $this->attendees[self::ANONYMOUS] as $attendee => $is_attending ) {
			if ( $is_attending ) continue;

			$user_list[] = $raw
				? apply_filters( 'eventrocket_anon_non_attendee_entry_raw', array( 'type' => 'anon', 'identifier' => $attendee ) )
				: apply_filters( 'eventrocket_anon_non_attendee_entry', sprintf( __( 'Anonymous (%s)', 'eventrocket' ), $attendee ) );
		}

		return $user_list;
	}
}
