<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/related.php.
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
	exit;
}

global $product, $banda_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

if ( ! $related = $product->get_related( $posts_per_page ) ) {
	return;
}

$args = apply_filters( 'banda_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );

$products                    = new WP_Query( $args );
$banda_loop['name']    = 'related';
$banda_loop['columns'] = apply_filters( 'banda_related_products_columns', $columns );

if ( $products->have_posts() ) : ?>

	<div class="related products">

		<h2><?php _e( 'Related Products', 'banda' ); ?></h2>

		<?php banda_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php banda_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
