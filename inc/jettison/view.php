<?php defined( 'ABSPATH' ) or exit() ?>

<div class="wrap eventrocket cleanup">
	<h2> <?php _e( 'Event Data Cleanup', 'eventrocket' ) ?> </h2>

	<?php if ( ! $in_progress ): ?>

		<p> <?php
			_e( 'There may be occasions where you want to remove all traces of The Events Calendar and associated '
			. 'plugins from the database, either because you no longer need it or because you want to start afresh. '
			. 'This tool can help with that. <strong> Use with caution! </strong> ', 'eventrocket' );
		?> </p>

	<?php elseif ( $in_progress && 0 < max( $current_data ) ): ?>

		<div class="updated">
			<p> <?php _e( '<strong> Still working&hellip; </strong> please be patient while the remaining data is '
				. 'removed.', 'eventrocket' ); ?>
				<img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ) ?>" />
			</p>
			<input type="hidden" name="keep_working" id="keep_working" value="<?php echo esc_attr( $action_url ) ?>" />
		</div>

	<?php elseif ( $in_progress && 0 === max( $current_data ) ): ?>

		<div class="updated"> <p>
			<?php _e( '<strong> Cleanup complete! </strong> Now get back to work. ', 'eventrocket' ); ?>
		</p> </div>

	<?php endif ?>

	<?php if ( 0 === max( $current_data ) ): ?>
		<p> <strong> <?php _e( 'No event related data found! You&#146;re all set!', 'eventrocket') ?> </strong></p>
	<?php else: ?>
		<h4> <?php _e( 'Event data found within your database:', 'eventrocket' ) ?> </h4>
		<dl>
			<dt> <?php _e( 'Event objects', 'eventrocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['events'] ) ?> </dd>
			<dt> <?php _e( 'Venue objects', 'eventrocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['venues'] ) ?> </dd>
			<dt> <?php _e( 'Organizer objects', 'eventrocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['organizers'] ) ?> </dd>
			<dt> <?php _e( 'User capabilities', 'eventrocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['capabilities'] ) ?> </dd>
			<dt> <?php _e( 'Other settings', 'eventrocket' ) ?> </dt>
			<dd> <?php echo esc_html( $current_data['options'] ) ?> </dd>
		</dl>

		<?php if ( ! $in_progress ): ?>

			<h4> <?php _e( 'Run the cleanup tool', 'eventrocket' ) ?> </h4>
			<div class="hide-if-no-js">
				<p id="cleanup_safety">
					<input type="checkbox" name="user_confirms" id="user_confirms" value="1" />
					<label for="user_confirms"> <?php _e( 'I understand the risks involved and acknowledge that running '
						. 'this cleanup tool without first making a backup may be deemed an act of stupidity.',
						'eventrocket' ) ?> </label>
				</p>
			</div>

			<p id="do_cleanup"> <a href="<?php echo esc_attr( $action_url ) ?>" class="button-primary"><?php _e( 'Do cleanup', 'eventrocket' ) ?></a> </p>
		<?php endif ?>

	<?php endif ?>
</div>