<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/banda/emails/plain/customer-new-account.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author 		Mtaandao
 * @package 	Banda/Templates/Emails/Plain
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Thanks for creating an account on %s. Your username is <strong>%s</strong>", 'banda' ), $blogname, $user_login ) . "\n\n";

if ( 'yes' === get_option( 'banda_registration_generate_password' ) && $password_generated )
	echo sprintf( __( "Your password is <strong>%s</strong>.", 'banda' ), $user_pass ) . "\n\n";

echo sprintf( __( 'You can access your account area to view your orders and change your password here: %s.', 'banda' ), wc_get_page_permalink( 'myaccount' ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'banda_email_footer_text', get_option( 'banda_email_footer_text' ) );
