<?php
defined( 'ABSPATH' ) or exit();

$index = isset( $index ) ? $index : 0;

$height = apply_filters( 'eventrocket_embedded_map_height', '180px' );
$width = apply_filters( 'eventrocket_embedded_map_width', '100%' );
$style = apply_filters( 'eventrocket_embedded_map_style', "height: $height; width: $width" );
?>
<div class="eventrocket_embedded_map" id="eventrocket_map_<?php esc_attr_e( $index ) ?>" style="<?php esc_attr_e( $style ) ?>"></div>