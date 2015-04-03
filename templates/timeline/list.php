<?php defined( 'ABSPATH' ) or exit() ?>

<?php do_action( 'tribe_events_before_template' ) ?>
<?php tribe_get_template_part( 'modules/bar' ) ?>

<div id="tribe-events-content" class="tribe-events-list timeline">

	<?php tribe_events_the_notices() ?>

	<?php if ( have_posts() ) : ?>

		<?php do_action( 'tribe_events_before_loop' ); ?>
		<div class="tribe-events-loop vcalendar">

			<?php
			eventrocket_get_template( 'timeline/header');
			eventrocket_get_template( 'timeline/loop' );
			eventrocket_get_template( 'timeline/footer' );
			?>

		</div><!-- .tribe-events-loop -->
		<?php do_action( 'tribe_events_after_loop' ) ?>

	<?php endif ?>

</div> <!-- #tribe-events-content -->

<div class="tribe-clear"></div>
<?php do_action( 'tribe_events_after_template' ) ?>