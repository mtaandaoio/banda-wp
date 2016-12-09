<?php
/**
 * Banda Shipping Settings
 *
 * @author   Mtaandao
 * @category Admin
 * @package  Banda/Admin
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Settings_Payment_Gateways' ) ) :

/**
 * WC_Settings_Payment_Gateways.
 */
class WC_Settings_Payment_Gateways extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'checkout';
		$this->label = _x( 'Checkout', 'Settings tab label', 'banda' );

		add_filter( 'banda_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'banda_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'banda_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'banda_admin_field_payment_gateways', array( $this, 'payment_gateways_setting' ) );
		add_action( 'banda_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'Checkout Options', 'banda' )
		);

		if ( ! defined( 'WC_INSTALLING' ) ) {
			// Load shipping methods so we can show any global options they may have.
			$payment_gateways = WC()->payment_gateways->payment_gateways();

			foreach ( $payment_gateways as $gateway ) {
				$title = empty( $gateway->method_title ) ? ucfirst( $gateway->id ) : $gateway->method_title;
				$sections[ strtolower( $gateway->id ) ] = esc_html( $title );
			}
		}

		return apply_filters( 'banda_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'banda_payment_gateways_settings', array(

			array(
				'title' => __( 'Checkout Process', 'banda' ),
				'type'  => 'title',
				'id'    => 'checkout_process_options',
			),

			array(
				'title'         => __( 'Coupons', 'banda' ),
				'desc'          => __( 'Enable the use of coupons', 'banda' ),
				'id'            => 'banda_enable_coupons',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'desc_tip'      =>  __( 'Coupons can be applied from the cart and checkout pages.', 'banda' ),
			),

			array(
				'desc'          => __( 'Calculate coupon discounts sequentially', 'banda' ),
				'id'            => 'banda_calc_discounts_sequentially',
				'default'       => 'no',
				'type'          => 'checkbox',
				'desc_tip'      =>  __( 'When applying multiple coupons, apply the first coupon to the full price and the second coupon to the discounted price and so on.', 'banda' ),
				'checkboxgroup' => 'end',
				'autoload'      => false,
			),

			array(
				'title'         => _x( 'Checkout Process', 'Settings group label', 'banda' ),
				'desc'          => __( 'Enable guest checkout', 'banda' ),
				'desc_tip'      =>  __( 'Allows customers to checkout without creating an account.', 'banda' ),
				'id'            => 'banda_enable_guest_checkout',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false,
			),

			array(
				'desc'            => __( 'Force secure checkout', 'banda' ),
				'id'              => 'banda_force_ssl_checkout',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'option',
				'desc_tip'        => sprintf( __( 'Force SSL (HTTPS) on the checkout pages (<a href="%s">an SSL Certificate is required</a>).', 'banda' ), 'https://docs.mtaandao.co.ke/document/ssl-and-https/#section-3' ),
			),

			'unforce_ssl_checkout' => array(
				'desc'            => __( 'Force HTTP when leaving the checkout', 'banda' ),
				'id'              => 'banda_unforce_ssl_checkout',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'end',
				'show_if_checked' => 'yes',
			),

			array(
				'type' => 'sectionend',
				'id' => 'checkout_process_options',
			),

			array(
				'title' => __( 'Checkout Pages', 'banda' ),
				'desc'  => __( 'These pages need to be set so that Banda knows where to send users to checkout.', 'banda' ),
				'type'  => 'title',
				'id'    => 'checkout_page_options',
			),

			array(
				'title'    => __( 'Cart Page', 'banda' ),
				'desc'     => __( 'Page contents:', 'banda' ) . ' [' . apply_filters( 'banda_cart_shortcode_tag', 'banda_cart' ) . ']',
				'id'       => 'banda_cart_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Checkout Page', 'banda' ),
				'desc'     => __( 'Page contents:', 'banda' ) . ' [' . apply_filters( 'banda_checkout_shortcode_tag', 'banda_checkout' ) . ']',
				'id'       => 'banda_checkout_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Terms and Conditions', 'banda' ),
				'desc'     => __( 'If you define a "Terms" page the customer will be asked if they accept them when checking out.', 'banda' ),
				'id'       => 'banda_terms_page_id',
				'default'  => '',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
				'type'     => 'single_select_page',
				'desc_tip' => true,
				'autoload' => false,
			),

			array(
				'type' => 'sectionend',
				'id' => 'checkout_page_options',
			),

			array( 'title' => __( 'Checkout Endpoints', 'banda' ), 'type' => 'title', 'desc' => __( 'Endpoints are appended to your page URLs to handle specific actions during the checkout process. They should be unique.', 'banda' ), 'id' => 'account_endpoint_options' ),

			array(
				'title'    => __( 'Pay', 'banda' ),
				'desc'     => __( 'Endpoint for the Checkout &rarr; Pay page', 'banda' ),
				'id'       => 'banda_checkout_pay_endpoint',
				'type'     => 'text',
				'default'  => 'order-pay',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Order Received', 'banda' ),
				'desc'     => __( 'Endpoint for the Checkout &rarr; Order Received page', 'banda' ),
				'id'       => 'banda_checkout_order_received_endpoint',
				'type'     => 'text',
				'default'  => 'order-received',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Add Payment Method', 'banda' ),
				'desc'     => __( 'Endpoint for the Checkout &rarr; Add Payment Method page', 'banda' ),
				'id'       => 'banda_myaccount_add_payment_method_endpoint',
				'type'     => 'text',
				'default'  => 'add-payment-method',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Delete Payment Method', 'banda' ),
				'desc'     => __( 'Endpoint for the delete payment method page', 'banda' ),
				'id'       => 'banda_myaccount_delete_payment_method_endpoint',
				'type'     => 'text',
				'default'  => 'delete-payment-method',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Set Default Payment Method', 'banda' ),
				'desc'     => __( 'Endpoint for the setting a default payment method page', 'banda' ),
				'id'       => 'banda_myaccount_set_default_payment_method_endpoint',
				'type'     => 'text',
				'default'  => 'set-default-payment-method',
				'desc_tip' => true,
			),


			array(
				'type' => 'sectionend',
				'id' => 'checkout_endpoint_options',
			),

			array(
				'title' => __( 'Payment Gateways', 'banda' ),
				'desc'  => __( 'Installed gateways are listed below. Drag and drop gateways to control their display order on the frontend.', 'banda' ),
				'type'  => 'title',
				'id'    => 'payment_gateways_options',
			),

			array(
				'type' => 'payment_gateways',
			),

			array(
				'type' => 'sectionend',
				'id' => 'payment_gateways_options',
			),

		) );

		if ( wc_site_is_https() ) {
			unset( $settings['unforce_ssl_checkout'] );
		}

		return apply_filters( 'banda_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		// Load shipping methods so we can show any global options they may have.
		$payment_gateways = WC()->payment_gateways->payment_gateways();

		if ( $current_section ) {
			foreach ( $payment_gateways as $gateway ) {
				if ( in_array( $current_section, array( $gateway->id, sanitize_title( get_class( $gateway ) ) ) ) ) {
					$gateway->admin_options();
					break;
				}
			}
		} else {
			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Output payment gateway settings.
	 */
	public function payment_gateways_setting() {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Gateway Display Order', 'banda' ) ?></th>
			<td class="forminp">
				<table class="wc_gateways widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = apply_filters( 'banda_payment_gateways_setting_columns', array(
									'sort'     => '',
									'name'     => __( 'Gateway', 'banda' ),
									'id'       => __( 'Gateway ID', 'banda' ),
									'status'   => __( 'Enabled', 'banda' )
								) );

								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {

							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {

									case 'sort' :
										echo '<td width="1%" class="sort">
											<input type="hidden" name="gateway_order[]" value="' . esc_attr( $gateway->id ) . '" />
										</td>';
										break;

									case 'name' :
										$method_title = $gateway->get_title() ? $gateway->get_title() : __( '(no title)', 'banda' );
										echo '<td class="name">
											<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( $gateway->id ) ) . '">' . esc_html( $method_title ) . '</a>
										</td>';
										break;

									case 'id' :
										echo '<td class="id">' . esc_html( $gateway->id ) . '</td>';
										break;

									case 'status' :
										echo '<td class="status">';

										if ( $gateway->enabled == 'yes' )
											echo '<span class="status-enabled tips" data-tip="' . __ ( 'Yes', 'banda' ) . '">' . __ ( 'Yes', 'banda' ) . '</span>';
										else
											echo '-';

										echo '</td>';
										break;

									default :
										do_action( 'banda_payment_gateways_setting_column_' . $key, $gateway );
										break;
								}
							}

							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$wc_payment_gateways = WC_Payment_Gateways::instance();

		if ( ! $current_section ) {
			WC_Admin_Settings::save_fields( $this->get_settings() );
			$wc_payment_gateways->process_admin_options();

		} else {
			foreach ( $wc_payment_gateways->payment_gateways() as $gateway ) {
				if ( in_array( $current_section, array( $gateway->id, sanitize_title( get_class( $gateway ) ) ) ) ) {
					do_action( 'banda_update_options_payment_gateways_' . $gateway->id );
					$wc_payment_gateways->init();
				}
			}
		}
	}
}

endif;

return new WC_Settings_Payment_Gateways();
