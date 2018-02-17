<?php namespace WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter;

use SimpleXMLElement;

/**
 * Frontend logic
 *
 * @package WP_Plugins\PretParkDeals\XML_RSS_Feed_Converter
 */
class Frontend extends Component
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

		// WP initialization
		add_action( 'init', [ &$this, 'register_rewrite_rule' ] );
		add_action( 'template_redirect', [ &$this, 'handle_xml_rss_feed' ] );

		// Query vars
		add_filter( 'query_vars', [ &$this, 'add_feed_query_var' ] );
	}

	/**
	 * Handle XML RSS feed
	 *
	 * @return void
	 */
	public function handle_xml_rss_feed()
	{
		$feed_id = get_query_var( 'xrfc_feed', null );
		if ( empty( $feed_id ) || !$this->feed->is_feed( $feed_id ) )
		{
			// skip non related
			return;
		}

		// vars
		$feed_info       = $this->feed->get_feed_info( $feed_id );
		$feed_query      = $this->feed->fetch_feed( $feed_id, XRFC_USE_CACHE, 'yes' === $feed_info['item']['use_attribute'] ? OBJECT : ARRAY_A );
		$last_build_date = get_post_field( 'post_modified_gmt', $feed_id );

		if ( is_wp_error( $feed_query ) )
		{
			// error happened
			wp_die( $feed_query->get_error_message(), $feed_query->get_error_code() );
			die();
		}

		if ( !is_array( $feed_query ) )
		{
			// result query items not set
			wp_die( __( 'XPath query not set.', XRFC_DOMAIN ) );
			die();
		}

		if ( 'yes' === $feed_info['item']['use_attribute'] )
		{
			// parse using attributes instead

			// vars
			$column_value   = null;
			$item           = null;
			$feed_item      = [ ];
			$output         = [ ];
			$column_name    = $feed_info['item']['column_name'];
			$attribute_name = $feed_info['item']['attribute_name'];

			for ( $i = 0, $length = count( $feed_query ); $i < $length; $i++ )
			{
				/* @var $item SimpleXMLElement */
				$item      = &$feed_query[ $i ];
				$feed_item = [ ];

				/* @var $column SimpleXMLElement */
				foreach ( $item->$column_name as $column )
				{
					// columns attributes list
					foreach ( $column->attributes() as $col_attr_name => $col_attr_value )
					{
						$col_attr_value = (string) $col_attr_value;
						if ( $attribute_name === $col_attr_name )
						{
							// target attribute to get it's value
							$feed_item[ $col_attr_value ] = (string) $column;
							break;
						}
					}
					unset( $col_attr_name, $col_attr_value );
				}

				$output[] = $feed_item;
			}

			// switch query items with new ones
			$feed_query = $output;
			unset( $output, $feed_item );
		}
		
		// header
		header( 'Content-Type: application/rss+xml; charset=' . get_option( 'blog_charset' ), true );

		xrfc_view( 'rss2_feed', compact( 'feed_id', 'feed_info', 'feed_query', 'last_build_date' ) );
		die();
	}

	/**
	 * Add XML RSS feed query variable
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_feed_query_var( $vars )
	{
		$vars[] = 'xrfc_feed';

		return $vars;
	}

	/**
	 * Register new rule regex for the generated RSS feed
	 *
	 * @return void
	 */
	public function register_rewrite_rule()
	{
		// XML-RSS feed URL
		add_rewrite_rule( '^rss-feed/([0-9]+)/?', 'index.php?xrfc_feed=$matches[1]', 'top' );
	}
}
