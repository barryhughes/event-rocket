<?php
/**
 * Event editor meta box options.
 *
 * We primarily use a table and inline styles to control cell width because
 * that is in keeping with what current builds of The Events Calendar do;
 * we should revise to something a little friendlier at some stage.
 *
 * @var bool $enabled
 * @var bool $restricted
 */
?>
<table class="eventtable">
	<tr>
		<td class="tribe_sectionheader" colspan="2">
			<h4> <?php _e( 'RSVP', 'eventrocket' ) ?> </h4>
		</td>
	</tr>

	<tr>
		<td style="width: 25%"> <?php _e( 'Enable RSVPs:', 'eventrocket' ) ?> </td>
		<td> <input type="checkbox" name="<?php esc_attr_e( EventRocket_RSVPManager::ENABLE_RSVP ) ?>" <?php checked( $enabled ) ?>/> </td>
	</tr>

	<tr>
		<td> <?php _e( 'Restrict to logged in users:', 'eventrocket' ) ?> </td>
		<td> <input type="checkbox" name="<?php esc_attr_e( EventRocket_RSVPManager::RESTRICT_RSVP ) ?>" <?php checked( $restricted ) ?>/> </td>
	</tr>
</table>