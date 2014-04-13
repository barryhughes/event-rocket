<?php
/**
 * Provides additional toolbar items to make it easier to reach specific
 * setting pages.
 */
class EventRocketHUD
{
	/**
	 * The toolbar node used as a container for event settings.
	 */
	const SETTINGS_PARENT = 'tribe-events-settings';

	/**
	 * @var WP_Admin_Bar
	 */
	protected $toolbar;

	/**
	 * List of event settings tabs.
	 *
	 * @var array
	 */
	protected $tabs = array();


	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'toolbar' ) );
		$this->settings_links();
	}

	public function toolbar( WP_Admin_Bar $toolbar ) {
		$this->toolbar = $toolbar;
	}

	protected function settings_links() {
		// Build a list of the available settings tabs
		if ( is_admin() ) add_action( 'admin_init', array( $this, 'settings_get_tabs' ), 20 );
		else add_action( 'init', array( $this, 'settings_get_tabs' ), 20 );

		// Add our links
		add_action( 'wp_before_admin_bar_render', array( $this, 'settings_add_tablinks' ), 20 );
	}

	public function settings_get_tabs() {
		/**
		 * @todo Tribe settings code calls WP API functions that are not yet available: adjust this!
		 *
		 * get_page_templates() which is not yet avaiable will be called by Tribe settings code
		 * (ie, get_page_templates()).
		 */
		if ( ! is_admin() ) {
			TribeEvents::instance()->initOptions();
			do_action( 'tribe_settings_do_tabs' );
		}

		$this->tabs = array_merge(
			(array) apply_filters( 'tribe_settings_all_tabs', array() ),
			(array) apply_filters( 'tribe_settings_no_save_tabs', array() )
		);
	}

	public function settings_add_tablinks() {
		$target = $this->toolbar->get_node( self::SETTINGS_PARENT );
		if ( null === $target ) return;

		foreach ( $this->tabs as $index => $item )
			$this->settings_add_to_group( $index, $item );
	}

	public function settings_add_to_group( $index, $item ) {
		$this->toolbar->add_node( array(
			'id' => $index,
			'title' => $item,
			'parent' => self::SETTINGS_PARENT
		) );
	}
}

new EventRocketHUD;