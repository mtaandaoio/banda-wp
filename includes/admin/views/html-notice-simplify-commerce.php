<?php
/**
 * Admin View: Notice - Simplify Commerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_slug = 'banda-gateway-simplify-commerce';

if ( current_user_can( 'install_plugins' ) ) {
	$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
} else {
	$url = 'https://wordpress.org/plugins/' . $plugin_slug;
}
?>
<div id="message" class="updated banda-message">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'simplify_commerce' ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>

	<p><?php _e( '<strong>The Simplify Commerce payment gateway is deprecated</strong> &#8211; Please install our new free Simplify Commerce plugin from WordPress.org. Simplify Commerce will be removed from Banda core in a future update.', 'banda' ); ?></p>

	<p class="submit"><a href="<?php echo esc_url( $url ); ?>" class="wc-update-now button-primary"><?php _e( 'Install our new Simplify Commerce plugin', 'banda' ); ?></a></p>
</div>
