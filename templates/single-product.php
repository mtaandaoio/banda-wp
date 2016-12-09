<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product.php.
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

		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

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
