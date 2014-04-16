<div class="wrap">
	<h2> <?php _e( 'Event Data Cleanup', 'eventrocket' ) ?> </h2>

	<p> <?php
		_e( 'There may be occasions where you want to remove all traces of The Events Calendar and associated '
		. 'plugins from the database, either because you no longer need it or because you want to start afresh. '
		. 'This tool can help with that. <strong> Use with caution! </strong> ', 'eventrocket' );
	?> </p>

	<?php var_dump( $current_data ) ?>
</div>