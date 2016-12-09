<?php

/**
 * Tools for Banda.
 *
 * @since    2.5.0
 * @package  Banda/CLI
 * @category CLI
 * @author   Mtaandao
 */
class WC_CLI_Tool extends WC_CLI_Command {

	/**
	 * Clear the product/shop transients cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc tool clear_transients
	 *
	 * @since 2.5.0
	 */
	public function clear_transients( $args, $assoc_args ) {
		wc_delete_product_transients();
		wc_delete_shop_order_transients();
		WC_Cache_Helper::get_transient_version( 'shipping', true );

		WP_CLI::success( 'Product transients and shop order transients were cleared.' );
	}
}
