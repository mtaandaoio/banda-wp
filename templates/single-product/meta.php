<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/meta.php.
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

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

?>
<div class="product_meta">

	<?php do_action( 'banda_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'banda' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'banda' ); ?></span></span>

	<?php endif; ?>

	<?php echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'banda' ) . ' ', '</span>' ); ?>

	<?php echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'banda' ) . ' ', '</span>' ); ?>

	<?php do_action( 'banda_product_meta_end' ); ?>

</div>
