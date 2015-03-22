<?php defined( 'ABSPATH' ) or exit() ?>

<?php do_action( 'tribe_events_before_template' ) ?>
<?php tribe_get_template_part( 'modules/bar' ) ?>

<!-- @todo render the actual view! -->

<div class="tribe-clear"></div>
<?php do_action( 'tribe_events_after_template' ) ?>