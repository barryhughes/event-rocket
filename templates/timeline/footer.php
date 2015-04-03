<?php defined( 'ABSPATH' ) or exit() ?>

<div id="post-closing-timeline-section" class="<?php echo tribe_events_event_classes() ?>">

	<div class="date-info page-nav" >
		<?php do_action( 'tribe_events_before_footer_nav' ) ?>
		<?php if ( tribe_has_next_event() ) : ?>
			<span class="nav-container next-page">
				<a href="<?php echo get_timeline_next_page_url() ?>" rel="next"><?php _e( 'Next', 'eventrocket' ) ?></a>
			</span>
			<span class="running-line"></span>
		<?php endif ?>
		<?php do_action( 'tribe_events_after_footer_nav' ) ?>
	</div>
	<div class="event-info view-footer"></div>

</div>