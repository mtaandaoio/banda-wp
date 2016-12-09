<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/form-edit-account.php.
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

do_action( 'banda_before_edit_account_form' ); ?>

<form class="banda-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'banda_edit_account_form_start' ); ?>

	<p class="banda-FormRow banda-FormRow--first form-row form-row-first">
		<label for="account_first_name"><?php _e( 'First name', 'banda' ); ?> <span class="required">*</span></label>
		<input type="text" class="banda-Input banda-Input--text input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="banda-FormRow banda-FormRow--last form-row form-row-last">
		<label for="account_last_name"><?php _e( 'Last name', 'banda' ); ?> <span class="required">*</span></label>
		<input type="text" class="banda-Input banda-Input--text input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'Email address', 'banda' ); ?> <span class="required">*</span></label>
		<input type="email" class="banda-Input banda-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>

	<fieldset>
		<legend><?php _e( 'Password Change', 'banda' ); ?></legend>

		<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
			<label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'banda' ); ?></label>
			<input type="password" class="banda-Input banda-Input--password input-text" name="password_current" id="password_current" />
		</p>
		<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
			<label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'banda' ); ?></label>
			<input type="password" class="banda-Input banda-Input--password input-text" name="password_1" id="password_1" />
		</p>
		<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
			<label for="password_2"><?php _e( 'Confirm New Password', 'banda' ); ?></label>
			<input type="password" class="banda-Input banda-Input--password input-text" name="password_2" id="password_2" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'banda_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="banda-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'banda' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'banda_edit_account_form_end' ); ?>
</form>

<?php do_action( 'banda_after_edit_account_form' ); ?>
