<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

// Get the payment gateway
$payment_gateway = wc_get_payment_gateway_by_order( $order );

// Get line items
$line_items          = $order->get_items( apply_filters( 'banda_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if ( wc_tax_enabled() ) {
	$order_taxes         = $order->get_taxes();
	$tax_classes         = WC_Tax::get_tax_classes();
	$classes_options     = array();
	$classes_options[''] = __( 'Standard', 'banda' );

	if ( ! empty( $tax_classes ) ) {
		foreach ( $tax_classes as $class ) {
			$classes_options[ sanitize_title( $class ) ] = $class;
		}
	}

	// Older orders won't have line taxes so we need to handle them differently :(
	$tax_data = '';
	if ( $line_items ) {
		$check_item = current( $line_items );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	} elseif ( $line_items_shipping ) {
		$check_item = current( $line_items_shipping );
		$tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
	} elseif ( $line_items_fee ) {
		$check_item = current( $line_items_fee );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	}

	$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
	$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
}
?>
<div class="banda_order_items_wrapper wc-order-items-editable">
	<table cellpadding="0" cellspacing="0" class="banda_order_items">
		<thead>
			<tr>
				<th class="item sortable" colspan="2" data-sort="string-ins"><?php _e( 'Item', 'banda' ); ?></th>
				<?php do_action( 'banda_admin_order_item_headers', $order ); ?>
				<th class="item_cost sortable" data-sort="float"><?php _e( 'Cost', 'banda' ); ?></th>
				<th class="quantity sortable" data-sort="int"><?php _e( 'Qty', 'banda' ); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'banda' ); ?></th>
				<?php
					if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
						foreach ( $order_taxes as $tax_id => $tax_item ) :
							$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
							$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'banda' );
							$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'banda' );
							$column_tip     = $tax_item['name'] . ' (' . $tax_class_name . ')';
							?>
							<th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
								<?php echo esc_attr( $column_label ); ?>
								<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
								<a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
							</th>
							<?php
						endforeach;
					endif;
				?>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="order_line_items">
		<?php
			foreach ( $line_items as $item_id => $item ) {
				$_product  = $order->get_product_from_item( $item );
				$item_meta = $order->get_item_meta( $item_id );

				do_action( 'banda_before_order_item_' . $item['type'] . '_html', $item_id, $item, $order );

				include( 'html-order-item.php' );

				do_action( 'banda_order_item_' . $item['type'] . '_html', $item_id, $item, $order );
			}
			do_action( 'banda_admin_order_items_after_line_items', $order->id );
		?>
		</tbody>
		<tbody id="order_shipping_line_items">
		<?php
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
			foreach ( $line_items_shipping as $item_id => $item ) {
				include( 'html-order-shipping.php' );
			}
			do_action( 'banda_admin_order_items_after_shipping', $order->id );
		?>
		</tbody>
		<tbody id="order_fee_line_items">
		<?php
			foreach ( $line_items_fee as $item_id => $item ) {
				include( 'html-order-fee.php' );
			}
			do_action( 'banda_admin_order_items_after_fees', $order->id );
		?>
		</tbody>
		<tbody id="order_refunds">
		<?php
			if ( $refunds = $order->get_refunds() ) {
				foreach ( $refunds as $refund ) {
					include( 'html-order-refund.php' );
				}
				do_action( 'banda_admin_order_items_after_refunds', $order->id );
			}
		?>
		</tbody>
	</table>
</div>
<div class="wc-order-data-row wc-order-item-bulk-edit" style="display:none;">
	<button type="button" class="button bulk-delete-items"><?php _e( 'Delete selected row(s)', 'banda' ); ?></button>
	<button type="button" class="button bulk-decrease-stock"><?php _e( 'Reduce stock', 'banda' ); ?></button>
	<button type="button" class="button bulk-increase-stock"><?php _e( 'Increase stock', 'banda' ); ?></button>
