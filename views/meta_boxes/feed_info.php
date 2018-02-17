<?php
/**
 * Created by Nabeel
 * Date: 2016-02-10
 * Time: 11:08 PM
 */
?>
<h3><?php _e( 'Fetch Details', XRFC_DOMAIN ); ?></h3>
<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="feed-url"><?php _e( 'Feed URL', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="url" id="feed-url" name="<?php echo esc_attr( $field_name ); ?>[feed_url]" class="code large-text" value="<?php echo esc_attr( $field_values['feed_url'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="feed-encoding"><?php _e( 'Feed Encoding', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="feed-encoding" name="<?php echo esc_attr( $field_name ); ?>[feed_encoding]" class="code" value="<?php echo esc_attr( $field_values['feed_encoding'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="feed-path"><?php _e( 'Item/Article XPath', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="feed-path" name="<?php echo esc_attr( $field_name ); ?>[xpath]" class="code large-text" value="<?php echo esc_attr( $field_values['xpath'] ) ?>" />
			<p class="description">
				<a href="http://www.w3schools.com/xml/xml_xpath.asp" target="_blank">
					<?php _e( 'XPath (the XML Path language) is a language for finding information in an XML document.', XRFC_DOMAIN ) ?>
				</a>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Preview', XRFC_DOMAIN ) ?></th>
		<td>
			<a href="<?php echo add_query_arg( [ 'action' => 'xrfc_feed_preview', 'feed_id' => $feed_id ], admin_url() ) ?>"
			   target="_blank" class="button" <?php disabled( true, empty( $field_values['feed_url'] ) ); ?>>
				<?php _e( 'Fetch a Preview', XRFC_DOMAIN ) ?>
			</a>
		</td>
	</tr>
	</tbody>
</table>

<hr />

<h3><?php _e( 'Generated RSS Feed Details', XRFC_DOMAIN ); ?></h3>
<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="rss-feed-url"><?php _e( 'RSS Feed URL', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-feed-url" class="large-text code" readonly="readonly" value="<?php echo esc_attr( home_url( 'rss-feed/' . $feed_id . '/' ) ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-encoding"><?php _e( 'Encoding', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-encoding" name="<?php echo esc_attr( $field_name ); ?>[rss][encoding]" class="code" value="<?php echo esc_attr( $field_values['rss']['encoding'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-title"><?php _e( 'Title', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-title" name="<?php echo esc_attr( $field_name ); ?>[rss][title]" class="large-text" value="<?php echo esc_attr( $field_values['rss']['title'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-description"><?php _e( 'Description', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-description" name="<?php echo esc_attr( $field_name ); ?>[rss][description]" class="large-text" value="<?php echo esc_attr( $field_values['rss']['description'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-language"><?php _e( 'Language', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-language" name="<?php echo esc_attr( $field_name ); ?>[rss][language]" class="code" value="<?php echo esc_attr( $field_values['rss']['language'] ) ?>" />
			<span class="description">
				<a href="https://msdn.microsoft.com/en-us/library/ee825488%28v=cs.20%29.aspx?f=255&MSPPError=-2147217396" target="_blank"><?php _e( 'Table of Language Culture Names, Codes', XRFC_DOMAIN ); ?></a>
			</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-update-period"><?php _e( 'Update Period', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-update-period" name="<?php echo esc_attr( $field_name ); ?>[rss][update_period]" class="code" value="<?php echo esc_attr( $field_values['rss']['update_period'] ) ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-update-frequency"><?php _e( 'Update Frequency', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="number" id="rss-update-frequency" name="<?php echo esc_attr( $field_name ); ?>[rss][update_frequency]" class="code small-text" value="<?php echo esc_attr( $field_values['rss']['update_frequency'] ) ?>" />
		</td>
	</tr>
	</tbody>
</table>

<hr>

<h3><?php _e( 'Generated RSS Feed Item Details', XRFC_DOMAIN ); ?></h3>
<p class="description"><?php _e( 'XML to feed item fields Mapping', XRFC_DOMAIN ) ?></p>
<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="rss-item-title"><?php _e( 'Title', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-title" name="<?php echo esc_attr( $field_name ); ?>[item][title]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['title'] ) ?>" />
			<span class="description"><?php _e( 'The title of the item', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-link"><?php _e( 'Link', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-link" name="<?php echo esc_attr( $field_name ); ?>[item][link]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['link'] ) ?>" />
			<span class="description"><?php _e( 'The URL of the item', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-pubDate"><?php _e( 'Published Date', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-pubDate" name="<?php echo esc_attr( $field_name ); ?>[item][pubDate]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['pubDate'] ) ?>" />
			<span class="description"><?php _e( 'Indicates when the item was published', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-creator"><?php _e( 'Creator/Author', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-creator" name="<?php echo esc_attr( $field_name ); ?>[item][creator]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['creator'] ) ?>" />
			<span class="description"><?php _e( 'Email address of the author of the item', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-guid"><?php _e( 'GUID', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-guid" name="<?php echo esc_attr( $field_name ); ?>[item][guid]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['guid'] ) ?>" />
			<span class="description"><?php _e( 'A string that uniquely identifies the item', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-discount"><?php _e( 'discount', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-discount" name="<?php echo esc_attr( $field_name ); ?>[item][discount]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['discount'] ) ?>" />
			<span class="description"><?php _e( 'Discount', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-oldprice"><?php _e( 'oldprice', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-oldprice" name="<?php echo esc_attr( $field_name ); ?>[item][oldprice]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['oldprice'] ) ?>" />
			<span class="description"><?php _e( 'Original price', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-newprice"><?php _e( 'newprice', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-newprice" name="<?php echo esc_attr( $field_name ); ?>[item][newprice]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['newprice'] ) ?>" />
			<span class="description"><?php _e( 'New price', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-featured_image"><?php _e( 'featured_image', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-featured_image" name="<?php echo esc_attr( $field_name ); ?>[item][featured_image]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['featured_image'] ) ?>" />
			<span class="description"><?php _e( 'Featured image', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-description"><?php _e( 'Description', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-description" name="<?php echo esc_attr( $field_name ); ?>[item][description]" class="regular-text code" value="<?php echo esc_attr( $field_values['item']['description'] ) ?>" />
			<span class="description"><?php _e( 'The item synopsis', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label><?php _e( 'Use Attribute', XRFC_DOMAIN ) ?></label></th>
		<td><?php $use_attribute = isset( $field_values['item']['use_attribute'] ) ? $field_values['item']['use_attribute'] : 'no'; ?>
			<fieldset><legend class="screen-reader-text"><span><?php _e( 'Use Attribute', XRFC_DOMAIN ) ?></span></legend>
				<label><input type="radio" name="<?php echo esc_attr( $field_name ); ?>[item][use_attribute]" value="yes"<?php checked( 'yes', $use_attribute ) ?> /> <?php _e( 'Yes', XRFC_DOMAIN ); ?></label><br/>
				<label><input type="radio" name="<?php echo esc_attr( $field_name ); ?>[item][use_attribute]" value="no"<?php checked( 'no', $use_attribute ) ?> /> <?php _e( 'No', XRFC_DOMAIN ); ?></label>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-attribute-name"><?php _e( 'Attribute Name', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-attribute-name" name="<?php echo esc_attr( $field_name ); ?>[item][attribute_name]" class="regular-text code" value="<?php echo esc_attr( isset( $field_values['item']['attribute_name'] ) ? $field_values['item']['attribute_name'] : '' ) ?>" />
			<span class="description"><?php _e( 'required if "Use Attribute" is "yes"', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="rss-item-column-name"><?php _e( 'Column Name', XRFC_DOMAIN ) ?></label></th>
		<td>
			<input type="text" id="rss-item-column-name" name="<?php echo esc_attr( $field_name ); ?>[item][column_name]" class="regular-text code" value="<?php echo esc_attr( isset( $field_values['item']['column_name'] ) ? $field_values['item']['column_name'] : '' ) ?>" />
			<span class="description"><?php _e( 'required if "Use Attribute" is "yes"', XRFC_DOMAIN ) ?></span>
		</td>
	</tr>
	</tbody>
</table>

<script type="application/javascript">
	/* <![CDATA[ */
	(function ( $ ) {
		$( function () {
			$( '#rss-feed-url' ).on( 'click focus', function () {
				this.setSelectionRange( 0, this.value.length );
			} );
		} );
	})( jQuery );
	/* ]]> */
</script>