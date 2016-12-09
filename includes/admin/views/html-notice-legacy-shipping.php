<?php
/**
 * Admin View: Notice - Legacy Shipping.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="message" class="updated banda-message banda-legacy-shipping-notice">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'legacy_shipping' ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>

	<p class="main"><strong><?php _e( 'New:', 'banda' ); ?> <?php _e( 'Shipping Zones', 'banda' ); ?></strong> &#8211; <?php _e( 'a group of regions that can be assigned different shipping methods and rates.', 'banda' ); ?></p>
	<p><?php _e( 'Legacy shipping methods (Flat Rate, International Flat Rate, Local Pickup and Delivery, and Free Shipping) are deprecated but will continue to work as normal for now. <b><em>They will be removed in future versions of Banda</em></b>. We recommend disabling these and setting up new rates within shipping zones as soon as possible.', 'banda' ); ?></p>

	<p class="submit">
		<?php if ( empty( $_GET['page'] ) || empty( $_GET['tab'] ) || 'wc-settings' !== $_GET['page'] || 'shipping' !== $_GET['tab'] ) : ?>
			<a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ); ?>"><?php _e( 'Setup shipping zones', 'banda' ); ?></a>
		<?php endif; ?>
		<a class="button-secondary" href="https://docs.mtaandao.co.ke/document/setting-up-shipping-zones/"><?php _e( 'Learn more about shipping zones', 'banda' ); ?></a>
	</p>
</div>
