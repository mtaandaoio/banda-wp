<?php
/**
 * Auth form login
 *
 * This template can be overridden by copying it to yourtheme/banda/auth/form-login.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates/Auth
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'banda_auth_page_header' ); ?>

<h1><?php printf( __( '%s would like to connect to your store' , 'banda' ), esc_html( $app_name ) ); ?></h1>

<?php wc_print_notices(); ?>

<p><?php printf( __( 'To connect to %1$s you need to be logged in. Log in to your store below, or %2$scancel and return to %1$s%3$s', 'banda' ), wc_clean( $app_name ), '<a href="' . esc_url( $return_url ) . '">', '</a>' ); ?></p>

<form method="post" class="wc-auth-login">
	<p class="form-row form-row-wide">
		<label for="username"><?php _e( 'Username or email address', 'banda' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="username" id="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
	</p>
	<p class="form-row form-row-wide">
		<label for="password"><?php _e( 'Password', 'banda' ); ?> <span class="required">*</span></label>
		<input class="input-text" type="password" name="password" id="password" />
	</p>
	<p class="wc-auth-actions">
		<?php wp_nonce_field( 'banda-login' ); ?>
		<input type="submit" class="button button-large button-primary wc-auth-login-button" name="login" value="<?php esc_attr_e( 'Login', 'banda' ); ?>" />
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect_url ); ?>" />
	</p>
</form>

<?php do_action( 'banda_auth_page_footer' ); ?>
