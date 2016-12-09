<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/banda/checkout/thankyou.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author 		Mtaandao
 * @package 	Banda/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p class="banda-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'banda' ); ?></p>

		<p class="banda-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'banda' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'banda' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<p class="banda-thankyou-order-received"><?php echo apply_filters( 'banda_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'banda' ), $order ); ?></p>

		<ul class="banda-thankyou-order-details order_details">
			<li class="order">
				<?php _e( 'Order Number:', 'banda' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php _e( 'Date:', 'banda' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php _e( 'Total:', 'banda' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php _e( 'Payment Method:', 'banda' ); ?>
				<strong><?php echo $order->payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'banda_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'banda_thankyou', $order->id ); ?>

<?php else : ?>

	<p class="banda-thankyou-order-received"><?php echo apply_filters( 'banda_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'banda' ), null ); ?></p>

<?php endif; ?>
