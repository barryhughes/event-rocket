<?php
/**
 * Event editor meta box options.
 *
 * We primarily use a table and inline styles to control cell width because
 * that is in keeping with what current builds of The Events Calendar do;
 * we should revise to something a little friendlier at some stage but for
 * now we'll follow suit and even go a step further and use a nested table
 * ... le GASP!
 *
 * @var bool $enabled
 * @var bool $restricted
 * @var number $limited
 * @var bool $show_attendees
 * @var EventRocket_RSVPAttendance $attendance
 */

defined( 'ABSPATH' ) or die();
?>
<table class="eventtable eventrocket_rsvp">
	<tr>
		<td class="tribe_sectionheader" colspan="3">
			<h4> <?php _e( 'RSVP', 'event-rocket' ) ?> </h4>
		</td>
	</tr>

	<tr>
		<td style="width: 25%"> <?php _e( 'Enable RSVPs:', 'event-rocket' ) ?> </td>
		<td> <input type="checkbox" name="<?php esc_attr_e( EventRocket_RSVPManager::ENABLE_RSVP ) ?>" <?php checked( $enabled ) ?>/> </td>

		<!-- Summary -->
		<td rowspan="4">
			<table>
				<tr>
					<td class="eventrocket_rsvp_attending"> <?php _e( 'Attending:', 'event-rocket' ) ?> </td>
					<td> <strong> <?php echo $attendance->count_total_positive_responses() ?> </strong> </td>
				</tr>
			    <tr>
				    <td class="eventrocket_rsvp_not_attending"> <?php _e( 'Not attending:', 'event-rocket' ) ?> </td>
				    <td> <strong> <?php echo $attendance->count_total_negative_responses() ?> </strong> </td>
			    </tr>
			</table>
		</td>
	</tr>

	<tr class="eventrocket_additionaloptions restrict_to_authed">
		<td> <?php _e( 'Restrict to logged in users:', 'event-rocket' ) ?> </td>
		<td> <input type="checkbox" name="<?php echo esc_attr( EventRocket_RSVPManager::RESTRICT_RSVP ) ?>" <?php checked( $restricted ) ?>/> </td>
	</tr>

	<tr class="eventrocket_additionaloptions show_attendees">
		<td> <?php _e( 'Show attendees:', 'event-rocket' ) ?> </td>
		<td> <input type="checkbox" name="<?php echo esc_attr( EventRocket_RSVPManager::SHOW_ATTENDEES_RSVP ) ?>" <?php checked( $show_attendees ) ?>/> </td>
	</tr>

	<tr class="eventrocket_additionaloptions limit_attendees">
		<td> <?php _e( 'Limit attendance ', 'event-rocket' ) ?> <i><?php _e( '(-1 for unlimited)', 'event-rocket' ) ?></i>: </td>
		<td> <input type="number" name="<?php esc_attr_e( EventRocket_RSVPManager::LIMIT_RSVP ) ?>" value="<?php echo esc_attr( $limited ) ?>" min="-1" /> </td>
	</tr>
</table>
