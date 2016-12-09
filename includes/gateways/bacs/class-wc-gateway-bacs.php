<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Bank Transfer Payment Gateway.
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class       WC_Gateway_BACS
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     Banda/Classes/Payment
 * @author      Mtaandao
 */
class WC_Gateway_BACS extends WC_Payment_Gateway {

	/** @var array Array of locales */
	public $locale;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'bacs';
		$this->icon               = apply_filters('banda_bacs_icon', '');
		$this->has_fields         = false;
		$this->method_title       = __( 'BACS', 'banda' );
		$this->method_description = __( 'Allows payments by BACS, more commonly known as direct bank/wire transfer.', 'banda' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions', $this->description );

		// BACS account fields shown on the thanks page and in emails
		$this->account_details = get_option( 'banda_bacs_accounts',
			array(
				array(
					'account_name'   => $this->get_option( 'account_name' ),
					'account_number' => $this->get_option( 'account_number' ),
					'sort_code'      => $this->get_option( 'sort_code' ),
					'bank_name'      => $this->get_option( 'bank_name' ),
					'iban'           => $this->get_option( 'iban' ),
					'bic'            => $this->get_option( 'bic' )
				)
			)
		);

		// Actions
		add_action( 'banda_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'banda_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );
		add_action( 'banda_thankyou_bacs', array( $this, 'thankyou_page' ) );

		// Customer Emails
		add_action( 'banda_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'banda' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bank Transfer', 'banda' ),
				'default' => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'banda' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'banda' ),
				'default'     => __( 'Direct Bank Transfer', 'banda' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'banda' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'banda' ),
				'default'     => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won\'t be shipped until the funds have cleared in our account.', 'banda' ),
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => __( 'Instructions', 'banda' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'banda' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'account_details' => array(
				'type'        => 'account_details'
			),
		);

	}

	/**
	 * Generate account details html.
	 *
	 * @return string
	 */
	public function generate_account_details_html() {

		ob_start();

		$country 	= WC()->countries->get_base_country();
		$locale		= $this->get_country_locale();

		// Get sortcode label in the $locale array and use appropriate one
		$sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort Code', 'banda' );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Account Details', 'banda' ); ?>:</th>
			<td class="forminp" id="bacs_accounts">
				<table class="widefat wc_input_table sortable" cellspacing="0">
					<thead>
						<tr>
							<th class="sort">&nbsp;</th>
							<th><?php _e( 'Account Name', 'banda' ); ?></th>
							<th><?php _e( 'Account Number', 'banda' ); ?></th>
							<th><?php _e( 'Bank Name', 'banda' ); ?></th>
							<th><?php echo $sortcode; ?></th>
							<th><?php _e( 'IBAN', 'banda' ); ?></th>
							<th><?php _e( 'BIC / Swift', 'banda' ); ?></th>
						</tr>
					</thead>
					<tbody class="accounts">
						<?php
						$i = -1;
						if ( $this->account_details ) {
							foreach ( $this->account_details as $account ) {
								$i++;

								echo '<tr class="account">
									<td class="sort"></td>
									<td><input type="text" value="' . esc_attr( wp_unslash( $account['account_name'] ) ) . '" name="bacs_account_name[' . $i . ']" /></td>
									<td><input type="text" value="' . esc_attr( $account['account_number'] ) . '" name="bacs_account_number[' . $i . ']" /></td>
									<td><input type="text" value="' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '" name="bacs_bank_name[' . $i . ']" /></td>
									<td><input type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="bacs_sort_code[' . $i . ']" /></td>
									<td><input type="text" value="' . esc_attr( $account['iban'] ) . '" name="bacs_iban[' . $i . ']" /></td>
									<td><input type="text" value="' . esc_attr( $account['bic'] ) . '" name="bacs_bic[' . $i . ']" /></td>
								</tr>';
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Account', 'banda' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)', 'banda' ); ?></a></th>
						</tr>
					</tfoot>
				</table>
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#bacs_accounts').on( 'click', 'a.add', function(){

							var size = jQuery('#bacs_accounts').find('tbody .account').length;

							jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bacs_account_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_account_number[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bank_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_sort_code[' + size + ']" /></td>\
									<td><input type="text" name="bacs_iban[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bic[' + size + ']" /></td>\
								</tr>').appendTo('#bacs_accounts table tbody');

							return false;
						});
					});
				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();

	}

	/**
	 * Save account details table.
	 */
	public function save_account_details() {

		$accounts = array();

		if ( isset( $_POST['bacs_account_name'] ) ) {

			$account_names   = array_map( 'wc_clean', $_POST['bacs_account_name'] );
			$account_numbers = array_map( 'wc_clean', $_POST['bacs_account_number'] );
			$bank_names      = array_map( 'wc_clean', $_POST['bacs_bank_name'] );
			$sort_codes      = array_map( 'wc_clean', $_POST['bacs_sort_code'] );
			$ibans           = array_map( 'wc_clean', $_POST['bacs_iban'] );
			$bics            = array_map( 'wc_clean', $_POST['bacs_bic'] );

			foreach ( $account_names as $i => $name ) {
				if ( ! isset( $account_names[ $i ] ) ) {
					continue;
				}

				$accounts[] = array(
					'account_name'   => $account_names[ $i ],
					'account_number' => $account_numbers[ $i ],
					'bank_name'      => $bank_names[ $i ],
					'sort_code'      => $sort_codes[ $i ],
					'iban'           => $ibans[ $i ],
					'bic'            => $bics[ $i ]
				);
			}
		}

		update_option( 'banda_bacs_accounts', $accounts );

	}

	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id
	 */
	public function thankyou_page( $order_id ) {

		if ( $this->instructions ) {
			echo wpautop( wptexturize( wp_kses_post( $this->instructions ) ) );
		}
		$this->bank_details( $order_id );

	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

		if ( ! $sent_to_admin && 'bacs' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
			$this->bank_details( $order->id );
		}

	}

	/**
	 * Get bank details and place into a list format.
	 *
	 * @param int $order_id
	 */
	private function bank_details( $order_id = '' ) {

		if ( empty( $this->account_details ) ) {
			return;
		}

		// Get order and store in $order
		$order 		= wc_get_order( $order_id );

		// Get the order country and country $locale
		$country 	= $order->billing_country;
		$locale		= $this->get_country_locale();

		// Get sortcode label in the $locale array and use appropriate one
		$sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort Code', 'banda' );

		$bacs_accounts = apply_filters( 'banda_bacs_accounts', $this->account_details );

		if ( ! empty( $bacs_accounts ) ) {
			echo '<h2 class="wc-bacs-bank-details-heading">' . __( 'Our Bank Details', 'banda' ) . '</h2>' . PHP_EOL;

			foreach ( $bacs_accounts as $bacs_account ) {

				$bacs_account = (object) $bacs_account;

				if ( $bacs_account->account_name || $bacs_account->bank_name ) {
					echo '<h3>' . wp_unslash( implode( ' - ', array_filter( array( $bacs_account->account_name, $bacs_account->bank_name ) ) ) ) . '</h3>' . PHP_EOL;
				}

				echo '<ul class="wc-bacs-bank-details order_details bacs_details">' . PHP_EOL;

				// BACS account fields shown on the thanks page and in emails
				$account_fields = apply_filters( 'banda_bacs_account_fields', array(
					'account_number'=> array(
						'label' => __( 'Account Number', 'banda' ),
						'value' => $bacs_account->account_number
					),
					'sort_code'     => array(
						'label' => $sortcode,
						'value' => $bacs_account->sort_code
					),
					'iban'          => array(
						'label' => __( 'IBAN', 'banda' ),
						'value' => $bacs_account->iban
					),
					'bic'           => array(
						'label' => __( 'BIC', 'banda' ),
						'value' => $bacs_account->bic
					)
				), $order_id );

				foreach ( $account_fields as $field_key => $field ) {
					if ( ! empty( $field['value'] ) ) {
						echo '<li class="' . esc_attr( $field_key ) . '">' . esc_attr( $field['label'] ) . ': <strong>' . wptexturize( $field['value'] ) . '</strong></li>' . PHP_EOL;
					}
				}

				echo '</ul>';
			}
		}

	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 'on-hold', __( 'Awaiting BACS payment', 'banda' ) );

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'    => 'success',
			'redirect'  => $this->get_return_url( $order )
		);

	}

	/**
	 * Get country locale if localized.
	 *
	 * @return array
	 */
	public function get_country_locale() {

		if ( empty( $this->locale ) ) {

			// Locale information to be used - only those that are not 'Sort Code'
			$this->locale = apply_filters( 'banda_get_bacs_locale', array(
				'AU' => array(
					'sortcode'	=> array(
						'label'		=> __( 'BSB', 'banda' ),
					),
				),
				'CA' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Bank Transit Number', 'banda' ),
					),
				),
				'IN' => array(
					'sortcode'	=> array(
						'label'		=> __( 'IFSC', 'banda' ),
					),
				),
				'IT' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Branch Sort', 'banda' ),
					),
				),
				'NZ' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Bank Code', 'banda' ),
					),
				),
				'SE' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Bank Code', 'banda' ),
					),
				),
				'US' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Routing Number', 'banda' ),
					),
				),
				'ZA' => array(
					'sortcode'	=> array(
						'label'		=> __( 'Branch Code', 'banda' ),
					),
				),
			) );

		}

		return $this->locale;

	}
}
