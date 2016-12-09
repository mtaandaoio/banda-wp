<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );

do_action( 'banda_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'banda_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'banda' ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<td class="label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
						<td class="value">
							<?php
								$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name );
								wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
								echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'banda_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'banda' ) . '</a>' ) : '';
							?>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>

		<?php do_action( 'banda_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * banda_before_single_variation Hook.
				 */
				do_action( 'banda_before_single_variation' );

				/**
				 * banda_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked banda_single_variation - 10 Empty div for variation data.
				 * @hooked banda_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'banda_single_variation' );

				/**
				 * banda_after_single_variation Hook.
				 */
				do_action( 'banda_after_single_variation' );
			?>
		</div>

		<?php do_action( 'banda_after_add_to_cart_button' ); ?>
	<?php endif; ?>

	<?php do_action( 'banda_after_variations_form' ); ?>
</form>

<?php
do_action( 'banda_after_add_to_cart_form' );
