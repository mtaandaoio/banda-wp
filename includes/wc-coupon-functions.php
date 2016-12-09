<?php
/**
 * Banda Coupons Functions
 *
 * Functions for coupon specific things.
 *
 * @author 		Mtaandao
 * @category 	Core
 * @package 	Banda/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get coupon types.
 *
 * @return array
 */
function wc_get_coupon_types() {
	return (array) apply_filters( 'banda_coupon_discount_types', array(
		'fixed_cart'      => __( 'Cart Discount', 'banda' ),
		'percent'         => __( 'Cart % Discount', 'banda' ),
		'fixed_product'   => __( 'Product Discount', 'banda' ),
		'percent_product' => __( 'Product % Discount', 'banda' )
	) );
}

/**
 * Get a coupon type's name.
 *
 * @param string $type (default: '')
 * @return string
 */
function wc_get_coupon_type( $type = '' ) {
	$types = wc_get_coupon_types();
	return isset( $types[ $type ] ) ? $types[ $type ] : '';
}

/**
 * Coupon types that apply to individual products. Controls which validation rules will apply.
 *
 * @since  2.5.0
 * @return array
 */
function wc_get_product_coupon_types() {
	return (array) apply_filters( 'banda_product_coupon_types', array( 'fixed_product', 'percent_product' ) );
}

/**
 * Coupon types that apply to the cart as a whole. Controls which validation rules will apply.
 *
 * @since  2.5.0
 * @return array
 */
function wc_get_cart_coupon_types() {
	return (array) apply_filters( 'banda_cart_coupon_types', array( 'fixed_cart', 'percent' ) );
}

/**
 * Check if coupons are enabled.
 * Filterable.
 *
 * @since  2.5.0
 *
 * @return array
 */
function wc_coupons_enabled() {
	return apply_filters( 'banda_coupons_enabled', 'yes' === get_option( 'banda_enable_coupons' ) );
}
