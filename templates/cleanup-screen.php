<?php defined( 'ABSPATH' ) or exit() ?>

<div class="wrap eventrocket cleanup">
	<h2> <?php _e( 'Event Data Cleanup', 'event-rocket' ) ?> </h2>

	<?php if ( ! $in_progress ): ?>

		<p> <?php
			_e( 'There may be occasions where you want to remove all traces of The Events Calendar and associated '
			. 'plugins from the database, either because you no longer need it or because you want to start afresh. '
			. 'This tool can help with that. <strong> Use with caution! </strong> ', 'event-rocket' );
		?> </p>

	<?php elseif ( $in_progress && 0 < max( $current_data ) ): ?>

		<div class="updated">
			<p> <?php _e( '<strong> Still working&hellip; </strong> please be patient while the remaining data is '
				. 'removed.', 'event-rocket' ); ?>
				<img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ) ?>" />
			</p>
			<input type="hidden" name="keep_working" id="keep_working" value="<?php echo esc_attr( $action_url ) ?>" />
		</div>

	<?php elseif ( $in_progress && 0 === max( $current_data ) ): ?>

		<div class="updated"> <p>
			<?php _e( '<strong> Cleanup complete! </strong> Now get back to work. ', 'event-rocket' ); ?>
		</p> </div>

	<?php endif ?>

	<?php if ( 0 === max( $current_data ) ): ?>
		<p> <strong> <?php _e( 'No event related data found! You&#146;re all set!', 'event-rocket') ?> </strong></p>
	<?php else: ?>
		<h4> <?php _e( 'Event data found within your database:', 'event-rocket' ) ?> </h4>
		<dl>
			<dt> <?php _e( 'Event objects', 'event-rocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['events'] ) ?> </dd>
			<dt> <?php _e( 'Venue objects', 'event-rocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['venues'] ) ?> </dd>
			<dt> <?php _e( 'Organizer objects', 'event-rocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['organizers'] ) ?> </dd>
			<dt> <?php _e( 'User capabilities', 'event-rocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['capabilities'] ) ?> </dd>
			<dt> <?php _e( 'Other settings', 'event-rocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['options'] ) ?> </dd>
		</dl>

		<?php if ( ! $in_progress ): ?>

			<h4> <?php _e( 'Run the cleanup tool', 'event-rocket' ) ?> </h4>
			<div class="hide-if-no-js">
				<p id="cleanup_safety">
					<input type="checkbox" name="user_confirms" id="user_confirms" value="1" />
					<label for="user_confirms"> <?php _e( 'I understand the risks involved and acknowledge that running '
						. 'this cleanup tool without first making a backup may be deemed an act of stupidity.',
						'event-rocket' ) ?> </label>
				</p>
			</div>

			<p id="do_cleanup"> <a href="<?php echo esc_attr( $action_url ) ?>" class="button-primary"><?php _e( 'Do cleanup', 'event-rocket' ) ?></a> </p>
		<?php endif ?>

	<?php endif ?>
</div>