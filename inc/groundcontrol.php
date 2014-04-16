<?php
defined( 'EVENTROCKET_INC' ) or die();

/**
 * Event Rocket is essentially a collection of small modules that have little
 * relationship between one another.
 *
 * The eventrocket_components filter provides a means of selectively deactivating
 * modules that are unnecessary or unwanted in a specific case.
 *
 * @todo provide a settings page to make this process easier
 */
$includes = array( 'nosecone', 'shortcodes', 'gps', '404_laser', 'hud' );
$includes = apply_filters( 'eventrocket_components', $includes );

foreach ( $includes as $component ) {
	$path = EVENTROCKET_INC . "/$component.php";
	if ( 0 !== strpos( realpath( $path), EVENTROCKET_INC ) ) continue;
	require_once EVENTROCKET_INC . "/$component.php";
}