<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated banda-message wc-connect">
	<a class="banda-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'update', remove_query_arg( 'do_update_banda' ) ), 'banda_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'banda' ); ?></a>

	<p><?php _e( 'Banda data update complete. Thank you for updating to the latest version!', 'banda' ); ?></p>
</div>
