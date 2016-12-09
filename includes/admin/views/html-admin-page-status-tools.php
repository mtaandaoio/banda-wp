<?php
/**
 * Admin View: Page - Status Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form method="post" action="options.php">
	<?php settings_fields( 'banda_status_settings_fields' ); ?>
	<?php $options = wp_parse_args( get_option( 'banda_status_options', array() ), array( 'uninstall_data' => 0, 'template_debug_mode' => 0, 'shipping_debug_mode' => 0 ) ); ?>
	<table class="wc_status_table widefat" cellspacing="0">
		<tbody class="tools">
			<?php foreach ( $tools as $action => $tool ) : ?>
				<tr class="<?php echo sanitize_html_class( $action ); ?>">
					<td><?php echo esc_html( $tool['name'] ); ?></td>
					<td>
						<p>
							<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=wc-status&tab=tools&action=' . $action ), 'debug_action' ); ?>" class="button <?php echo esc_attr( $action ); ?>"><?php echo esc_html( $tool['button'] ); ?></a>
							<span class="description"><?php echo wp_kses_post( $tool['desc'] ); ?></span>
						</p>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><?php _e( 'Shipping Debug Mode', 'banda' ); ?></td>
				<td>
					<p>
						<label><input type="checkbox" class="checkbox" name="banda_status_options[shipping_debug_mode]" value="1" <?php checked( '1', $options['shipping_debug_mode'] ); ?> /> <?php _e( 'Enabled', 'banda' ); ?></label>
					</p>
					<p>
						<span class="description"><?php _e( 'Enable Shipping Debug Mode to show matching shipping zones and to bypass shipping rate cache.', 'banda' ); ?></span>
					</p>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Template Debug Mode', 'banda' ); ?></td>
				<td>
					<p>
						<label><input type="checkbox" class="checkbox" name="banda_status_options[template_debug_mode]" value="1" <?php checked( '1', $options['template_debug_mode'] ); ?> /> <?php _e( 'Enabled', 'banda' ); ?></label>
					</p>
					<p>
						<span class="description"><?php _e( 'Enable Template Debug Mode to bypass all theme and plugin template overrides for logged-in administrators. Used for debugging purposes.', 'banda' ); ?></span>
					</p>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Remove All Data', 'banda' ); ?></td>
				<td>
					<p>
						<label><input type="checkbox" class="checkbox" name="banda_status_options[uninstall_data]" value="1" <?php checked( '1', $options['uninstall_data'] ); ?> /> <?php _e( 'Enabled', 'banda' ); ?></label>
					</p>
					<p>
						<span class="description"><?php _e( 'This tool will remove all Banda, Product and Order data when using the "Delete" link on the plugins screen. It will also remove any setting/option prepended with "banda_" so may also affect installed Banda Extensions.', 'banda' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save changes', 'banda' ) ?>" />
	</p>
</form>
