<?php namespace WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter;

use Symfony\Component\VarDumper\VarDumper;
use WP_Post;

/**
 * Backend logic
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */
class Backend extends Component
{
	/**
	 * Feed
	 *
	 * @var Feed
	 */
	protected $feed;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		// vars
		$this->feed = func_get_arg( 0 );

		// WP initialization hook
		add_action( 'init', [ &$this, 'register_post_type' ], 0 );

		// Preview Feed fetch
		add_action( 'admin_action_xrfc_feed_preview', [ &$this, 'preview_feed_fetch' ] );

		// meta boxes registration hook
		add_action( 'add_meta_boxes', [ &$this, 'register_meta_boxes' ] );

		// post save data => "custom post type" hook
		add_action( 'save_post_' . $this->feed->post_type, [ &$this, 'save_feed_info' ] );
	}

	/**
	 * Preview feed URL fetched data
	 *
	 * @return void
	 */
	public function preview_feed_fetch()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			// skip non-privileged users
			return;
		}

		$feed_id = filter_input( INPUT_GET, 'feed_id', FILTER_SANITIZE_NUMBER_INT );
		if ( '' === $feed_id || null === get_post( $feed_id ) )
		{
			// skip invalid feed ID
			return;
		}

		$fetch = $this->feed->fetch_feed( $feed_id, false );
		if ( is_wp_error( $fetch ) )
		{
			// something wrong
			wp_die( $fetch->get_error_message(), $fetch->get_error_code() );
		}

		// pretty XML formatting
		VarDumper::dump( $fetch );
		die();
	}

	/**
	 * Save feed information fields
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function save_feed_info( $post_id )
	{
		$new_info = filter_input( INPUT_POST, $this->feed->feed_meta_key, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		if ( empty( $new_info ) )
		{
			// skip empty posting
			return;
		}

		$this->feed->save_feed_info( $post_id, $new_info );
	}

	/**
	 * Register feed meta boxes
	 *
	 * @param string $post_type
	 *
	 * @return void
	 */
	public function register_meta_boxes( $post_type = '' )
	{
		if ( $this->feed->post_type !== $post_type )
		{
			// skip non-related post types
			return;
		}

		add_meta_box( 'xrfc_feed_info', __( 'Feed Information', XRFC_DOMAIN ), [ &$this, 'load_meta_box' ], $post_type, 'advanced', 'high' );
	}

	/**
	 * Load given meta box
	 *
	 * @param WP_Post $post
	 * @param array   $meta_box
	 *
	 * @return void
	 */
	public function load_meta_box( $post, $meta_box )
	{
		xrfc_view( 'meta_boxes/' . str_replace( 'xrfc_', '', $meta_box['id'] ), [
			'feed_id'      => $post->ID,
			'field_values' => $this->feed->get_feed_info( $post->ID ),
			'field_name'   => $this->feed->feed_meta_key,
		] );
	}

	/**
	 * Register Custom Post Type
	 *
	 * @return void
	 */
	function register_post_type()
	{
		$args = [
			'label'               => __( 'Feed', XRFC_DOMAIN ),
			'description'         => __( 'XML Feed', XRFC_DOMAIN ),
			'labels'              => [
				'name'                  => _x( 'Feeds', 'Post Type General Name', XRFC_DOMAIN ),
				'singular_name'         => _x( 'Feed', 'Post Type Singular Name', XRFC_DOMAIN ),
				'menu_name'             => __( 'Feed', XRFC_DOMAIN ),
				'name_admin_bar'        => __( 'Post Type', XRFC_DOMAIN ),
				'archives'              => __( 'Feeds Archives', XRFC_DOMAIN ),
				'parent_item_colon'     => __( 'Parent Feed:', XRFC_DOMAIN ),
				'all_items'             => __( 'All Feeds', XRFC_DOMAIN ),
				'add_new_item'          => __( 'Add New Feed', XRFC_DOMAIN ),
				'add_new'               => __( 'Add New', XRFC_DOMAIN ),
				'new_item'              => __( 'New feed', XRFC_DOMAIN ),
				'edit_item'             => __( 'Edit feed', XRFC_DOMAIN ),
				'update_item'           => __( 'Update Feed', XRFC_DOMAIN ),
				'view_item'             => __( 'View Feed', XRFC_DOMAIN ),
				'search_items'          => __( 'Search Feed', XRFC_DOMAIN ),
				'not_found'             => __( 'Not found', XRFC_DOMAIN ),
				'not_found_in_trash'    => __( 'Not found in Trash', XRFC_DOMAIN ),
				'featured_image'        => __( 'Featured Image', XRFC_DOMAIN ),
				'set_featured_image'    => __( 'Set featured image', XRFC_DOMAIN ),
				'remove_featured_image' => __( 'Remove featured image', XRFC_DOMAIN ),
				'use_featured_image'    => __( 'Use as featured image', XRFC_DOMAIN ),
				'insert_into_item'      => __( 'Insert into feed', XRFC_DOMAIN ),
				'uploaded_to_this_item' => __( 'Uploaded to this feed', XRFC_DOMAIN ),
				'items_list'            => __( 'Feeds list', XRFC_DOMAIN ),
				'items_list_navigation' => __( 'Feeds list navigation', XRFC_DOMAIN ),
				'filter_items_list'     => __( 'Filter Feeds list', XRFC_DOMAIN ),
			],
			'supports'            => [ 'title' ],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-rss',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		];

		register_post_type( $this->feed->post_type, $args );
	}
}
