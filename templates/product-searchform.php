<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/banda/product-searchform.php.
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
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<form role="search" method="get" class="banda-product-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<label class="screen-reader-text" for="banda-product-search-field"><?php _e( 'Search for:', 'banda' ); ?></label>
	<input type="search" id="banda-product-search-field" class="search-field" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'banda' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'banda' ); ?>" />
	<input type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'banda' ); ?>" />
	<input type="hidden" name="post_type" value="product" />
</form>
