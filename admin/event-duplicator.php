<?php
class EventRocket_EventDuplicator
{
	const POST_CREATION_SUCCESSFUL = 100;
	const POST_CREATION_WARNING    = 200;
	const POST_CREATION_FAILED     = 900;

	protected $src_post;
	protected $duplicate;
	protected $status = array();

	/** @var EventRocket_EventDuplicatorFilters */
	public $filters;

	/** @var  EventRocket_EventDuplicatorUI */
	public $ui;


	public function __construct() {
		$this->setup_objects();
		$this->setup_hooks();
	}

	protected function setup_objects() {
		$this->filters = new EventRocket_EventDuplicatorFilters;
		$this->ui = new EventRocket_EventDuplicatorUI;
	}

	protected function setup_hooks() {
		add_action( 'admin_init', array( $this, 'listener' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
		add_filter( 'post_row_actions', array( $this, 'add_duplicate_action'), 20, 2 );
	}

	public function add_duplicate_action( $actions, $post) {
		// Not a post? Bail (we aren't using a typehint since some plugins may pass in something a stdClass object etc)
		if ( ! is_a( $post, 'WP_Post' ) )
			return $actions;

		// Not an event? Don't add the link
		if ( Tribe__Events__Main::POSTTYPE !== $post->post_type )
			return $actions;

		// Recurring event? Don't add the link either!
		if ( function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $post->ID ) )
			return $actions;

		// Form the link
		$url   = $this->duplication_link_url( $post->ID );
		$text  = __( 'Duplicate', 'eventrocket' );
		$date  = tribe_get_start_date( $post->ID, false, DateTime::ISO8601 );
		$title = $this->get_duplicate_post_title( $post );

		$link = '<a href="'. $url . '" class="eventrocket_duplicate" '
		      . 'data-date="' . $date . '" '
		      . 'data-title="' . $title . '" '
		      . '>' . $text . '</a>';

		// Add to the list of actions
		$actions['duplicate'] = $link;
		return $actions;
	}

	protected function duplication_link_url( $post_id ) {
		$url = get_admin_url( null, 'edit.php?' . http_build_query( array(
			'post_type'       => Tribe__Events__Main::POSTTYPE,
			'duplicate_event' => absint( $post_id ),
		) ) );

		return wp_nonce_url( $url, 'eventrocket_duplicate_' . $post_id, '_check' );
	}

	public function listener() {
		global $pagenow;
		$this->cookie_statuses();

		if ( 'edit.php' !== $pagenow || Tribe__Events__Main::POSTTYPE !== @$_GET['post_type'] ) return;
		if ( ! isset( $_GET['duplicate_event'] ) ) return;
		if ( ! wp_verify_nonce( @$_GET['_check'], 'eventrocket_duplicate_' . $_GET['duplicate_event'] ) ) return;

		$this->src_post = get_post( $_GET['duplicate_event'] );
		if ( Tribe__Events__Main::POSTTYPE !== $this->src_post->post_type ) return;

		$this->duplicate();
	}

	protected function cookie_statuses() {
		// Check for status messages conveyed via cookies
		if ( isset( $_COOKIE['eventrocket_dup_status'] ) && ! empty( $_COOKIE['eventrocket_dup_status'] ) ) {
			$information     = (array) json_decode( stripslashes( $_COOKIE['eventrocket_dup_status'] ) );
			$this->status    = isset( $information['status'] ) ? (int) $information['status'] : 0;
			$this->duplicate = isset( $information['event'] )  ? (int) $information['event']  : 0;
			setcookie( 'eventrocket_dup_status', 0 );
		}
	}

	protected function duplicate() {
		$post_data = (array) $this->src_post;
		$post_meta = get_post_meta( $this->src_post->ID );

		$post_data['post_status'] = apply_filters( 'eventrocket_duplicated_post_status', 'draft' );
		$post_data['post_title'] = $this->get_duplicate_post_title( get_post( $this->src_post->ID ) );
		$post_data = (array) apply_filters( 'eventrocket_duplicated_post_data', $post_data, $this->src_post );

		unset( $post_data['ID'] );
		add_filter( 'wp_insert_post_empty_content', '__return_false' );
		$this->duplicate = wp_insert_post( $post_data );

		if ( ! $this->duplicate  || is_wp_error( $this->duplicate ) ) {
			$this->status = self::POST_CREATION_FAILED;
			return;
		}

		$post_meta = (array) apply_filters( 'eventrocket_duplicated_post_meta', $post_meta, $this->src_post, $this->duplicate );

		foreach ( $post_meta as $key => $value ) {
			$value = (array) $value;
			foreach ( $value as $meta_entry ) {
				$meta_entry = $this->smart_unserialize( $meta_entry );
				update_post_meta( $this->duplicate, $key, $meta_entry );
			}
		}

		$this->apply_terms();

		$this->status = self::POST_CREATION_SUCCESSFUL;
		$this->redirect();
	}

	protected function smart_unserialize( $value ) {
		if ( ! is_string( $value ) ) return $value;
		$data = @unserialize( $value );
		return ( false !== $data ) ? $data : $value;
	}

	protected function get_duplicate_post_title( $post ) {
		$default = __( 'Copy of %s', 'eventrocket' );
		$template = apply_filters( 'eventrocket_duplicated_post_title_template', $default, $post );
		return sprintf( $template, $post->post_title );
	}

	/**
	 * Applies any taxonomy terms applied to the source post to the destination post.
	 */
	protected function apply_terms() {
		foreach ( $this->event_taxonomies() as $taxonomy ) {
			foreach ( wp_get_post_terms( $this->src_post->ID, $taxonomy ) as $term ) {
				wp_set_object_terms( $this->duplicate, $term->term_id, $taxonomy, true );
			}
		}
	}

	/**
	 * Returns a list of all taxonomies that can currently be used with events.
	 *
	 * @return array
	 */
	protected function event_taxonomies() {
		$taxonomies = array();

		foreach ( get_taxonomies() as $taxonomy ) {
			$taxonomy = get_taxonomy( $taxonomy );
			if ( in_array( Tribe__Events__Main::POSTTYPE, $taxonomy->object_type ) ) {
				$taxonomies[] = $taxonomy->name;
			}
		}

		return $taxonomies;
	}

	public function notices() {
		if ( self::POST_CREATION_SUCCESSFUL === $this->status ) {
			$edit = get_admin_url( null, 'post.php?post=' . $this->duplicate . '&action=edit' );
			$view = get_permalink( $this->duplicate );
			echo '<div class="updated"> <p> '
				. sprintf( __('Event successfully duplicated! <br/> <a href="%s">Edit</a> | <a href="%s">View</a>', 'eventrocket' ), $edit, $view )
				. '</p> </div>';
		}

		elseif ( self::POST_CREATION_WARNING === $this->status ) {
			$edit = get_admin_url( null, 'post.php?post=' . $this->duplicate . '&action=edit' );
			$view = get_permalink( $this->duplicate );
			echo '<div class="error"> <p>'
				. sprintf( __( 'Event was duplicated but something went wrong! <br/> <a href="%s">Edit</a> | <a href="%s">View</a>', 'eventrocket' ), $edit, $view )
				. '</p> </div>';
		}

		elseif ( self::POST_CREATION_FAILED === $this->status ) {
			echo '<div class="error"> <p>'
				. __( 'Sorry! The event could not be duplicated. Please try again or speak to your administrator or developer for further assistance.', 'eventrocket' )
				. '</p> </div>';
		}
	}

	protected function redirect() {
		$sendback = remove_query_arg( array(), wp_get_referer() );
		setcookie( 'eventrocket_dup_status', json_encode( array(
			'status' => (int) $this->status,
			'event'  => (int) $this->duplicate
		) ) );
		exit( wp_safe_redirect( $sendback ) );
	}
}


function eventrocket_duplicator() {
	static $duplicator = null;
	if ( null === $duplicator ) $duplicator = new EventRocket_EventDuplicator;
	return $duplicator;
}

add_action( 'init', 'eventrocket_duplicator' );