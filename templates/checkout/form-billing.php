<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/banda/checkout/form-billing.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */

?>
<div class="banda-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php _e( 'Billing &amp; Shipping', 'banda' ); ?></h3>

	<?php else : ?>

		<h3><?php _e( 'Billing Details', 'banda' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'banda_before_checkout_billing_form', $checkout ); ?>

	<?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

		<?php banda_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<?php do_action('banda_after_checkout_billing_form', $checkout ); ?>

	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

		<?php if ( $checkout->enable_guest_checkout ) : ?>

			<p class="form-row form-row-wide create-account">
				<input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'banda_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'banda' ); ?></label>
			</p>

		<?php endif; ?>

		<?php do_action( 'banda_before_checkout_registration_form', $checkout ); ?>

		<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

			<div class="create-account">

				<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'banda' ); ?></p>

				<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

					<?php banda_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<div class="clear"></div>

			</div>

		<?php endif; ?>

		<?php do_action( 'banda_after_checkout_registration_form', $checkout ); ?>

	<?php endif; ?>
</div>
