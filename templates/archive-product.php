<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/banda/archive-product.php.
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
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

	<?php
		/**
		 * banda_before_main_content hook.
		 *
		 * @hooked banda_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked banda_breadcrumb - 20
		 */
		do_action( 'banda_before_main_content' );
	?>

		<?php if ( apply_filters( 'banda_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php banda_page_title(); ?></h1>

		<?php endif; ?>

		<?php
			/**
			 * banda_archive_description hook.
			 *
			 * @hooked banda_taxonomy_archive_description - 10
			 * @hooked banda_product_archive_description - 10
			 */
			do_action( 'banda_archive_description' );
		?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * banda_before_shop_loop hook.
				 *
				 * @hooked banda_result_count - 20
				 * @hooked banda_catalog_ordering - 30
				 */
				do_action( 'banda_before_shop_loop' );
			?>

			<?php banda_product_loop_start(); ?>

				<?php banda_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php banda_product_loop_end(); ?>

			<?php
				/**
				 * banda_after_shop_loop hook.
				 *
				 * @hooked banda_pagination - 10
				 */
				do_action( 'banda_after_shop_loop' );
			?>

		<?php elseif ( ! banda_product_subcategories( array( 'before' => banda_product_loop_start( false ), 'after' => banda_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * banda_after_main_content hook.
		 *
		 * @hooked banda_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'banda_after_main_content' );
	?>

	<?php
		/**
		 * banda_sidebar hook.
		 *
		 * @hooked banda_get_sidebar - 10
		 */
		do_action( 'banda_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
