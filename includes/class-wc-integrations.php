<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Banda Integrations class
 *
 * Loads Integrations into Banda.
 *
 * @class    WC_Integrations
 * @version  2.3.0
 * @package  Banda/Classes/Integrations
 * @category Class
 * @author   Mtaandao
 */
class WC_Integrations {

	/**
	 * Array of integrations.
	 *
	 * @var array
	 */
	public $integrations = array();

	/**
	 * Initialize integrations.
	 */
	public function __construct() {

		do_action( 'banda_integrations_init' );

		$load_integrations = apply_filters( 'banda_integrations', array() );

		// Load integration classes
		foreach ( $load_integrations as $integration ) {

			$load_integration = new $integration();

			$this->integrations[ $load_integration->id ] = $load_integration;
		}
	}

	/**
	 * Return loaded integrations.
	 *
	 * @access public
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}
