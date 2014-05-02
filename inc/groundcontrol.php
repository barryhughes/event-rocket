<?php
defined( 'EVENTROCKET_INC' ) or die();

/**
 * Manages the list of rocket modules that will be loaded.
 */
class EventRocketModules
{
	protected static $modules = array(
		'nosecone',
		'shortcodes',
		'gps',
		'404_laser',
		'hud'
	);


	public static function enable( $module ) {
		if ( ! in_array( $module, self::$modules ) ) self::$modules[] = $module;
	}

	public static function disable( $module ) {
		foreach ( self::$modules as $key => $name )
			if ( $module === $name ) $index = $key;

		if ( isset( $index ) ) unset( self::$modules[$index] );
	}

	public static function load() {
		$includes = apply_filters( 'eventrocket_components', self::$modules );

		foreach ( $includes as $component ) {
			$path = EVENTROCKET_INC . "/$component.php";
			if ( 0 !== strpos( realpath( $path), EVENTROCKET_INC ) ) continue;
			require_once EVENTROCKET_INC . "/$component.php";
		}
	}
}

do_action( 'eventrocket_modules_preload' );
EventRocketModules::load();