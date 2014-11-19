<?php
/**
 * @var bool $enabled
 * @var bool $restricted
 * @var EventRocket_RSVPAttendance $attendance
 */

defined( 'ABSPATH' ) or exit();

if ( ! $enabled ) return;
?>

<div class="eventrocket rsvp form">
	<form action="" method="post">
		<h4> <?php _ex( 'RSVP', 'form title', 'eventrocket' ) ?> </h4>

		<?php if ( $restricted && ! is_user_logged_in() ): ?>
			<p> <?php _e( 'To RSVP to this event you must be logged in!', 'eventrocket' ) ?> </p>
		<?php endif ?>

		<?php if ( $restricted && is_user_logged_in() ): ?>
			<?php if ( $attendance->is_attending() ): ?>
				<p>
					<?php _e( 'You have indicated that you are attending this event!', 'eventrocket' ) ?>
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw_<?php echo wp_create_nonce( 'withdraw' . get_the_ID() . get_current_user_id() ) ?>">
						<?php _e( 'Withdraw from event', 'eventrocket' ) ?>
					</button>
				</p>
			<?php elseif ( $attendance->is_not_attending() ): ?>
				<p>
					<?php _e( 'You have indicated that you will not be attending this event!', 'eventrocket' ) ?>
					<button class="eventrocket rsvp withdraw" name="rsvp_attend_<?php echo wp_create_nonce( 'attend' . get_the_ID() . get_current_user_id() ) ?>">
						<?php _e( 'Actually, I will attend!', 'eventrocket' ) ?>
					</button>
				</p>
			<?php elseif ( $attendance->is_undetermined() ): ?>
				<p>
					<?php _e( 'Please indicate if you plan to attend this event.', 'eventrocket' ) ?>
					<button class="eventrocket rsvp withdraw" name="rsvp_attend_<?php echo wp_create_nonce( 'attend' . get_the_ID() . get_current_user_id() ) ?>">
						<?php _e( 'Yes! I will attend', 'eventrocket' ) ?>
					</button>
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw_<?php echo wp_create_nonce( 'withdraw' . get_the_ID() . get_current_user_id() ) ?>">
						<?php _e( 'No, I will not', 'eventrocket' ) ?>
					</button>
				</p>
			<?php endif ?>
		<?php endif ?>

		<?php if ( ! $restricted ): ?>
		<?php endif ?>
	</form>
</div>