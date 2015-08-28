<?php
class EventRocket_RSVPManager
{
	const ENABLE_RSVP         = '_eventrocket_enable_rsvp';
	const RESTRICT_RSVP       = '_eventrocket_restrict_rsvp';
	const LIMIT_RSVP          = '_eventrocket_limit_rsvp';
	const SHOW_ATTENDEES_RSVP = '_eventrocket_show_attendees_rsvp';

	/** @var  EventRocket_AttendeeList */
	protected $attendee_list;

	/** @var EventRocket_RSVPForm */
	protected $form;

	/** @var array of EventRocket_RSVPAttendance objects */
	protected $attendance = array();


	public function __construct() {
		add_action( 'tribe_events_cost_table', array( $this, 'editor_options' ), 5 );
		add_action( 'tribe_events_update_meta', array( $this, 'save_options' ), 10, 2 );
		$this->attendee_list();
		$this->form();
	}

	/**
	 * Adds RSVP options to the event editor metabox.
	 */
	public function editor_options() {
		$post_id = get_the_ID();

		$enabled    = get_post_meta( $post_id, self::ENABLE_RSVP, true );
		$restricted = get_post_meta( $post_id, self::RESTRICT_RSVP, true );

		$limited 	= get_post_meta( $post_id, self::LIMIT_RSVP, true );
		$limited    = empty( $limited ) ? 0 : absint( $limited );

		$show_attendees = get_post_meta( $post_id, self::SHOW_ATTENDEES_RSVP, true );
		$attendance     = $this->attendance( $post_id );

		include EVENTROCKET_INC . '/templates/rsvp-options.php';
	}

	/**
	 * Saves the RSVP options for the event.
	 *
	 * Note that nonce checks etc are performed upstream of this method within
	 * The Events Calendar itself.
	 *
	 * @param int   $event_id
	 * @param array $data
	 */
	public function save_options( $event_id, array $data ) {
		$enable         = isset( $data[self::ENABLE_RSVP] ) && $data[self::ENABLE_RSVP];
		$restrict       = isset( $data[self::RESTRICT_RSVP] ) && $data[self::RESTRICT_RSVP];
		$limited        = isset( $data[self::LIMIT_RSVP] ) ? $data[self::LIMIT_RSVP] : 0;
		$show_attendees = isset( $data[self::SHOW_ATTENDEES_RSVP] ) && $data[self::SHOW_ATTENDEES_RSVP];

		update_post_meta( $event_id, self::ENABLE_RSVP, $enable );
		update_post_meta( $event_id, self::RESTRICT_RSVP, $restrict );
		update_post_meta( $event_id, self::LIMIT_RSVP, $limited );

		update_post_meta( $event_id, self::ENABLE_RSVP, $enable );
		update_post_meta( $event_id, self::RESTRICT_RSVP, $restrict );
		update_post_meta( $event_id, self::SHOW_ATTENDEES_RSVP, $show_attendees );
	}

	/**
	 * @return EventRocket_RSVPAttendeeList
	 */
	public function attendee_list() {
		if ( ! isset( $this->attendee_list ) ) $this->attendee_list = new EventRocket_RSVPAttendeeList;
		return $this->attendee_list;
	}

	/**
	 * @return EventRocket_RSVPForm
	 */
	public function form() {
		if ( ! isset( $this->form ) ) $this->form = new EventRocket_RSVPForm;
		return $this->form;
	}

	/**
	 * @param  null $event_id
	 * @return EventRocket_RSVPAttendance
	 */
	public function attendance( $event_id = null ) {
		$id = null === $event_id ? get_the_ID() : $event_id;
		if ( ! isset( $this->attendance[$id] ) ) $this->attendance[$id] = new EventRocket_RSVPAttendance( $id );
		return $this->attendance[$id];
	}
}


/**
 * @return EventRocket_RSVPManager
 */
function eventrocket_rsvp() {
	static $rsvp = null;
	if ( null === $rsvp ) $rsvp = new EventRocket_RSVPManager;
	return $rsvp;
}
