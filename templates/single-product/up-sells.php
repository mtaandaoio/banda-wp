<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/up-sells.php.
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

if ( ! $upsells = $product->get_upsells() ) {
	return;
}

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => WC()->query->get_meta_query()
);

$products                    = new WP_Query( $args );
$banda_loop['name']    = 'up-sells';
$banda_loop['columns'] = apply_filters( 'banda_up_sells_columns', $columns );

if ( $products->have_posts() ) : ?>

	<div class="up-sells upsells products">

		<h2><?php _e( 'You may also like&hellip;', 'banda' ) ?></h2>

		<?php banda_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php banda_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
