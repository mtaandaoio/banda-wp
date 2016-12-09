<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/banda/checkout/form-checkout.php.
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
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'banda_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'banda_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'banda' ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout banda-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'banda_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'banda_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'banda_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'banda_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<h3 id="order_review_heading"><?php _e( 'Your order', 'banda' ); ?></h3>

	<?php do_action( 'banda_checkout_before_order_review' ); ?>

	<div id="order_review" class="banda-checkout-review-order">
		<?php do_action( 'banda_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'banda_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'banda_after_checkout_form', $checkout ); ?>
