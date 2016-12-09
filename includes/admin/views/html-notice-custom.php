<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated banda-message">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', $notice ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
