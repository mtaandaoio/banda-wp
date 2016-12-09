<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/banda/content-product.php.
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
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php post_class(); ?>>
	<?php
	/**
	 * banda_before_shop_loop_item hook.
	 *
	 * @hooked banda_template_loop_product_link_open - 10
	 */
	do_action( 'banda_before_shop_loop_item' );

	/**
	 * banda_before_shop_loop_item_title hook.
	 *
	 * @hooked banda_show_product_loop_sale_flash - 10
	 * @hooked banda_template_loop_product_thumbnail - 10
	 */
	do_action( 'banda_before_shop_loop_item_title' );

	/**
	 * banda_shop_loop_item_title hook.
	 *
	 * @hooked banda_template_loop_product_title - 10
	 */
	do_action( 'banda_shop_loop_item_title' );

	/**
	 * banda_after_shop_loop_item_title hook.
	 *
	 * @hooked banda_template_loop_rating - 5
	 * @hooked banda_template_loop_price - 10
	 */
	do_action( 'banda_after_shop_loop_item_title' );

	/**
	 * banda_after_shop_loop_item hook.
	 *
	 * @hooked banda_template_loop_product_link_close - 5
	 * @hooked banda_template_loop_add_to_cart - 10
	 */
	do_action( 'banda_after_shop_loop_item' );
	?>
</li>
