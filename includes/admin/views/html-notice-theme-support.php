<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated banda-message wc-connect">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'theme_support' ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>

	<p><?php printf( __( '<strong>Your theme does not declare Banda support</strong> &#8211; Please read our %sintegration%s guide or check out our %sStorefront%s theme which is totally free to download and designed specifically for use with Banda.', 'banda' ), '<a target="_blank" href="' . esc_url( apply_filters( 'banda_docs_url', 'https://docs.mtaandao.co.ke/document/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ) . '">', '</a>', '<a target="_blank" href="' . esc_url( admin_url( 'theme-install.php?theme=storefront' ) ) . '">', '</a>' ); ?></p>
	<p class="submit">
		<a href="https://mtaandao.co.ke/storefront/?utm_source=notice&amp;utm_medium=product&amp;utm_content=storefront&amp;utm_campaign=bandaplugin" class="button-primary" target="_blank"><?php _e( 'Read More About Storefront', 'banda' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'banda_docs_url', 'http://docs.mtaandao.co.ke/document/third-party-custom-theme-compatibility/?utm_source=notice&utm_medium=product&utm_content=themecompatibility&utm_campaign=bandaplugin' ) ); ?>" class="button-secondary" target="_blank"><?php _e( 'Theme Integration Guide', 'banda' ); ?></a>
	</p>
</div>
