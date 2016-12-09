<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/form-lost-password.php.
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
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices(); ?>

<form method="post" class="banda-ResetPassword lost_reset_password">

	<p><?php echo apply_filters( 'banda_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'banda' ) ); ?></p>

	<p class="banda-FormRow banda-FormRow--first form-row form-row-first">
		<label for="user_login"><?php _e( 'Username or email', 'banda' ); ?></label>
		<input class="banda-Input banda-Input--text input-text" type="text" name="user_login" id="user_login" />
	</p>

	<div class="clear"></div>

	<?php do_action( 'banda_lostpassword_form' ); ?>

	<p class="banda-FormRow form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<input type="submit" class="banda-Button button" value="<?php esc_attr_e( 'Reset Password', 'banda' ); ?>" />
	</p>

	<?php wp_nonce_field( 'lost_password' ); ?>

</form>
