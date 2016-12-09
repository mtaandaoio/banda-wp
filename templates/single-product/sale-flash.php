<?php
/**
 * Single Product Sale Flash
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/sale-flash.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author 		Mtaandao
 * @package 	Banda/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<?php if ( $product->is_on_sale() ) : ?>

	<?php echo apply_filters( 'banda_sale_flash', '<span class="onsale">' . __( 'Sale!', 'banda' ) . '</span>', $post, $product ); ?>

<?php endif; ?>
