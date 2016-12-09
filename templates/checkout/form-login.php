<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/banda/checkout/form-login.php.
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
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'banda_enable_checkout_login_reminder' ) ) {
	return;
}

$info_message  = apply_filters( 'banda_checkout_login_message', __( 'Returning customer?', 'banda' ) );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'banda' ) . '</a>';
wc_print_notice( $info_message, 'notice' );
?>

<?php
	banda_login_form(
		array(
			'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'banda' ),
			'redirect' => wc_get_page_permalink( 'checkout' ),
			'hidden'   => true
		)
	);
?>
