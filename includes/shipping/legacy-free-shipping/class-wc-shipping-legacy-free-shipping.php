<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Free Shipping Method.
 *
 * This class is here for backwards commpatility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version 2.4.0
 * @package Banda/Classes/Shipping
 * @author  Mtaandao
 */
class WC_Shipping_Legacy_Free_Shipping extends WC_Shipping_Method {

	/** @var float Min amount to be valid */
	public $min_amount;

	/** @var string Requires option */
	public $requires;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id 			= 'legacy_free_shipping';
		$this->method_title = __( 'Free Shipping (Legacy)', 'banda' );
		$this->method_description = sprintf( __( '<strong>This method is deprecated in 2.6.0 and will be removed in future versions - we recommend disabling it and instead setting up a new rate within your <a href="%s">Shipping Zones</a>.</strong>', 'banda' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) );
		$this->init();
	}

	/**
	 * Process and redirect if disabled.
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		if ( 'no' === $this->settings[ 'enabled' ] ) {
			wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=options' ) );
			exit;
		}
	}
	
	/**
	 * Return the name of the option in the WP DB.
	 * @since 2.6.0
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . 'free_shipping' . '_settings';
	}

	/**
	 * init function.
	 */
	public function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled		= $this->get_option( 'enabled' );
		$this->title 		= $this->get_option( 'title' );
		$this->min_amount 	= $this->get_option( 'min_amount', 0 );
		$this->availability = $this->get_option( 'availability' );
		$this->countries 	= $this->get_option( 'countries' );
		$this->requires		= $this->get_option( 'requires' );

		// Actions
		add_action( 'banda_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'banda' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Once disabled, this legacy method will no longer be available.', 'banda' ),
				'default' 		=> 'no'
			),
			'title' => array(
				'title' 		=> __( 'Method Title', 'banda' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during checkout.', 'banda' ),
				'default'		=> __( 'Free Shipping', 'banda' ),
				'desc_tip'		=> true,
			),
			'availability' => array(
				'title' 		=> __( 'Method availability', 'banda' ),
				'type' 			=> 'select',
				'default' 		=> 'all',
				'class'			=> 'availability wc-enhanced-select',
				'options'		=> array(
					'all' 		=> __( 'All allowed countries', 'banda' ),
					'specific' 	=> __( 'Specific Countries', 'banda' )
				)
			),
			'countries' => array(
				'title' 		=> __( 'Specific Countries', 'banda' ),
				'type' 			=> 'multiselect',
				'class'			=> 'wc-enhanced-select',
				'css'			=> 'width: 450px;',
				'default' 		=> '',
				'options'		=> WC()->countries->get_shipping_countries(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select some countries', 'banda' )
				)
			),
			'requires' => array(
				'title' 		=> __( 'Free Shipping Requires...', 'banda' ),
				'type' 			=> 'select',
				'class'         => 'wc-enhanced-select',
				'default' 		=> '',
				'options'		=> array(
					'' 				=> __( 'N/A', 'banda' ),
					'coupon'		=> __( 'A valid free shipping coupon', 'banda' ),
					'min_amount' 	=> __( 'A minimum order amount (defined below)', 'banda' ),
					'either' 		=> __( 'A minimum order amount OR a coupon', 'banda' ),
					'both' 			=> __( 'A minimum order amount AND a coupon', 'banda' ),
				)
			),
			'min_amount' => array(
				'title' 		=> __( 'Minimum Order Amount', 'banda' ),
				'type' 			=> 'price',
				'placeholder'	=> wc_format_localized_price( 0 ),
				'description' 	=> __( 'Users will need to spend this amount to get free shipping (if enabled above).', 'banda' ),
				'default' 		=> '0',
				'desc_tip'		=> true
			)
		);
	}

	/**
	 * is_available function.
	 * @param array $package
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( 'no' == $this->enabled ) {
			return false;
		}

		if ( 'specific' == $this->availability ) {
			$ship_to_countries = $this->countries;
		} else {
			$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
		}

		if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
			return false;
		}

		// Enabled logic
		$is_available       = false;
		$has_coupon         = false;
		$has_met_min_amount = false;

		if ( in_array( $this->requires, array( 'coupon', 'either', 'both' ) ) ) {

			if ( $coupons = WC()->cart->get_coupons() ) {
				foreach ( $coupons as $code => $coupon ) {
					if ( $coupon->is_valid() && $coupon->enable_free_shipping() ) {
						$has_coupon = true;
					}
				}
			}
		}

		if ( in_array( $this->requires, array( 'min_amount', 'either', 'both' ) ) && isset( WC()->cart->cart_contents_total ) ) {
			if ( WC()->cart->prices_include_tax ) {
				$total = WC()->cart->cart_contents_total + array_sum( WC()->cart->taxes );
			} else {
				$total = WC()->cart->cart_contents_total;
			}

			if ( $total >= $this->min_amount ) {
				$has_met_min_amount = true;
			}
		}

		switch ( $this->requires ) {
			case 'min_amount' :
				if ( $has_met_min_amount ) {
					$is_available = true;
				}
			break;
			case 'coupon' :
				if ( $has_coupon ) {
					$is_available = true;
				}
			break;
			case 'both' :
				if ( $has_met_min_amount && $has_coupon ) {
					$is_available = true;
				}
			break;
			case 'either' :
				if ( $has_met_min_amount || $has_coupon ) {
					$is_available = true;
				}
			break;
			default :
				$is_available = true;
			break;
		}

		return apply_filters( 'banda_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	/**
	 * calculate_shipping function.
	 * @return array
	 */
	public function calculate_shipping( $package = array() ) {
		$args = array(
			'id' 	  => $this->id,
			'label'   => $this->title,
			'cost' 	  => 0,
			'taxes'   => false,
			'package' => $package,
		);
		$this->add_rate( $args );
	}
}