</div>
<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
	<?php
		$coupons = $order->get_items( array( 'coupon' ) );
		if ( $coupons ) {
			?>
			<div class="wc-used-coupons">
				<ul class="wc_coupon_list"><?php
					echo '<li><strong>' . __( 'Coupon(s) Used', 'banda' ) . '</strong></li>';
					foreach ( $coupons as $item_id => $item ) {
						$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

						$link = $post_id ? add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) : add_query_arg( array( 's' => $item['name'], 'post_status' => 'all', 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) );

						echo '<li class="code"><a href="' . esc_url( $link ) . '" class="tips" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => $order->get_order_currency() ) ) ) . '"><span>' . esc_html( $item['name'] ). '</span></a></li>';
					}
				?></ul>
			</div>
			<?php
		}
	?>
	<table class="wc-order-totals">
		<tr>
			<td class="label"><?php echo wc_help_tip( __( 'This is the total discount. Discounts are defined per line item.', 'banda' ) ); ?> <?php _e( 'Discount', 'banda' ); ?>:</td>
			<td width="1%"></td>
			<td class="total">
				<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_order_currency() ) ); ?>
			</td>
		</tr>

		<?php do_action( 'banda_admin_order_totals_after_discount', $order->id ); ?>

		<tr>
			<td class="label"><?php echo wc_help_tip( __( 'This is the shipping and handling total costs for the order.', 'banda' ) ); ?> <?php _e( 'Shipping', 'banda' ); ?>:</td>
			<td width="1%"></td>
			<td class="total"><?php
				if ( ( $refunded = $order->get_total_shipping_refunded() ) > 0 ) {
					echo '<del>' . strip_tags( wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_order_currency() ) ) ) . '</del> <ins>' . wc_price( $order->get_total_shipping() - $refunded, array( 'currency' => $order->get_order_currency() ) ) . '</ins>';
				} else {
					echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_order_currency() ) );
				}
			?></td>
		</tr>

		<?php do_action( 'banda_admin_order_totals_after_shipping', $order->id ); ?>

		<?php if ( wc_tax_enabled() ) : ?>
			<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
				<tr>
					<td class="label"><?php echo $tax->label; ?>:</td>
					<td width="1%"></td>
					<td class="total"><?php
						if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
							echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_order_currency() ) ) . '</ins>';
						} else {
							echo $tax->formatted_amount;
						}
					?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'banda_admin_order_totals_after_tax', $order->id ); ?>

		<tr>
			<td class="label"><?php _e( 'Order Total', 'banda' ); ?>:</td>
			<td>
				<?php if ( $order->is_editable() ) : ?>
					<div class="wc-order-edit-line-item-actions">
						<a class="edit-order-item" href="#"></a>
					</div>
				<?php endif; ?>
			</td>
			<td class="total">
				<div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
				<div class="edit" style="display: none;">
					<input type="text" class="wc_input_price" id="_order_total" name="_order_total" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo ( isset( $data['_order_total'][0] ) ) ? esc_attr( wc_format_localized_price( $data['_order_total'][0] ) ) : ''; ?>" />
					<div class="clear"></div>
				</div>
			</td>
		</tr>

		<?php do_action( 'banda_admin_order_totals_after_total', $order->id ); ?>

		<tr>
			<td class="label refunded-total"><?php _e( 'Refunded', 'banda' ); ?>:</td>
			<td width="1%"></td>
			<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ); ?></td>
		</tr>

		<?php do_action( 'banda_admin_order_totals_after_refunded', $order->id ); ?>

	</table>
	<div class="clear"></div>
</div>
<div class="wc-order-data-row wc-order-bulk-actions wc-order-data-row-toggle">
	<p class="add-items">
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button add-line-item"><?php _e( 'Add item(s)', 'banda' ); ?></button>
		<?php else : ?>
			<span class="description"><?php echo wc_help_tip( __( 'To edit this order change the status back to "Pending"', 'banda' ) ); ?> <?php _e( 'This order is no longer editable.', 'banda' ); ?></span>
		<?php endif; ?>
		<?php if ( wc_tax_enabled() && $order->is_editable() ) : ?>
			<button type="button" class="button add-order-tax"><?php _e( 'Add tax', 'banda' ); ?></button>
		<?php endif; ?>
		<?php if ( 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ) ) : ?>
			<button type="button" class="button refund-items"><?php _e( 'Refund', 'banda' ); ?></button>
		<?php endif; ?>
		<?php
			// allow adding custom buttons
			do_action( 'banda_order_item_add_action_buttons', $order );
		?>
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button button-primary calculate-tax-action"><?php _e( 'Calculate Taxes', 'banda' ); ?></button>
			<button type="button" class="button button-primary calculate-action"><?php _e( 'Calculate Total', 'banda' ); ?></button>
		<?php endif; ?>
	</p>
</div>
<div class="wc-order-data-row wc-order-add-item wc-order-data-row-toggle" style="display:none;">
	<button type="button" class="button add-order-item"><?php _e( 'Add product(s)', 'banda' ); ?></button>
	<button type="button" class="button add-order-fee"><?php _e( 'Add fee', 'banda' ); ?></button>
	<button type="button" class="button add-order-shipping"><?php _e( 'Add shipping cost', 'banda' ); ?></button>
	<button type="button" class="button cancel-action"><?php _e( 'Cancel', 'banda' ); ?></button>
	<button type="button" class="button button-primary save-action"><?php _e( 'Save', 'banda' ); ?></button>
	<?php
		// allow adding custom buttons
		do_action( 'banda_order_item_add_line_buttons', $order );
	?>
