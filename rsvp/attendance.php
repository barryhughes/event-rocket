<?php
class EventRocket_RSVPAttendance
{
	const ATTENDEES = '_eventrocket_attendance';

	protected $event_id  = 0;
	protected $attendees = array();


	public function __construct( $event_id ) {
		$this->event_id  = $event_id;
		$this->attendees = (array) get_post_meta( $this->event_id, self::ATTENDEES, true );
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
		$this->attendees[$user_id] = true;
		$this->save();
	}

	public function set_to_not_attend( $user_id ) {
		$user_id = absint( $user_id );
		$this->attendees[$user_id] = false;
		$this->save();
	}

	public function clear_from_list( $user_id  ) {
		$user_id = absint( $user_id );
		unset( $this->attendees[$user_id] );
		$this->save();
	}

	protected function save() {
		update_post_meta( $this->event_id, self::ATTENDEES, $this->attendees );
	}
}