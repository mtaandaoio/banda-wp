<?php
/**
 * Banda General Settings
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_General' ) ) :

/**
 * WC_Admin_Settings_General.
 */
class WC_Settings_General extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'banda' );

		add_filter( 'banda_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'banda_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'banda_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$currency_code_options = get_banda_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_banda_currency_symbol( $code ) . ')';
		}

		$settings = apply_filters( 'banda_general_settings', array(

			array( 'title' => __( 'General Options', 'banda' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title'    => __( 'Base Location', 'banda' ),
				'desc'     => __( 'This is the base location for your business. Tax rates will be based on this country.', 'banda' ),
				'id'       => 'banda_default_country',
				'css'      => 'min-width:350px;',
				'default'  => 'GB',
				'type'     => 'single_select_country',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Selling Location(s)', 'banda' ),
				'desc'     => __( 'This option lets you limit which countries you are willing to sell to.', 'banda' ),
				'id'       => 'banda_allowed_countries',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' =>  true,
				'options'  => array(
					'all'        => __( 'Sell to All Countries', 'banda' ),
					'all_except' => __( 'Sell to All Countries, Except For&hellip;', 'banda' ),
					'specific'   => __( 'Sell to Specific Countries', 'banda' )
				)
			),

			array(
				'title'   => __( 'Sell to All Countries, Except For&hellip;', 'banda' ),
				'desc'    => '',
				'id'      => 'banda_all_except_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'   => __( 'Sell to Specific Countries', 'banda' ),
				'desc'    => '',
				'id'      => 'banda_specific_allowed_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'    => __( 'Shipping Location(s)', 'banda' ),
				'desc'     => __( 'Choose which countries you want to ship to, or choose to ship to all locations you sell to.', 'banda' ),
				'id'       => 'banda_ship_to_countries',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => true,
				'options'  => array(
					''         => __( 'Ship to all countries you sell to', 'banda' ),
					'all'      => __( 'Ship to all countries', 'banda' ),
					'specific' => __( 'Ship to specific countries only', 'banda' ),
					'disabled' => __( 'Disable shipping &amp; shipping calculations', 'banda' ),
				)
			),

			array(
				'title'   => __( 'Ship to Specific Countries', 'banda' ),
				'desc'    => '',
				'id'      => 'banda_specific_ship_to_countries',
				'css'     => '',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'    => __( 'Default Customer Location', 'banda' ),
				'id'       => 'banda_default_customer_address',
				'desc_tip' =>  __( 'This option determines a customers default location. The MaxMind GeoLite Database will be periodically downloaded to your wp-content directory if using geolocation.', 'banda' ),
				'default'  => 'geolocation',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					''                 => __( 'No location by default', 'banda' ),
					'base'             => __( 'Shop base address', 'banda' ),
					'geolocation'      => __( 'Geolocate', 'banda' ),
					'geolocation_ajax' => __( 'Geolocate (with page caching support)', 'banda' ),
				),
			),

			array(
				'title'   => __( 'Enable Taxes', 'banda' ),
				'desc'    => __( 'Enable taxes and tax calculations', 'banda' ),
				'id'      => 'banda_calc_taxes',
				'default' => 'no',
				'type'    => 'checkbox'
			),

			array(
				'title'   => __( 'Store Notice', 'banda' ),
				'desc'    => __( 'Enable site-wide store notice text', 'banda' ),
				'id'      => 'banda_demo_store',
				'default' => 'no',
				'type'    => 'checkbox'
			),

			array(
				'title'    => __( 'Store Notice Text', 'banda' ),
				'desc'     => '',
				'id'       => 'banda_demo_store_notice',
				'default'  => __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'banda' ),
				'type'     => 'textarea',
				'css'     => 'width:350px; height: 65px;',
				'autoload' => false
			),

			array( 'type' => 'sectionend', 'id' => 'general_options'),

			array( 'title' => __( 'Currency Options', 'banda' ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'banda' ), 'id' => 'pricing_options' ),

			array(
				'title'    => __( 'Currency', 'banda' ),
				'desc'     => __( 'This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.', 'banda' ),
				'id'       => 'banda_currency',
				'css'      => 'min-width:350px;',
				'default'  => 'GBP',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' =>  true,
				'options'  => $currency_code_options
			),

			array(
				'title'    => __( 'Currency Position', 'banda' ),
				'desc'     => __( 'This controls the position of the currency symbol.', 'banda' ),
				'id'       => 'banda_currency_pos',
				'css'      => 'min-width:350px;',
				'class'    => 'wc-enhanced-select',
				'default'  => 'left',
				'type'     => 'select',
				'options'  => array(
					'left'        => __( 'Left', 'banda' ) . ' (' . get_banda_currency_symbol() . '99.99)',
					'right'       => __( 'Right', 'banda' ) . ' (99.99' . get_banda_currency_symbol() . ')',
					'left_space'  => __( 'Left with space', 'banda' ) . ' (' . get_banda_currency_symbol() . ' 99.99)',
					'right_space' => __( 'Right with space', 'banda' ) . ' (99.99 ' . get_banda_currency_symbol() . ')'
				),
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Thousand Separator', 'banda' ),
				'desc'     => __( 'This sets the thousand separator of displayed prices.', 'banda' ),
				'id'       => 'banda_price_thousand_sep',
				'css'      => 'width:50px;',
				'default'  => ',',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Decimal Separator', 'banda' ),
				'desc'     => __( 'This sets the decimal separator of displayed prices.', 'banda' ),
				'id'       => 'banda_price_decimal_sep',
				'css'      => 'width:50px;',
				'default'  => '.',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Number of Decimals', 'banda' ),
				'desc'     => __( 'This sets the number of decimal points shown in displayed prices.', 'banda' ),
				'id'       => 'banda_price_num_decimals',
				'css'      => 'width:50px;',
				'default'  => '2',
				'desc_tip' =>  true,
				'type'     => 'number',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1
				)
			),

			array( 'type' => 'sectionend', 'id' => 'pricing_options' )

		) );

		return apply_filters( 'banda_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output a colour picker input box.
	 *
	 * @param mixed $name
	 * @param string $id
	 * @param mixed $value
	 * @param string $desc (default: '')
	 */
	public function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box">' . wc_help_tip( $desc ) . '
			<input name="' . esc_attr( $id ). '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WC_Settings_General();
