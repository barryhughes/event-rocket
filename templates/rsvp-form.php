<?php
/**
 * @var bool $enabled
 * @var bool $restricted
 * @var bool $limited
 * @var bool $show_attendees
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

		<h4> <?php _ex( 'RSVP', 'form title', 'event-rocket' ) ?> </h4>

		<?php if ( $restricted && ! is_user_logged_in() ): ?>
			<p> <?php _e( 'To RSVP to this event you must be logged in!', 'event-rocket' ) ?> </p>
		<?php endif ?>

		<?php if ( is_user_logged_in() ): ?>

			<?php if ( $limited > 0 && $attendance->count_total_positive_responses() >= $limited && $attendance->is_user_undetermined( $user_id ) ): ?>
				<p>
					<?php _e( 'This event is full', 'event-rocket' ) ?>
				</p>
			<?php elseif ( $attendance->is_user_attending( $user_id ) ): ?>
				<p>
					<?php _e( 'You have indicated that you are attending this event!', 'event-rocket' ) ?> <br />
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'Withdraw from event', 'event-rocket' ) ?>
					</button>
				</p>
			<?php elseif ( $attendance->is_user_not_attending( $user_id ) ): ?>
				<p>
					<?php _e( 'You have indicated that you will not be attending this event!', 'event-rocket' ) ?> <br />
					<?php if ( $limited == 0 || $attendance->count_total_positive_responses() < $limited ): ?>
						<button class="eventrocket rsvp attend" name="rsvp_attend" value="<?php esc_attr_e( $user_id ) ?>">
							<?php _e( 'Actually &hellip; I will attend after all', 'event-rocket' ) ?>
						</button>
					<?php endif ?>
				</p>
			<?php elseif ( $attendance->is_user_undetermined( $user_id ) ): ?>
				<p>
					<?php _e( 'Please indicate if you plan to attend this event.', 'event-rocket' ) ?> <br />
					<button class="eventrocket rsvp attend" name="rsvp_attend" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'Yes! I will attend', 'event-rocket' ) ?>
					</button>
					<button class="eventrocket rsvp withdraw" name="rsvp_withdraw" value="<?php esc_attr_e( $user_id ) ?>">
						<?php _e( 'No, I will not', 'event-rocket' ) ?>
					</button>
				</p>
			<?php endif ?>

			<?php if ( $show_attendees ): ?>
				<h5> <?php
					$confirmed  = $attendance->count_total_positive_responses();
					$count_text = sprintf( _n( '(%d attending)', '(%d attending)', $confirmed, 'event-rocket' ), $confirmed );
					printf( __( 'Attendee list %1$s %2$s %3$s', 'event-rocket' ), '<i>', $count_text, '</i>' );
				?> </h5>

				<?php if ( 0 === $confirmed ): ?>
					<p> <?php _e( 'No confirmations so far.', 'event-rocket' ) ?> </p>
				<?php else: ?>
					<?php
					$anon_confirmations = $attendance->count_positive_anon_responses();
					?>
					<ul>
						<?php if ( $anon_confirmations > 0 ): ?>
							<li> <?php printf( _n( '%d anonymous attendee', '%d anonymous attendees', $anon_confirmations, 'event-rocket' ), $anon_confirmations ) ?> </li>
						<?php endif ?>

						<?php foreach ( $attendance->list_authed_positives( true ) as $user ): ?>
							<li> <?php echo esc_html( $user->display_name ) ?> </li>
						<?php endforeach ?>
					</ul>
				<?php endif // if we have confirmed attendees ?>
			<?php endif // if show_attendees is enabled ?>
		<?php endif // if the user is logged in ?>

		<?php if ( ! is_user_logged_in() ): ?>
			<?php if ( $limited > 0 && $attendance->count_total_positive_responses() >= $limited && ! $restricted && ! $anon_accepted ): ?>
				<p>
					<?php _e( 'This event is full', 'event-rocket' ) ?>
				</p>
			<?php elseif ( ! $restricted && ! $anon_accepted ): ?>
				<p>
					<?php _e( 'If you plan on attending please let us know by providing your email address.', 'event-rocket' ) ?> <br />

					<?php do_action( 'eventrocket_rsvp_anon_submission_form' ) ?>

					<input type="text" name="eventrocket_anon_id" id="eventrocket_anon_id" value="" placeholder="<?php esc_attr_e( 'your@email.address', 'event-rocket' ) ?>" />
					<button class="eventrocket rsvp anon attend" name="rsvp_attend" value="__anon">
						<?php _ex( 'Yes! I will attend', 'anon attendance button', 'event-rocket' ) ?>
					</button>
				</p>
			<?php endif ?>

			<?php if ( ! $restricted && $anon_accepted ): ?>
				<p>
					<?php _e( 'Thank you for confirming your attendance.', 'event-rocket' ) ?> <br />
				</p>
			<?php endif ?>
		<?php endif // if the user is not logged in ?>

	</form>
</div>