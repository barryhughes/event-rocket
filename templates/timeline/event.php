<?php do_action( 'tribe_events_before_the_event_title' ) ?>
	<h2 class="tribe-events-list-event-title entry-title summary">
		<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
			<?php the_title() ?>
		</a>
	</h2>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<div class="detail">
	<?php echo tribe_event_featured_image( null, 'medium' ) ?>
	<?php do_action( 'tribe_events_before_the_meta' ) ?>
	<?php do_action( 'tribe_events_after_the_meta' ) ?>
</div>


<?php do_action( 'tribe_events_before_the_content' ) ?>
	<div class="tribe-events-list-event-description tribe-events-content description entry-summary">
		<?php the_excerpt() ?>
		<a href="<?php echo tribe_get_event_link() ?>" class="tribe-events-read-more" rel="bookmark"><?php _e( 'Find out more', 'tribe-events-calendar' ) ?> &raquo;</a>
	</div> <!-- .tribe-events-list-event-description -->
<?php do_action( 'tribe_events_after_the_content' ) ?>