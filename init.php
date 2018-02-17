<?php namespace WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter;

/**
 * Plugin Name: XML to RSS Feed Converter
 * Description: Converts any XML feed to valid RSS feed
 * Version: 1.6.0
 * Author: Nabeel Molham
 * Author URI: http://nabeel.molham.me/
 * Text Domain: xml-to-rss-converter
 * Domain Path: /languages
 * License: GNU General Public License, version 3, http://www.gnu.org/licenses/gpl-3.0.en.html
 */

if ( ! defined( 'WPINC' ) ) {
	// Exit if accessed directly
	die();
}

/**
 * Constants
 */

// plugin master file
define( 'XRFC_MAIN_FILE', __FILE__ );

// plugin DIR
define( 'XRFC_DIR', plugin_dir_path( XRFC_MAIN_FILE ) );

// plugin URI
define( 'XRFC_URI', plugin_dir_url( XRFC_MAIN_FILE ) );

// localization text Domain
define( 'XRFC_DOMAIN', 'xml-to-rss-converter' );

if ( ! defined( 'XRFC_USE_CACHE' ) ) {
	// cache settings
	define( 'XRFC_USE_CACHE', true );
}

require_once XRFC_DIR . 'vendor/autoload.php';
require_once XRFC_DIR . 'includes/classes/Singular.php';
require_once XRFC_DIR . 'includes/helpers.php';
require_once XRFC_DIR . 'includes/functions.php';

/**
 * Plugin main component
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */
class Plugin extends Singular {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.6.0';

	/**
	 * Feed custom post type key name
	 *
	 * @var string
	 */
	public $post_type_feed = 'xrfc_feed';

	/**
	 * Backend
	 *
	 * @var Backend
	 */
	public $backend;

	/**
	 * Backend
	 *
	 * @var Frontend
	 */
	public $frontend;

	/**
	 * Feed
	 *
	 * @var Feed
	 */
	public $feed;

	/**
	 * Backend
	 *
	 * @var Ajax_Handler
	 */
	public $ajax;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	protected function init() {
		// load language files
		add_action( 'plugins_loaded', [ &$this, 'load_language' ] );

		// autoloader register
		spl_autoload_register( [ &$this, 'autoloader' ] );

		// modules
		$this->ajax     = Ajax_Handler::get_instance();
		$this->feed     = Feed::get_instance();
		$this->backend  = Backend::get_instance( $this->feed );
		$this->frontend = Frontend::get_instance( $this->feed );

		register_activation_hook( XRFC_MAIN_FILE, [ &$this, 'plugin_activated' ] );

		// plugin loaded hook
		do_action_ref_array( 'xrfc_loaded', [ &$this ] );
	}

	/**
	 * Load view template
	 *
	 * @param string $view_name
	 * @param array  $args ( optional )
	 *
	 * @return void
	 */
	public function load_view( $view_name, $args = null ) {
		// build view file path
		$__view_name     = $view_name;
		$__template_path = XRFC_DIR . 'views/' . $__view_name . '.php';
		if ( ! file_exists( $__template_path ) ) {
			// file not found!
			wp_die( sprintf( __( 'Template <code>%s</code> File not found, calculated path: <code>%s</code>', XRFC_DOMAIN ), $__view_name, $__template_path ) );
		}

		// clear vars
		unset( $view_name );

		if ( ! empty( $args ) ) {
			// extract passed args into variables
			extract( $args, EXTR_OVERWRITE );
		}

		/**
		 * Before loading template hook
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 */
		do_action_ref_array( 'xrfc_load_template_before', [ &$__template_path, $__view_name, $args ] );

		/**
		 * Loading template file path filter
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 *
		 * @return string
		 */
		require apply_filters( 'xrfc_load_template_path', $__template_path, $__view_name, $args );

		/**
		 * After loading template hook
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 */
		do_action( 'xrfc_load_template_after', $__template_path, $__view_name, $args );
	}

	/**
	 * Language file loading
	 *
	 * @return void
	 */
	public function load_language() {
		load_plugin_textdomain( XRFC_DOMAIN, false, dirname( plugin_basename( XRFC_MAIN_FILE ) ) . '/languages' );
	}

	/**
	 * When plugin activated
	 *
	 * @return void
	 */
	public function plugin_activated() {
		// refresh rewrite rules cache
		flush_rewrite_rules();
	}

	/**
	 * System classes loader
	 *
	 * @param $class_name
	 *
	 * @return void
	 */
	public function autoloader( $class_name ) {
		if ( strpos( $class_name, __NAMESPACE__ ) === false ) {
			// skip non related classes
			return;
		}

		$class_path = XRFC_DIR . 'includes' . DIRECTORY_SEPARATOR . 'classes' . str_replace( [
				__NAMESPACE__,
				'\\',
			], [ '', DIRECTORY_SEPARATOR ], $class_name ) . '.php';

		if ( file_exists( $class_path ) ) {
			// load class file if found
			require_once $class_path;
		}
	}
}

// boot up the system
xml_to_rss_feed_converter();