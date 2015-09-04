<?php
defined( 'ABSPATH' ) or exit();


$longitude = isset( $longitude ) ? (float) $longitude : 0;
$latitude = isset( $latitude ) ? (float) $latitude : 0;

wp_nonce_field( 'event_rocket_save_long_lat', 'eventrocket_venue_positioning' );
?>

<p>
	<?php _e( 'Adjust the precise location of this venue for perfect positioning when using Google Maps.', 'event-rocket' ) ?>
</p>

<p>
	<label for="eventrocket_latitude"> <?php _e( 'Latitude', 'event-rocket' ); ?> </label> <br/>
	<input type="text" name="eventrocket_latitude" id="eventrocket_latitude" value="<?php esc_attr_e( $latitude ) ?>" />
</p>

<p>
	<label for="eventrocket_longitude"> <?php _e( 'Longitude', 'event-rocket' ); ?> </label> <br/>
	<input type="text" name="eventrocket_longitude" id="eventrocket_longitude" value="<?php esc_attr_e( $longitude ) ?>" />
</p>