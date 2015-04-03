<?php defined( 'ABSPATH' ) or exit() ?>

<div id="post-opening-timeline-section" class="<?php echo tribe_events_event_classes() ?>">

	<?php do_action( 'tribe_events_before_header_nav' ) ?>
	<div class="date-info page-nav" >
		<?php if ( timeline_has_previous_page() ) : ?>
			<span class="nav-container prev-page">
				<a href="<?php echo get_timeline_prev_page_url() ?>" rel="prev"><?php _e( 'Previous', 'eventrocket' ) ?></a>
			</span>
			<span class="running-line first"></span>
		<?php endif; ?>
	</div>
	<?php do_action( 'tribe_events_after_header_nav' ) ?>

	<?php do_action( 'tribe_events_before_header' ); ?>
		<div class="event-info view-title">
			<?php do_action( 'tribe_events_before_the_title' ) ?>
				<h2 class="tribe-events-page-title"><?php echo tribe_get_events_title() ?></h2>
			<?php do_action( 'tribe_events_after_the_title' ) ?>
		</div>
	<?php do_action( 'tribe_events_after_header' ); ?>

</div>