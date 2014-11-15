<?php defined( 'ABSPATH' ) or exit() ?>

<div class="eventrocket embedded-organizer post">
	<div class="post-thumb"> <?php the_post_thumbnail() ?> </div>
	<h3> <a href="<?php the_permalink() ?>"><?php the_title() ?></a> </h3>
	<?php the_excerpt() ?>
</div>