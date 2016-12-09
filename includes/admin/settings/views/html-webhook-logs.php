<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$count_comments = wp_count_comments( $webhook->id );
$total          = $count_comments->approved;

?>

<?php echo WC_Admin_Webhooks::get_logs_navigation( $total, $webhook ); ?>

<table id="webhook-logs-table" class="widefat">
	<thead>
		<tr>
			<th><?php _e( 'Date', 'banda' ); ?></th>
			<th><?php _e( 'URL', 'banda' ); ?></th>
			<th><?php _e( 'Request', 'banda' ); ?></th>
			<th><?php _e( 'Response', 'banda' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e( 'Date', 'banda' ); ?></th>
			<th><?php _e( 'URL', 'banda' ); ?></th>
			<th><?php _e( 'Request', 'banda' ); ?></th>
			<th><?php _e( 'Response', 'banda' ); ?></th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ( $logs as $log ) {
				$log = $webhook->get_delivery_log( $log->comment_ID );

				include( 'html-webhook-log.php' );
			}
		?>
	</tbody>
</table>

<?php echo WC_Admin_Webhooks::get_logs_navigation( $total, $webhook ); ?>
