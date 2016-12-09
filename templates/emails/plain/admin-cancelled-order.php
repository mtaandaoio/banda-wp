<?php
/**
 * Admin cancelled order email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/banda/emails/plain/admin-cancelled-order.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author  	Mtaandao
 * @package 	Banda/Templates/Emails/Plain
 * @version 	2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'The order #%d from %s has been cancelled. The order was as follows:', 'banda' ), $order->id, $order->get_formatted_billing_full_name() ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'banda_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'banda_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'banda_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'banda_email_footer_text', get_option( 'banda_email_footer_text' ) );
