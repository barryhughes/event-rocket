<?php
class EventRocket_RSVPAttendance
{
	const ATTENDEES = '_eventrocket_attendance';

	protected $event_id  = 0;
	protected $user_id   = 0;
	protected $attendees = array();


	public function __construct( $event_id, $user_id = null ) {
		$this->event_id  = $event_id;
		$this->user_id   = ( null === $user_id ) ? get_current_user_id() : $user_id;
		$this->attendees = (array) get_post_meta( $this->event_id, self::ATTENDEES, true );
	}

	public function is_attending( $user_id = null ) {
		$user_id = ( null === $user_id ) ? $this->user_id : $user_id;
		$user_id = absint( $user_id );
		return isset( $this->attendees[$user_id] ) && $this->attendees[$user_id];
	}

	public function is_not_attending( $user_id = null ) {
		$user_id = ( null === $user_id ) ? $this->user_id : $user_id;
		$user_id = absint( $user_id );
		return isset( $this->attendees[$user_id] ) && ! $this->attendees[$user_id];
	}

	public function is_undetermined( $user_id = null ) {
		$user_id = ( null === $user_id ) ? $this->user_id : $user_id;
		$user_id = absint( $user_id );
		return ! isset( $this->attendees[$user_id] );
	}

	public function will_attend( $user_id ) {

	}

	public function will_not_attend( $user_id ) {

	}
}