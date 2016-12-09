<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Local Delivery Shipping Method.
 *
 * This class is here for backwards commpatility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version		2.3.0
 * @package		Banda/Classes/Shipping
 * @author 		Mtaandao
 */
class WC_Shipping_Legacy_Local_Delivery extends WC_Shipping_Local_Pickup {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                 = 'legacy_local_delivery';
		$this->method_title       = __( 'Local Delivery (Legacy)', 'banda' );
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
		return $this->plugin_id . 'local_delivery' . '_settings';
	}

	/**
	 * init function.
	 */
	public function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title        = $this->get_option( 'title' );
		$this->type         = $this->get_option( 'type' );
		$this->fee          = $this->get_option( 'fee' );
		$this->type         = $this->get_option( 'type' );
		$this->codes        = $this->get_option( 'codes' );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );

		add_action( 'banda_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * calculate_shipping function.
	 *
	 * @param array $package (default: array())
	 */
	public function calculate_shipping( $package = array() ) {
		$shipping_total = 0;

		switch ( $this->type ) {
			case 'fixed' :
				$shipping_total = $this->fee;
			break;
			case 'percent' :
				$shipping_total = $package['contents_cost'] * ( $this->fee / 100 );
			break;
			case 'product' :
				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
						$shipping_total += $this->fee * $values['quantity'];
					}
				}
			break;
		}

		$rate = array(
			'id'      => $this->id,
			'label'   => $this->title,
			'cost'    => $shipping_total,
			'package' => $package,
		);

		$this->add_rate( $rate );
	}

	/**
	 * Init form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable', 'banda' ),
				'type'    => 'checkbox',
				'label'   => __( 'Once disabled, this legacy method will no longer be available.', 'banda' ),
				'default' => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'banda' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'banda' ),
				'default'     => __( 'Local Delivery', 'banda' ),
				'desc_tip'    => true,
			),
			'type' => array(
				'title'       => __( 'Fee Type', 'banda' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => __( 'How to calculate delivery charges', 'banda' ),
				'default'     => 'fixed',
				'options'     => array(
				'fixed'       => __( 'Fixed amount', 'banda' ),
					'percent'     => __( 'Percentage of cart total', 'banda' ),
					'product'     => __( 'Fixed amount per product', 'banda' ),
				),
				'desc_tip'    => true,
			),
			'fee' => array(
				'title'       => __( 'Delivery Fee', 'banda' ),
				'type'        => 'price',
				'description' => __( 'What fee do you want to charge for local delivery, disregarded if you choose free. Leave blank to disable.', 'banda' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => wc_format_localized_price( 0 )
			),
			'codes' => array(
				'title'       => __( 'Allowed ZIP/Post Codes', 'banda' ),
				'type'        => 'text',
				'desc_tip'    => __( 'What ZIP/post codes are available for local delivery?', 'banda' ),
				'default'     => '',
				'description' => __( 'Separate codes with a comma. Accepts wildcards, e.g. <code>P*</code> will match a postcode of PE30. Also accepts a pattern, e.g. <code>NG1___</code> would match NG1 1AA but not NG10 1AA', 'banda' ),
				'placeholder' => 'e.g. 12345, 56789'
			),
			'availability' => array(
				'title'       => __( 'Method availability', 'banda' ),
				'type'        => 'select',
				'default'     => 'all',
				'class'       => 'availability wc-enhanced-select',
				'options'     => array(
					'all'         => __( 'All allowed countries', 'banda' ),
					'specific'    => __( 'Specific Countries', 'banda' )
				)
			),
			'countries' => array(
				'title'       => __( 'Specific Countries', 'banda' ),
				'type'        => 'multiselect',
				'class'       => 'wc-enhanced-select',
				'css'         => 'width: 450px;',
				'default'     => '',
				'options'     => WC()->countries->get_shipping_countries(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select some countries', 'banda' )
				)
			)
		);
	}
}