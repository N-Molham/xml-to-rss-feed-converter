<?php namespace WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter;

use SimpleXMLElement;
use WP_Error;
use WP_Post;

/**
 * URL Feed logic
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */
class Feed extends Component {
	/**
	 * Feed post type name
	 *
	 * @var string
	 */
	public $post_type = 'xrfc_feed';

	/**
	 * Feed info field name
	 *
	 * @var string
	 */
	public $feed_meta_key = 'xrfc_info';

	/**
	 * Fetch feed URL for results
	 *
	 * @param int    $feed_id
	 * @param bool   $use_cache
	 * @param string $output OBJECT or ARRAY_A or ARRAY_N
	 *
	 * @return SimpleXMLElement|array|WP_Error
	 */
	public function fetch_feed( $feed_id, $use_cache = true, $output = OBJECT ) {
		global $wpdb;

		$feed_info = $this->get_feed_info( $feed_id );
		if ( empty( $feed_info['feed_url'] ) ) {
			// no URL found in feed info
			return new WP_Error( 'feed_url', __( 'Empty feed URL', XRFC_DOMAIN ) );
		}

		/**
		 * Filter loaded feed post object
		 *
		 * @param WP_Post $feed_post
		 * @param int     $feed_id
		 *
		 * @return WP_Post
		 */
		$feed_post = apply_filters( 'xrfc_feed_post', get_post( $feed_id ), $feed_id );

		/**
		 * Filter loaded feed post object
		 *
		 * @param bool $use_cache
		 * @param int  $feed_id
		 *
		 * @return bool
		 */
		$use_cache = (bool) apply_filters( 'xrfc_load_feed_from_cache', $use_cache, $feed_id );

		// check if content needs update or not
		$no_need_for_update = get_post_modified_time( 'U', false, $feed_post, true ) + 3600 >= current_time( 'timestamp' );

		if ( true === $use_cache && ! empty( $feed_post->post_content ) && $no_need_for_update ) {
			// load from previous loaded content
			$feed_content = $feed_post->post_content;
		} else {
			// fetch feed content
			$fetch_request = wp_remote_get( $feed_info['feed_url'] );
			if ( is_wp_error( $fetch_request ) ) {
				// error fetching URL content
				return $fetch_request;
			}

			// sanitize content feed
			// $feed_content = sanitize_post_field( 'post_content', $fetch_request['body'], $feed_id, 'db' );
			$feed_content = wp_remote_retrieve_body( $fetch_request );

			// UTF-8 convert
			$feed_content = @mb_convert_encoding( $feed_content, 'UTF-8', empty( $feed_info['feed_encoding'] ) ? null : strtoupper( $feed_info['feed_encoding'] ) );

			// save content
			$wpdb->update( $wpdb->posts, [
				'post_content'      => $feed_content,
				'post_modified'     => current_time( 'mysql' ),
				'post_modified_gmt' => current_time( 'mysql', 1 ),
			], [ 'ID' => $feed_id ], [ '%s', '%s', '%s' ], [ '%d' ] );
		}

		// XML initialization
		$feed_xml = simplexml_load_string( $feed_content );
		if ( false === $feed_xml ) {
			// vars
			$parse_error   = libxml_get_last_error();
			$error_message = __( 'Unable to parse feed content', XRFC_DOMAIN );

			if ( $parse_error ) {
				// append XML parse error message
				$error_message .= '<br/>Error: "<strong><code>' . $parse_error->message . '</code></strong>"<br/>';
				$error_message .= '<code>' . esc_html( $feed_content ) . '</code>';
			}

			// content parse error
			return new WP_Error( 'feed_xml_parse_error', $error_message );
		}

		if ( ! empty( $feed_info['xpath'] ) ) {
			// query XML with given XPath selector
			$feed_xml = $feed_xml->xpath( $feed_info['xpath'] );
			if ( false === $feed_xml || ! isset( $feed_xml[0] ) ) {
				// query returned no results
				return new WP_Error( 'no_result', __( 'No results found using the XPath', XRFC_DOMAIN ) );
			}
		}

		if ( OBJECT !== $output ) {
			// vars
			$feed_length = count( $feed_xml );
			$feed_xml    = (array) $feed_xml;

			if ( ARRAY_N === $output ) {
				// convert to plain numeric array
				for ( $i = 0; $i < $feed_length; $i ++ ) {
					$feed_xml[ $i ] = array_map( 'WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter\Helpers::to_string', array_values( (array) $feed_xml[ $i ] ) );
				}
			} elseif ( ARRAY_A === $output ) {
				// convert to plain associative array
				for ( $i = 0; $i < $feed_length; $i ++ ) {
					$feed_xml[ $i ] = array_map( 'WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter\Helpers::to_string', get_object_vars( $feed_xml[ $i ] ) );
				}
			}
		}

		/**
		 * Filter feed fetched data
		 *
		 * @param SimpleXMLElement|array $feed_xml
		 * @param array                  $feed_info
		 * @param int                    $feed_id
		 *
		 * @return SimpleXMLElement|array
		 */
		return apply_filters( 'xrfc_feed_fetch', $feed_xml, $feed_info, $feed_id );
	}

	/**
	 * Check whether the given post is feed
	 *
	 * @param int|WP_Post $feed_id
	 *
	 * @return bool
	 */
	public function is_feed( $feed_id ) {
		return $this->post_type === get_post_type( $feed_id );
	}

	/**
	 * Save feed information into meta
	 *
	 * @param int   $feed_id
	 * @param array $new_info
	 *
	 * @return void
	 */
	public function save_feed_info( $feed_id, $new_info ) {
		/**
		 * Filter feed's new information before saving
		 *
		 * @param array $new_info
		 * @param int   $feed_id
		 *
		 * @return array|bool false to stop saving the info
		 */
		$new_info = apply_filters( 'xrfc_save_feed_info', wp_parse_args( $new_info, $this->feed_info_defaults() ), $feed_id );
		if ( false === $new_info ) {
			// do not save feed's new info
			return;
		}

		update_post_meta( $feed_id, $this->feed_meta_key, $new_info );
	}

	/**
	 * Get feed's information
	 *
	 * @param int $feed_id
	 *
	 * @return array
	 */
	public function get_feed_info( $feed_id ) {
		return wp_parse_args( get_post_meta( $feed_id, $this->feed_meta_key, true ), $this->feed_info_defaults() );
	}

	/**
	 * Get feed info default values
	 *
	 * @return array
	 */
	public function feed_info_defaults() {
		return [
			'feed_url'      => '',
			'feed_encoding' => '',
			'xpath'         => '',
			'rss'           => [
				'encoding'         => 'utf-8',
				'title'            => '',
				'description'      => '',
				'language'         => 'en-US',
				'update_period'    => 'daily',
				'update_frequency' => '1',
			],
			'item'          => [
				'title'          => '',
				'link'           => '',
				'pubDate'        => '',
				'creator'        => '',
				'guid'           => '',
				'discount'       => '',
				'oldprice'       => '',
				'newprice'       => '',
				'featured_image' => '',
				'description'    => '',
				'use_attribute'  => 'no',
				'attribute_name' => '',
				'column_name'    => '',
			],
		];
	}
}
