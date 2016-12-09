<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/banda/cart/cart-empty.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

?>

<p class="cart-empty">
	<?php _e( 'Your cart is currently empty.', 'banda' ) ?>
</p>

<?php do_action( 'banda_cart_is_empty' ); ?>

<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<p class="return-to-shop">
		<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'banda_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Return To Shop', 'banda' ) ?>
		</a>
	</p>
<?php endif; ?>
