<?php
/**
 * Banda Webhook functions
 *
 * @author   Mtaandao
 * @category Core
 * @package  Banda/Functions
 * @version  2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get Webhook statuses.
 *
 * @since  2.3.0
 * @return array
 */
function wc_get_webhook_statuses() {
	return apply_filters( 'banda_webhook_statuses', array(
		'active'   => __( 'Active', 'banda' ),
		'paused'   => __( 'Paused', 'banda' ),
		'disabled' => __( 'Disabled', 'banda' ),
	) );
}
