<?php
/**
 * RSS2 Feed Template
 *
 * Created by Nabeel
 * Date: 2016-02-11
 * Time: 4:41 AM
 */
echo '<?xml version="1.0" encoding="' . $feed_info['rss']['encoding'] . '"?' . '>';

// vars
$last_build_date = mysql2date( 'D, d M Y H:i:s +0000', $last_build_date, false );
$admin_email     = get_bloginfo( 'admin_email' );

/**
 * Fires between the xml and rss tags in a feed.
 */
do_action( 'xrfc_rss2_tag_pre' );
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 */
	do_action( 'xrfc_rss2_ns' );
	?>>

	<channel>
		<title><![CDATA[<?php echo $feed_info['rss']['title']; ?>]]></title>
		<link><![CDATA[<?php self_link(); ?>]]></link>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<description><![CDATA[<?php echo $feed_info['rss']['description']; ?>]]></description>
		<lastBuildDate><?php echo $last_build_date; ?></lastBuildDate>
		<language><?php echo $feed_info['rss']['language']; ?></language>
		<sy:updatePeriod><?php echo $feed_info['rss']['update_period']; ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo $feed_info['rss']['update_frequency']; ?></sy:updateFrequency>
		<?php
		/**
		 * Fires at the end of the RSS2 Feed Header.
		 */
		do_action( 'xrfc_rss2_head' );

		// vars
		$fields_map = &$feed_info['item'];
		$item       = null;

		for ( $i = 0, $count = count( $feed_query ); $i < $count; $i++ ):
			$item = &$feed_query[ $i ];
			?>
			<item>
				<title><![CDATA[<?php echo xrfc_item_field( 'title', $item, $fields_map ); ?>]]></title>
				<link><![CDATA[<?php echo xrfc_item_field( 'link', $item, $fields_map ); ?>]]></link>
				<pubDate><?php echo xrfc_item_field( 'pubDate', $item, $fields_map, $last_build_date ); ?></pubDate>
				<author><![CDATA[<?php echo xrfc_item_field( 'creator', $item, $fields_map, $admin_email . ' (' . get_user_by( 'email', $admin_email )->display_name . ')' ); ?>]]></author>
				<guid isPermaLink="false"><![CDATA[<?php echo xrfc_item_field( 'guid', $item, $fields_map ); ?>]]></guid>
				<discount><![CDATA[<?php echo xrfc_item_field( 'discount', $item, $fields_map ); ?>]]></discount>
				<comments><![CDATA[<?php echo str_replace('.', ',', xrfc_item_field( 'oldprice', $item, $fields_map )); ?>]]></comments>
				<category><![CDATA[<?php echo str_replace('.', ',', xrfc_item_field( 'newprice', $item, $fields_map )); ?>]]></category>
				<image><![CDATA[<?php echo xrfc_item_field( 'featured_image', $item, $fields_map ); ?>]]></image>
				<description><![CDATA[<?php echo xrfc_item_field( 'description', $item, $fields_map ); ?>]]></description>
				<?php rss_enclosure(); ?>
				<?php
				/**
				 * Fires at the end of each RSS2 feed item.
				 */
				do_action( 'xrfc_rss2_item' );
				?>
			</item>
		<?php endfor; ?>
	</channel>
</rss>
