<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/orders.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'banda_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<table class="banda-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $customer_orders->orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count();
				?>
				<tr class="order">
					<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'banda_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'banda_my_account_my_orders_column_' . $column_id, $order ); ?>

							<?php elseif ( 'order-number' === $column_id ) : ?>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php echo _x( '#', 'hash before order number', 'banda' ) . $order->get_order_number(); ?>
								</a>

							<?php elseif ( 'order-date' === $column_id ) : ?>
								<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>

							<?php elseif ( 'order-status' === $column_id ) : ?>
								<?php echo wc_get_order_status_name( $order->get_status() ); ?>

							<?php elseif ( 'order-total' === $column_id ) : ?>
								<?php echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'banda' ), $order->get_formatted_order_total(), $item_count ); ?>

							<?php elseif ( 'order-actions' === $column_id ) : ?>
								<?php
									$actions = array(
										'pay'    => array(
											'url'  => $order->get_checkout_payment_url(),
											'name' => __( 'Pay', 'banda' )
										),
										'view'   => array(
											'url'  => $order->get_view_order_url(),
											'name' => __( 'View', 'banda' )
										),
										'cancel' => array(
											'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
											'name' => __( 'Cancel', 'banda' )
										)
									);

									if ( ! $order->needs_payment() ) {
										unset( $actions['pay'] );
									}

									if ( ! in_array( $order->get_status(), apply_filters( 'banda_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
										unset( $actions['cancel'] );
									}

									if ( $actions = apply_filters( 'banda_my_account_my_orders_actions', $actions, $order ) ) {
										foreach ( $actions as $key => $action ) {
											echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
										}
									}
								?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php do_action( 'banda_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="banda-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="banda-Button banda-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'banda' ); ?></a>
			<?php endif; ?>

			<?php if ( $current_page !== intval( $customer_orders->max_num_pages ) ) : ?>
				<a class="banda-Button banda-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'banda' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="banda-Message banda-Message--info banda-info">
		<a class="banda-Button button" href="<?php echo esc_url( apply_filters( 'banda_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Go Shop', 'banda' ) ?>
		</a>
		<?php _e( 'No order has been made yet.', 'banda' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'banda_after_account_orders', $has_orders ); ?>
