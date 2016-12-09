<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/form-reset-password.php.
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

	<p><?php echo apply_filters( 'banda_reset_password_message', __( 'Enter a new password below.', 'banda') ); ?></p>

	<p class="banda-FormRow banda-FormRow--first form-row form-row-first">
		<label for="password_1"><?php _e( 'New password', 'banda' ); ?> <span class="required">*</span></label>
		<input type="password" class="banda-Input banda-Input--text input-text" name="password_1" id="password_1" />
	</p>
	<p class="banda-FormRow banda-FormRow--last form-row form-row-last">
		<label for="password_2"><?php _e( 'Re-enter new password', 'banda' ); ?> <span class="required">*</span></label>
		<input type="password" class="banda-Input banda-Input--text input-text" name="password_2" id="password_2" />
	</p>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

	<div class="clear"></div>

	<?php do_action( 'banda_resetpassword_form' ); ?>

	<p class="banda-FormRow form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<input type="submit" class="banda-Button button" value="<?php esc_attr_e( 'Save', 'banda' ); ?>" />
	</p>

	<?php wp_nonce_field( 'reset_password' ); ?>

</form>
