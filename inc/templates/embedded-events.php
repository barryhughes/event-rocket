<?php defined( 'ABSPATH' ) or exit() ?>

<div class="eventrocket embedded-event post">
	<div class="post-thumb"> <?php echo tribe_event_featured_image() ?> </div>
	<h3> <a href="<?php the_permalink() ?>"><?php the_title() ?></a> </h3>
	<div class="schedule"> <?php echo tribe_events_event_schedule_details() ?> </div>
	<div class="summary"> <?php the_excerpt() ?> </div>
</div>