<?php defined( 'ABSPATH' ) or exit() ?>

<div class="tribe-events-loop vcalendar">

	<?php while ( have_posts() ) : the_post() ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>

		<?php
		$venue_details = array();

		if ( $venue_name = tribe_get_meta( 'tribe_event_venue_name' ) )
			$venue_details[] = $venue_name;

		if ( $venue_address = tribe_get_meta( 'tribe_event_venue_address' ) )
			$venue_details[] = $venue_address;

		$has_venue_address = ( $venue_address ) ? ' location' : '';
		?>

		<div id="post-<?php the_ID() ?>" class="<?php tribe_events_event_classes() ?>">
			<div class="date-info">

			</div>
			<div class="event-info">

				<?php do_action( 'tribe_events_before_the_event_title' ) ?>
				<h2 class="tribe-events-list-event-title entry-title summary">
					<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
						<?php the_title() ?>
					</a>
				</h2>
				<?php do_action( 'tribe_events_after_the_event_title' ) ?>

				<?php do_action( 'tribe_events_before_the_meta' ) ?>
				<div class="tribe-events-event-meta vcard">
					<div class="author <?php echo $has_venue_address ?>">
						<div class="updated published time-details">
							<?php echo tribe_events_event_schedule_details() ?>
						</div>

						<?php if ( $venue_details ) : ?>
							<div class="tribe-events-venue-details">
								<?php echo implode( ', ', $venue_details ) ?>
							</div> <!-- .tribe-events-venue-details -->
						<?php endif ?>

					</div>
				</div> <!-- .tribe-events-event-meta -->
				<?php do_action( 'tribe_events_after_the_meta' ) ?>

				<?php echo tribe_event_featured_image( null, 'medium' ) ?>

				<?php do_action( 'tribe_events_before_the_content' ) ?>
				<div class="tribe-events-list-event-description tribe-events-content description entry-summary">
					<?php the_excerpt() ?>
					<a href="<?php echo tribe_get_event_link() ?>" class="tribe-events-read-more" rel="bookmark"><?php _e( 'Find out more', 'tribe-events-calendar' ) ?> &raquo;</a>
				</div> <!-- .tribe-events-list-event-description -->
				<?php do_action( 'tribe_events_after_the_content' ) ?>

			</div>
		</div> <!-- .hentry .vevent -->


		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
	<?php endwhile; ?>

</div><!-- .tribe-events-loop -->
