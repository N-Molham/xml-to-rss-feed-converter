<?php
/**
 * RSS2 Feed Template for debug
 *
 * Created by Nabeel
 * Date: 2016-02-11
 * Time: 4:41 AM
 */

$last_build_date = mysql2date( 'D, d M Y H:i:s +0000', $last_build_date, false );
$admin_email     = get_bloginfo( 'admin_email' );


// vars
$fields_map = $feed_info['item'];
$item       = null;

for ( $i = 0, $count = count( $feed_query ); $i < $count; $i ++ ) :
	$item = &$feed_query[ $i ];
	?>
	<div>
		<h2><?php echo xrfc_item_field( 'title', $item, $fields_map ); ?></h2>
		<p><?php echo xrfc_item_field( 'link', $item, $fields_map ); ?></p>
		<p><?php echo xrfc_item_field( 'pubDate', $item, $fields_map, $last_build_date ); ?></p>
		<p><?php echo xrfc_item_field( 'creator', $item, $fields_map, $admin_email . ' (' . get_user_by( 'email', $admin_email )->display_name . ')' ); ?></p>
		<p><?php echo xrfc_item_field( 'guid', $item, $fields_map ); ?></p>
		<p><?php echo xrfc_item_field( 'discount', $item, $fields_map ); ?></p>
		<p><?php echo str_replace( '.', ',', xrfc_item_field( 'oldprice', $item, $fields_map ) ); ?></p>
		<p><?php echo str_replace( '.', ',', xrfc_item_field( 'newprice', $item, $fields_map ) ); ?></p>
		<p><?php echo xrfc_item_field( 'featured_image', $item, $fields_map ); ?></p>
		<p><?php echo xrfc_item_field( 'description', $item, $fields_map ); ?></p>
	</div>
<?php die(); endfor; ?>
