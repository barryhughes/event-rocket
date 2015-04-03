<?php defined( 'ABSPATH' ) or exit() ?>

<?php if ( $show_date ): ?>
	<div class="eventrocket day-marker">
		<span class="date-container">
			<span class="day">
				<?php echo date_i18n( apply_filters( 'eventrocket_timeline_marker_day_format', 'D' ), $timestamp ) ?>
			</span>
			<span class="date">
				<?php
					echo date_i18n( apply_filters( 'eventrocket_timeline_marker_date_format', 'j' ), $timestamp );
					echo '<sup>';
					echo date_i18n( apply_filters( 'eventrocket_timeline_marker_date_ordinal', 'S'), $timestamp  );
					echo '</sup>';
				?>
			</span>
			<span class="month"> <?php echo date_i18n( apply_filters( 'eventrocket_timeline_marker_month_format', 'M' ), $timestamp ) ?> </span>
		</span>
	</div>
<?php endif ?>

<?php if ( $show_time &&  ! tribe_event_is_all_day() ): ?>
	<div class="eventrocket time-marker">
		<span class="time-container">
			<?php echo date_i18n( apply_filters( 'eventrocket_timeline_marker_time_format', get_option( 'time_format', 'H:i' ) ), $timestamp ) ?>
		</span>
	</div>
<?php endif ?>

<?php
$classes = '';
if ( $is_first && ! $has_previous ) $classes = 'first';
if ( $is_last && ! $has_next ) $classes = 'last';
?>
<span class="running-line <?php echo $classes ?>"></span>