<?php
/**
 * Banda Uninstall
 *
 * Uninstalling Banda deletes user roles, pages, tables, and options.
 *
 * @author      Mtaandao
 * @category    Core
 * @package     Banda/Uninstaller
 * @version     2.3.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb, $wp_version;

wp_clear_scheduled_hook( 'banda_scheduled_sales' );
wp_clear_scheduled_hook( 'banda_cancel_unpaid_orders' );
wp_clear_scheduled_hook( 'banda_cleanup_sessions' );
wp_clear_scheduled_hook( 'banda_geoip_updater' );
wp_clear_scheduled_hook( 'banda_tracker_send_event' );

$status_options = get_option( 'banda_status_options', array() );

if ( ! empty( $status_options['uninstall_data'] ) ) {
	// Roles + caps.
	include_once( 'includes/class-wc-install.php' );
	WC_Install::remove_roles();

	// Pages.
	wp_trash_post( get_option( 'banda_shop_page_id' ) );
	wp_trash_post( get_option( 'banda_cart_page_id' ) );
	wp_trash_post( get_option( 'banda_checkout_page_id' ) );
	wp_trash_post( get_option( 'banda_myaccount_page_id' ) );
	wp_trash_post( get_option( 'banda_edit_address_page_id' ) );
	wp_trash_post( get_option( 'banda_view_order_page_id' ) );
	wp_trash_post( get_option( 'banda_change_password_page_id' ) );
	wp_trash_post( get_option( 'banda_logout_page_id' ) );

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}banda_attribute_taxonomies';" ) ) {
		$wc_attributes = array_filter( (array) $wpdb->get_col( "SELECT attribute_name FROM {$wpdb->prefix}banda_attribute_taxonomies;" ) );
	} else {
		$wc_attributes = array();
	}

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_api_keys" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_attribute_taxonomies" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_downloadable_product_permissions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_termmeta" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_tax_rates" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_tax_rate_locations" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_shipping_zone_methods" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_shipping_zone_locations" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_shipping_zones" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_sessions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_payment_tokens" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_payment_tokenmeta" );

	// Delete options.
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'banda\_%';");

	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation', 'shop_coupon', 'shop_order', 'shop_order_refund' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_order_items" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}banda_order_itemmeta" );

	// Delete terms if > WP 4.2 (term splitting was added in 4.2)
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies
		foreach ( array( 'product_cat', 'product_tag', 'product_shipping_class', 'product_type' ) as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $taxonomy,
				)
			);
		}

		// Delete term attributes
		foreach ( $wc_attributes as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => 'pa_' . $taxonomy,
				)
			);
		}

		// Delete orphan relationships
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	// Clear any cached data that has been removed
	wp_cache_flush();
}
