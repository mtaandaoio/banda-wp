<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/form-login.php.
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
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'banda_before_customer_login_form' ); ?>

<?php if ( get_option( 'banda_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="u-columns col2-set" id="customer_login">

	<div class="u-column1 col-1">

<?php endif; ?>

		<h2><?php _e( 'Login', 'banda' ); ?></h2>

		<form method="post" class="login">

			<?php do_action( 'banda_login_form_start' ); ?>

			<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
				<label for="username"><?php _e( 'Username or email address', 'banda' ); ?> <span class="required">*</span></label>
				<input type="text" class="banda-Input banda-Input--text input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>
			<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
				<label for="password"><?php _e( 'Password', 'banda' ); ?> <span class="required">*</span></label>
				<input class="banda-Input banda-Input--text input-text" type="password" name="password" id="password" />
			</p>

			<?php do_action( 'banda_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'banda-login', 'banda-login-nonce' ); ?>
				<input type="submit" class="banda-Button button" name="login" value="<?php esc_attr_e( 'Login', 'banda' ); ?>" />
				<label for="rememberme" class="inline">
					<input class="banda-Input banda-Input--checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'banda' ); ?>
				</label>
			</p>
			<p class="banda-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'banda' ); ?></a>
			</p>

			<?php do_action( 'banda_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'banda_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="u-column2 col-2">

		<h2><?php _e( 'Register', 'banda' ); ?></h2>

		<form method="post" class="register">

			<?php do_action( 'banda_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'banda_registration_generate_username' ) ) : ?>

				<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'banda' ); ?> <span class="required">*</span></label>
					<input type="text" class="banda-Input banda-Input--text input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
				</p>

			<?php endif; ?>

			<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email address', 'banda' ); ?> <span class="required">*</span></label>
				<input type="email" class="banda-Input banda-Input--text input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
			</p>

			<?php if ( 'no' === get_option( 'banda_registration_generate_password' ) ) : ?>

				<p class="banda-FormRow banda-FormRow--wide form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'banda' ); ?> <span class="required">*</span></label>
					<input type="password" class="banda-Input banda-Input--text input-text" name="password" id="reg_password" />
				</p>

			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'banda' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" autocomplete="off" /></div>

			<?php do_action( 'banda_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<p class="woocomerce-FormRow form-row">
				<?php wp_nonce_field( 'banda-register', 'banda-register-nonce' ); ?>
				<input type="submit" class="banda-Button button" name="register" value="<?php esc_attr_e( 'Register', 'banda' ); ?>" />
			</p>

			<?php do_action( 'banda_register_form_end' ); ?>

		</form>

	</div>

</div>
<?php endif; ?>

<?php do_action( 'banda_after_customer_login_form' ); ?>