</div>
<?php if ( 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ) ) : ?>
<div class="wc-order-data-row wc-order-refund-items wc-order-data-row-toggle" style="display: none;">
	<table class="wc-order-totals">
		<tr style="display:none;">
			<td class="label"><label for="restock_refunded_items"><?php _e( 'Restock refunded items', 'banda' ); ?>:</label></td>
			<td class="total"><input type="checkbox" id="restock_refunded_items" name="restock_refunded_items" checked="checked" /></td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Amount already refunded', 'banda' ); ?>:</td>
			<td class="total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ); ?></td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Total available to refund', 'banda' ); ?>:</td>
			<td class="total"><?php echo wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ); ?></td>
		</tr>
		<tr>
			<td class="label"><label for="refund_amount"><?php _e( 'Refund amount', 'banda' ); ?>:</label></td>
			<td class="total">
				<input type="text" class="text" id="refund_amount" name="refund_amount" class="wc_input_price" />
				<div class="clear"></div>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="refund_reason"><?php echo wc_help_tip( __( 'Note: the refund reason will be visible by the customer.', 'banda' ) ); ?> <?php _e( 'Reason for refund (optional)', 'banda' ); ?>:</label></td>
			<td class="total">
				<input type="text" class="text" id="refund_reason" name="refund_reason" />
				<div class="clear"></div>
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<div class="refund-actions">
		<?php
		$refund_amount            = '<span class="wc-order-refund-amount">' . wc_price( 0, array( 'currency' => $order->get_order_currency() ) ) . '</span>';
		$gateway_supports_refunds = false !== $payment_gateway && $payment_gateway->supports( 'refunds' );
		$gateway_name             = false !== $payment_gateway ? ( ! empty( $payment_gateway->method_title ) ? $payment_gateway->method_title : $payment_gateway->get_title() ) : __( 'Payment Gateway', 'banda' );
		?>
		<button type="button" class="button <?php echo $gateway_supports_refunds ? 'button-primary do-api-refund' : 'tips disabled'; ?>" <?php echo $gateway_supports_refunds ? '' : 'data-tip="' . esc_attr__( 'The payment gateway used to place this order does not support automatic refunds.', 'banda' ) . '"'; ?>><?php printf( _x( 'Refund %s via %s', 'Refund $amount', 'banda' ), $refund_amount, $gateway_name ); ?></button>
		<button type="button" class="button button-primary do-manual-refund tips" data-tip="<?php esc_attr_e( 'You will need to manually issue a refund through your payment gateway after using this.', 'banda' ); ?>"><?php printf( _x( 'Refund %s manually', 'Refund $amount manually', 'banda' ), $refund_amount ); ?></button>
		<button type="button" class="button cancel-action"><?php _e( 'Cancel', 'banda' ); ?></button>
		<div class="clear"></div>
	</div>
</div>
<?php endif; ?>

<script type="text/template" id="tmpl-wc-modal-add-products">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php _e( 'Add products', 'banda' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<input type="hidden" id="add_item_id" name="add_order_items" class="wc-product-search" style="width: 100%;" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'banda' ); ?>" data-multiple="true" />
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add', 'banda' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-modal-add-tax">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php _e( 'Add tax', 'banda' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php _e( 'Rate name', 'banda' ); ?></th>
									<th><?php _e( 'Tax class', 'banda' ); ?></th>
									<th><?php _e( 'Rate code', 'banda' ); ?></th>
									<th><?php _e( 'Rate %', 'banda' ); ?></th>
								</tr>
							</thead>
						<?php
							$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}banda_tax_rates ORDER BY tax_rate_name LIMIT 100" );

							foreach ( $rates as $rate ) {
								echo '
									<tr>
										<td><input type="radio" id="add_order_tax_' . absint( $rate->tax_rate_id ) . '" name="add_order_tax" value="' . absint( $rate->tax_rate_id ) . '" /></td>
										<td><label for="add_order_tax_' . absint( $rate->tax_rate_id ) . '">' . WC_Tax::get_rate_label( $rate ) . '</label></td>
										<td>' . ( isset( $classes_options[ $rate->tax_rate_class ] ) ? $classes_options[ $rate->tax_rate_class ] : '-' ) . '</td>
										<td>' . WC_Tax::get_rate_code( $rate ) . '</td>
										<td>' . WC_Tax::get_rate_percent( $rate ) . '</td>
									</tr>
								';
							}
						?>
						</table>
						<?php if ( absint( $wpdb->get_var( "SELECT COUNT(tax_rate_id) FROM {$wpdb->prefix}banda_tax_rates;" ) ) > 100 ) : ?>
							<p>
								<label for="manual_tax_rate_id"><?php _e( 'Or, enter tax rate ID:', 'banda' ); ?></label><br/>
								<input type="number" name="manual_tax_rate_id" id="manual_tax_rate_id" step="1" placeholder="<?php esc_attr_e( 'Optional', 'banda' ); ?>" />
							</p>
						<?php endif; ?>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add', 'banda' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
