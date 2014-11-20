<?php
/**
 * @var bool $enabled
 * @var bool $restricted
 * @var bool $anon_accepted
 * @var EventRocket_RSVPAttendance $attendance
 */

defined( 'ABSPATH' ) or exit();

if ( ! $enabled ) return;

$post_id = get_the_ID();
$div_id  = 'rsvp-form-' . $post_id;
$user_id = get_current_user_id();
?>

<div id="<?php esc_attr_e( $div_id ) ?>" class="eventrocket rsvp form">
	<form action="#<?php esc_attr_e( $div_id ) ?>" method="post">
		<?php wp_nonce_field( 'mark_attendance' . $user_id . $post_id, 'eventrocket_rsvp_check' ) ?>
		<input type="hidden" name="eventrocket_rsvp_event" value="<?php esc_attr_e( $post_id ) ?>" />

		<h4> <?php _ex( 'RSVP', 'form title', 'eventrocket' ) ?> </h4>

		<?php if ( $restricted && ! is_user_logged_in() ): ?>
			<p> <?php _e( 'To RSVP to this event you must be logged in!', 'eventrocket' ) ?> </p>
		<?php endif ?>

		<?php if ( is_user_logged_in() ): ?>
			<?php if ( $attendance->is_user_attending( $user_id ) ): ?>
				<p>
					<?php _e( 'You have indicated that you are attending this event!', 'eventrocket' ) ?> <br />
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'Withdraw from event', 'eventrocket' ) ?>
					</button>
				</p>
			<?php elseif ( $attendance->is_user_not_attending( $user_id ) ): ?>
				<p>
					<?php _e( 'You have indicated that you will not be attending this event!', 'eventrocket' ) ?> <br />
					<button class="eventrocket rsvp attend" name="rsvp_attend" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'Actually &hellip; I will attend after all', 'eventrocket' ) ?>
					</button>
				</p>
			<?php elseif ( $attendance->is_user_undetermined( $user_id ) ): ?>
				<p>
					<?php _e( 'Please indicate if you plan to attend this event.', 'eventrocket' ) ?> <br />
					<button class="eventrocket rsvp attend" name="rsvp_attend" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'Yes! I will attend', 'eventrocket' ) ?>
					</button>
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'No, I will not', 'eventrocket' ) ?>
					</button>
				</p>
			<?php endif ?>
		<?php endif ?>

		<?php if ( ! is_user_logged_in() ): ?>
			<?php if ( ! $restricted && ! $anon_accepted ): ?>
				<p>
					<?php _e( 'If you plan on attending please let us know by providing your email address.', 'eventrocket' ) ?> <br />

					<?php do_action( 'eventrocket_rsvp_anon_submission_form' ) ?>

					<input type="text" name="eventrocket_anon_id" id="eventrocket_anon_id" value="" placeholder="<?php esc_attr_e( 'your@email.address', 'eventrocket' ) ?>" />
					<button class="eventrocket rsvp anon attend" name="rsvp_attend" value="__anon">
						<?php _ex( 'Yes! I will attend', 'anon attendance button', 'eventrocket' ) ?>
					</button>
				</p>
			<?php endif ?>

			<?php if ( ! $restricted && $anon_accepted ): ?>
				<p>
					<?php _e( 'Thank you for confirming your attendance.', 'eventrocket' ) ?> <br />
				</p>
			<?php endif ?>
		<?php endif ?>
	</form>
</div>