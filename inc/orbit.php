<?php
defined( 'EVENT_ROCKET_INC' ) or die();

$includes = array( 'nosecone', 'shortcodes', 'gps', '404_laser' );

foreach ( $includes as $component )
	require EVENT_ROCKET_INC . "/$component.php";
