<?php
/**
 * This template can be overriden by placing a copy in your theme directory:
 *
 *     themes/YOUR_THEME/tribe-events/rsvp-user-attendance.php
 *
 * @var array $attending
 * @var array $declines
 */

if ( ! empty( $attending ) ): ?>
	<h4> Confirmed as attending: </h4>
	<ul>
	<?php foreach ( $attending as $event ): ?>
		<li>
			<a href="<?php echo get_permalink( $event ) ?>"><?php echo get_the_title( $event ) ?></a>
			<?php printf( _x( 'on %s', 'user rsvp attendance', 'eventrocket' ), tribe_get_start_date( $event ) ) ?>
		</li>
	<?php endforeach ?>
	</ul>
<?php endif ?>

<?php if ( ! empty( $declines ) ): ?>
	<h4> Confirmed as absent: </h4>
	<ul>
		<?php foreach ( $declines as $event ): ?>
			<li>
				<a href="<?php echo get_permalink( $event ) ?>"><?php echo get_the_title( $event ) ?></a>
				<?php printf( _x( 'on %s', 'user rsvp attendance', 'eventrocket' ), tribe_get_start_date( $event ) ) ?>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>