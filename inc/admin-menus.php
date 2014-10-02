<?php
defined( 'ABSPATH' ) or exit();


/**
 * Provides additional toolbar items to make it easier to reach specific
 * setting pages.
 */
class EventRocket_AdminMenus
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
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
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
		$this->settings_frontend_hack();

		do_action( 'tribe_settings_do_tabs' );
		$this->tabs = (array) apply_filters( 'tribe_settings_all_tabs', array() );
	}

	/**
	 * Outside of the admin environment we need to do a few tricks in order to
	 * load the list of tabs.
	 */
	protected function settings_frontend_hack() {
		if ( is_admin() ) return;
		$this->community_compatibility_fix();
		require_once(ABSPATH . 'wp-admin/includes/theme.php');
		TribeEvents::instance()->initOptions();
	}

	/**
	 * Ensure compatibility with Community Events, should it be installed.
	 */
	protected function community_compatibility_fix() {
		if ( ! class_exists( 'TribeCommunityEvents' ) ) return;
		require_once(ABSPATH . 'wp-admin/includes/user.php');
	}

	public function settings_add_tablinks() {
		$target = $this->toolbar->get_node( self::SETTINGS_PARENT );
		if ( null === $target ) return;

		asort( $this->tabs );

		foreach ( $this->tabs as $index => $item )
			$this->settings_add_to_group( $index, $item );
	}

	public function settings_add_to_group( $index, $item ) {
		if ( ! class_exists( 'TribeSettings' ) ) return;
		$settings = TribeSettings::instance();

		$query = array( 'page' => $settings->adminSlug, 'tab' => $index, 'post_type' => TribeEvents::POSTTYPE );
		$url = apply_filters( 'tribe_settings_url', add_query_arg( $query, admin_url( 'edit.php' ) ) );

		$this->toolbar->add_node( array(
			'id' => $index,
			'title' => $item,
			'parent' => self::SETTINGS_PARENT,
			'href' => $url
		) );
	}
}

new EventRocket_AdminMenus;