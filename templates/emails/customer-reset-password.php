<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/banda/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author 		Mtaandao
 * @package 	Banda/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'banda_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Someone requested that the password be reset for the following account:', 'banda' ); ?></p>
<p><?php printf( __( 'Username: %s', 'banda' ), $user_login ); ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'banda' ); ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'banda' ); ?></p>
<p>
	<a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => rawurlencode( $user_login ) ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>">
			<?php _e( 'Click here to reset your password', 'banda' ); ?></a>
</p>
<p></p>

<?php do_action( 'banda_email_footer', $email ); ?>