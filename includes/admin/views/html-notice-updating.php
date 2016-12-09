<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated banda-message wc-connect">
	<p><strong><?php _e( 'Banda Data Update', 'banda' ); ?></strong> &#8211; <?php _e( 'Your database is being updated in the background.', 'banda' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_banda', 'true', admin_url( 'admin.php?page=wc-settings' ) ) ); ?>"><?php _e( 'Taking a while? Click here to run it now.', 'banda' ); ?></a></p>
</div>
