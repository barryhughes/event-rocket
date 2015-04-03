<?php
defined( 'ABSPATH' ) or exit();
$marker = new EventRocket_Timeline_DayMarker;
?>

<?php while ( have_posts() ) :
	the_post();
	do_action( 'tribe_events_inside_before_loop' );
	?>

	<div id="post-<?php the_ID() ?>" class="<?php tribe_events_event_classes() ?>">
		<div class="date-info">
			<?php $marker->display() ?>
		</div>
		<div class="event-info">
			<?php eventrocket_get_template( 'timeline/event' ) ?>
		</div>
	</div> <!-- #post-n -->

	<?php do_action( 'tribe_events_inside_after_loop' ) ?>

<?php endwhile ?>