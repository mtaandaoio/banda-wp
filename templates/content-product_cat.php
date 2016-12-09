<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/banda/content-product_cat.php.
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
	exit;
}
?>
<li <?php wc_product_cat_class( '', $category ); ?>>
	<?php
	/**
	 * banda_before_subcategory hook.
	 *
	 * @hooked banda_template_loop_category_link_open - 10
	 */
	do_action( 'banda_before_subcategory', $category );

	/**
	 * banda_before_subcategory_title hook.
	 *
	 * @hooked banda_subcategory_thumbnail - 10
	 */
	do_action( 'banda_before_subcategory_title', $category );

	/**
	 * banda_shop_loop_subcategory_title hook.
	 *
	 * @hooked banda_template_loop_category_title - 10
	 */
	do_action( 'banda_shop_loop_subcategory_title', $category );

	/**
	 * banda_after_subcategory_title hook.
	 */
	do_action( 'banda_after_subcategory_title', $category );

	/**
	 * banda_after_subcategory hook.
	 *
	 * @hooked banda_template_loop_category_link_close - 10
	 */
	do_action( 'banda_after_subcategory', $category ); ?>
</li>
