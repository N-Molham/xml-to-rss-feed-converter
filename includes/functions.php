<?php
/**
 * Created by Nabeel
 * Date: 2016-01-22
 * Time: 2:38 AM
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */

use WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter\Plugin;

if ( ! function_exists( 'xrfc_item_field' ) ):
	/**
	 * Get feed item field value based on fields mapping
	 *
	 * @param string                 $field_name
	 * @param array|SimpleXMLElement $item
	 * @param array                  $fields_map
	 * @param string                 $default_value
	 *
	 * @return string
	 */
	function xrfc_item_field( $field_name, $item, $fields_map, $default_value = '' ) {

		$_field_name = $field_name;
		$field_name  = isset( $fields_map[ $field_name ] ) ? $fields_map[ $field_name ] : $field_name;

		if ( is_array( $item ) ) {

			$field_value = isset( $item[ $field_name ] ) ? $item[ $field_name ] : $default_value;

			if ( 'description' === $field_name ) {
				$_field_name = 'excerpt';
				foreach ( $item as $item_field_name => $item_field_value ) {
					// append xml full data
					$field_value .= '<p><strong>' . $item_field_name . '</strong>: ' . $item_field_value . '</p>';
				}
			}

		} else {

			if ( ! empty( $field_name ) ) {

				$field_query = $item->xpath( ltrim( $field_name, '/\\' ) );
				if ( false !== $field_name && count( $field_query ) ) {
					$field_value = (string) array_pop( $field_query );
				}

			}

		}

		return apply_filters( 'the_' . $_field_name . '_rss', $field_value, 'rss2' );
	}
endif;

if ( ! function_exists( 'xml_to_rss_feed_converter' ) ):
	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	function xml_to_rss_feed_converter() {
		return Plugin::get_instance();
	}
endif;

if ( ! function_exists( 'xrfc_view' ) ):
	/**
	 * Load view
	 *
	 * @param string $view_name
	 * @param array  $args
	 *
	 * @return void
	 */
	function xrfc_view( $view_name, $args = null ) {
		xml_to_rss_feed_converter()->load_view( $view_name, $args );
	}
endif;

if ( ! function_exists( 'xrfc_version' ) ):
	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	function xrfc_version() {
		xml_to_rss_feed_converter()->version;
	}
endif;