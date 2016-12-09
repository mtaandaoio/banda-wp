<?php
/**
 * Banda Account Settings
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_Accounts' ) ) :

/**
 * WC_Settings_Accounts.
 */
class WC_Settings_Accounts extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'account';
		$this->label = __( 'Accounts', 'banda' );

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
		$settings = apply_filters( 'banda_' . $this->id . '_settings', array(

			array( 'title' => __( 'Account Pages', 'banda' ), 'type' => 'title', 'desc' => __( 'These pages need to be set so that Banda knows where to send users to access account related functionality.', 'banda' ), 'id' => 'account_page_options' ),

			array(
				'title'    => __( 'My Account Page', 'banda' ),
				'desc'     => __( 'Page contents:', 'banda' ) . ' [' . apply_filters( 'banda_my_account_shortcode_tag', 'banda_my_account' ) . ']',
				'id'       => 'banda_myaccount_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => 'account_page_options' ),

			array(	'title' => '', 'type' => 'title', 'id' => 'account_registration_options' ),

			array(
				'title'         => __( 'Enable Registration', 'banda' ),
				'desc'          => __( 'Enable registration on the "Checkout" page', 'banda' ),
				'id'            => 'banda_enable_signup_and_login_from_checkout',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Enable registration on the "My Account" page', 'banda' ),
				'id'            => 'banda_enable_myaccount_registration',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false
			),

			array(
				'title'         => __( 'Login', 'banda' ),
				'desc'          => __( 'Display returning customer login reminder on the "Checkout" page', 'banda' ),
				'id'            => 'banda_enable_checkout_login_reminder',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'title'         => __( 'Account Creation', 'banda' ),
				'desc'          => __( 'Automatically generate username from customer email', 'banda' ),
				'id'            => 'banda_registration_generate_username',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Automatically generate customer password', 'banda' ),
				'id'            => 'banda_registration_generate_password',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false
			),

			array( 'type' => 'sectionend', 'id' => 'account_registration_options' ),

			array( 'title' => __( 'My Account Endpoints', 'banda' ), 'type' => 'title', 'desc' => __( 'Endpoints are appended to your page URLs to handle specific actions on the accounts pages. They should be unique and can be left blank to disable the endpoint.', 'banda' ), 'id' => 'account_endpoint_options' ),

			array(
				'title'    => __( 'Orders', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Orders page', 'banda' ),
				'id'       => 'banda_myaccount_orders_endpoint',
				'type'     => 'text',
				'default'  => 'orders',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'View Order', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; View Order page', 'banda' ),
				'id'       => 'banda_myaccount_view_order_endpoint',
				'type'     => 'text',
				'default'  => 'view-order',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Downloads', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Downloads page', 'banda' ),
				'id'       => 'banda_myaccount_downloads_endpoint',
				'type'     => 'text',
				'default'  => 'downloads',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Edit Account', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Edit Account page', 'banda' ),
				'id'       => 'banda_myaccount_edit_account_endpoint',
				'type'     => 'text',
				'default'  => 'edit-account',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Addresses', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Addresses page', 'banda' ),
				'id'       => 'banda_myaccount_edit_address_endpoint',
				'type'     => 'text',
				'default'  => 'edit-address',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Payment Methods', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Payment Methods page', 'banda' ),
				'id'       => 'banda_myaccount_payment_methods_endpoint',
				'type'     => 'text',
				'default'  => 'payment-methods',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Lost Password', 'banda' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Lost Password page', 'banda' ),
				'id'       => 'banda_myaccount_lost_password_endpoint',
				'type'     => 'text',
				'default'  => 'lost-password',
				'desc_tip' => true,
			),

			array(
				'title' => __( 'Logout', 'banda' ),
				'desc'     => __( 'Endpoint for the triggering logout. You can add this to your menus via a custom link: yoursite.com/?customer-logout=true', 'banda' ),
				'id'       => 'banda_logout_endpoint',
				'type'     => 'text',
				'default'  => 'customer-logout',
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => 'account_endpoint_options' ),

		) );

		return apply_filters( 'banda_get_settings_' . $this->id, $settings );
	}
}

endif;

return new WC_Settings_Accounts();
