<?php defined( 'ABSPATH' ) or exit() ?>

<?php do_action( 'tribe_events_before_template' ) ?>
<?php tribe_get_template_part( 'modules/bar' ) ?>

<div id="tribe-events-content" class="tribe-events-list">

	<?php do_action( 'tribe_events_before_the_title' ) ?>
	<h2 class="tribe-events-page-title"><?php echo tribe_get_events_title() ?></h2>
	<?php do_action( 'tribe_events_after_the_title' ) ?>

	<?php tribe_events_the_notices() ?>

	<?php do_action( 'tribe_events_before_header' ); ?>
	<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>

		<?php do_action( 'tribe_events_before_header_nav' ) ?>
		<?php eventrocket_get_template( 'timeline/nav' ) ?>
		<?php do_action( 'tribe_events_after_header_nav' ) ?>

	</div> <!-- #tribe-events-header -->
	<?php do_action( 'tribe_events_after_header' ); ?>

	<?php if ( have_posts() ) : ?>
		<?php do_action( 'tribe_events_before_loop' ); ?>
		<?php eventrocket_get_template( 'timeline/loop' )  ?>
		<?php do_action( 'tribe_events_after_loop' ); ?>
	<?php endif; ?>

	<?php do_action( 'tribe_events_before_footer' ); ?>
	<div id="tribe-events-footer">

		<!-- Footer Navigation -->
		<?php do_action( 'tribe_events_before_footer_nav' ); ?>
		<?php eventrocket_get_template( 'timeline/nav' ) ?>
		<?php do_action( 'tribe_events_after_footer_nav' ); ?>

	</div> <!-- #tribe-events-footer -->
	<?php do_action( 'tribe_events_after_footer' ) ?>

</div> <!-- #tribe-events-content -->

<div class="tribe-clear"></div>
<?php do_action( 'tribe_events_after_template' ) ?>