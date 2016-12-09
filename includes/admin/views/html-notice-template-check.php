<?php
/**
 * Admin View: Notice - Template Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme = wp_get_theme();
?>
<div id="message" class="updated banda-message">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'template_files' ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>

	<p><?php printf( __( '<strong>Your theme (%s) contains outdated copies of some Banda template files.</strong> These files may need updating to ensure they are compatible with the current version of Banda. You can see which files are affected from the %ssystem status page%s. If in doubt, check with the author of the theme.', 'banda' ), esc_html( $theme['Name'] ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status' ) ) . '">', '</a>' ); ?></p>
	<p class="submit"><a class="button-primary" href="https://docs.mtaandao.co.ke/document/template-structure/" target="_blank"><?php _e( 'Learn More About Templates', 'banda' ); ?></a></p>
</div>
