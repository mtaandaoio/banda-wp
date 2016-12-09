<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/banda/content-single-product.php.
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

?>

<?php
	/**
	 * banda_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'banda_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo banda_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * banda_before_single_product_summary hook.
		 *
		 * @hooked banda_show_product_sale_flash - 10
		 * @hooked banda_show_product_images - 20
		 */
		do_action( 'banda_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * banda_single_product_summary hook.
			 *
			 * @hooked banda_template_single_title - 5
			 * @hooked banda_template_single_rating - 10
			 * @hooked banda_template_single_price - 10
			 * @hooked banda_template_single_excerpt - 20
			 * @hooked banda_template_single_add_to_cart - 30
			 * @hooked banda_template_single_meta - 40
			 * @hooked banda_template_single_sharing - 50
			 */
			do_action( 'banda_single_product_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * banda_after_single_product_summary hook.
		 *
		 * @hooked banda_output_product_data_tabs - 10
		 * @hooked banda_upsell_display - 15
		 * @hooked banda_output_related_products - 20
		 */
		do_action( 'banda_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'banda_after_single_product' ); ?>
