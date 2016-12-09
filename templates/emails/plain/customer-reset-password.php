<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/banda/emails/plain/customer-reset-password.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates/Emails/Plain
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo __( 'Someone requested that the password be reset for the following account:', 'banda' ) . "\r\n\r\n";
echo esc_url( network_home_url( '/' ) ) . "\r\n\r\n";
echo sprintf( __( 'Username: %s', 'banda' ), $user_login ) . "\r\n\r\n";
echo __( 'If this was a mistake, just ignore this email and nothing will happen.', 'banda' ) . "\r\n\r\n";
echo __( 'To reset your password, visit the following address:', 'banda' ) . "\r\n\r\n";

echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => $user_login ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ) . "\r\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'banda_email_footer_text', get_option( 'banda_email_footer_text' ) );
