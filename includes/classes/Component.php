<?php namespace WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter;

/**
 * Base Component
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */
class Component extends Singular {
	/**
	 * Plugin Main Component
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init() {
		// vars
		$this->plugin = Plugin::get_instance();
	}
}
