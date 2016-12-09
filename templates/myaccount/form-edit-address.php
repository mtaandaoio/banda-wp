<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/form-edit-address.php.
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

$page_title = ( $load_address === 'billing' ) ? __( 'Billing Address', 'banda' ) : __( 'Shipping Address', 'banda' );

do_action( 'banda_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<form method="post">

		<h3><?php echo apply_filters( 'banda_my_account_edit_address_title', $page_title ); ?></h3>

		<?php do_action( "banda_before_edit_address_form_{$load_address}" ); ?>

		<?php foreach ( $address as $key => $field ) : ?>

			<?php banda_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>

		<?php endforeach; ?>

		<?php do_action( "banda_after_edit_address_form_{$load_address}" ); ?>

		<p>
			<input type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Save Address', 'banda' ); ?>" />
			<?php wp_nonce_field( 'banda-edit_address' ); ?>
			<input type="hidden" name="action" value="edit_address" />
		</p>

	</form>

<?php endif; ?>

<?php do_action( 'banda_after_edit_account_address_form' ); ?>
