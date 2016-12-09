<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated banda-message wc-connect">
	<p><strong><?php _e( 'Banda Data Update', 'banda' ); ?></strong> &#8211; <?php _e( 'We need to update your store\'s database to the latest version.', 'banda' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_banda', 'true', admin_url( 'admin.php?page=wc-settings' ) ) ); ?>" class="wc-update-now button-primary"><?php _e( 'Run the updater', 'banda' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.wc-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'banda' ) ); ?>' ); // jshint ignore:line
	});
</script>
