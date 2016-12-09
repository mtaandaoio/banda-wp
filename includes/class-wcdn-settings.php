<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Settings class
 */
if ( !class_exists( 'Banda_Delivery_Notes_Settings' ) ) {

	class Banda_Delivery_Notes_Settings {
			
		public $tab_name;
		public $hidden_submit;
		
		/**
		 * Constructor
		 */
		public function __construct() {		
			// Define default variables
			$this->tab_name = 'banda-delivery-notes';
			$this->hidden_submit = Banda_Delivery_Notes::$plugin_prefix . 'submit';
			
			// Load the hooks
			add_action( 'admin_init', array( $this, 'load_admin_hooks' ) );
		}
		
		/**
		 * Load the admin hooks
		 */
		public function load_admin_hooks() {	
			add_filter( 'banda_settings_tabs_array', array( $this, 'add_settings_tab' ), 100 );
			add_action( 'banda_settings_tabs_' . $this->tab_name, array( $this, 'create_settings_page' ) );
			add_action( 'banda_update_options_' . $this->tab_name, array( $this, 'save_settings_page' ) );
			add_action( 'current_screen', array( $this, 'load_screen_hooks' ) );
			add_action( 'wp_ajax_wcdn_load_thumbnail', array( $this, 'load_thumbnail_ajax' ) );
			add_action( 'wp_ajax_wcdn_reset_counter', array( $this, 'reset_invoice_counter_ajax' ) );
		}
		
		/**
		 * Add the scripts
		 */
		public function load_screen_hooks() {
			$screen = get_current_screen();

			if( $this->is_settings_page() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'add_styles' ) );
			}
		}

		/**
		 * Add the styles
		 */
		public function add_styles() {
			wp_enqueue_style( 'banda-delivery-notes-admin', Banda_Delivery_Notes::$plugin_url . 'css/admin.css' );
		}
		
		/**
		 * Add the scripts
		 */
		public function add_scripts() {		
			wp_enqueue_media();
			wp_enqueue_script( 'banda-delivery-notes-print-link', Banda_Delivery_Notes::$plugin_url . 'js/jquery.print-link.js', array( 'jquery' ) );
			wp_enqueue_script( 'banda-delivery-notes-admin', Banda_Delivery_Notes::$plugin_url . 'js/admin.js', array( 'jquery', 'custom-header', 'banda-delivery-notes-print-link' ) );

			// Localize the script strings
			$translation = array( 'resetCounter' => __( 'Do you really want to reset the counter to zero? This process can\'t be undone.', 'banda-delivery-notes' ) );
			wp_localize_script( 'banda-delivery-notes-admin', 'WCDNText', $translation );
		}
		
		/**
		 * Check if we are on settings page
		 */
		public function is_settings_page() {
			if( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && $_GET['tab'] == $this->tab_name ) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Check if sequential order plugin is activated
		 */
		public function is_banda_sequential_order_numbers_activated() {
			if ( in_array( 'banda-sequential-order-numbers/banda-sequential-order-numbers.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return true;
			} else {
				return false;
			}
		}
			
		/**
		 * Add a tab to the settings page
		 */
		public function add_settings_tab( $tabs ) {
			$tabs[$this->tab_name] = __( 'Print', 'banda-delivery-notes' );
			
			return $tabs;
		}

		/**
		 * Load thumbnail with ajax
		 */
		public function load_thumbnail_ajax() {
			$attachment_id = (int)$_POST['attachment_id']; 
			
			// Verify the id
			if( !$attachment_id ) {
				die();
			}
			
			// create the thumbnail
			$this->create_thumbnail( $attachment_id );
			
			exit;
		}
		
		/**
		 * Create the thumbnail
		 */
		public function create_thumbnail( $attachment_id ) {
			$attachment_src = wp_get_attachment_image_src( $attachment_id, 'medium', false );
			
			?>
			<img src="<?php echo $attachment_src[0]; ?>" alt="" />
			<?php
		}
		
		/**
		 * Reset the invoice number counter with ajax
		 */
		public function reset_invoice_counter_ajax() {
			if( !wp_verify_nonce( $_POST['nonce'], Banda_Delivery_Notes::$plugin_prefix . 'settings_nonce' ) || !isset( $_POST['reset'] ) ) {
				print_r('no reset');
				exit;
			}

			// Reset the counter
			update_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_counter', 0 );
			
			exit;
		}	
			
		/**
		 * Create the settings page content
		 */
		public function create_settings_page() {
			wp_nonce_field( Banda_Delivery_Notes::$plugin_prefix . 'settings_nonce', 'settings-nonce' );
			
			?>
			<h3><?php _e( 'Print Order', 'banda-delivery-notes' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr class="hide-if-no-js">
						<?php
						$attachment_id = get_option( Banda_Delivery_Notes::$plugin_prefix . 'company_logo_image_id' );
						?>
						<th>
							<label><?php _e( 'Company/Shop Logo', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<input id="company-logo-image-id" type="hidden" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>company_logo_image_id" class="regular-text" value="<?php echo $attachment_id ?>" />
							<div id="company-logo-placeholder"><?php if( !empty( $attachment_id ) ) : ?><?php $this->create_thumbnail( $attachment_id ); ?><?php endif; ?></div>
							<div id="company-logo-buttons">
								<a href="#" id="company-logo-remove-button" class="button" <?php if( empty( $attachment_id ) ) : ?>style="display: none;"<?php endif; ?>><?php _e( 'Remove Logo', 'banda-delivery-notes' ); ?></a>
								<a href="#" id="company-logo-add-button" class="button" <?php if( !empty( $attachment_id ) ) : ?>style="display: none;"<?php endif; ?> data-uploader-title="<?php echo esc_attr( __( 'Set Logo', 'banda-delivery-notes' ) ); ?>" data-uploader-button-title="<?php echo esc_attr( __( 'Set Logo', 'banda-delivery-notes' ) ); ?>"><?php _e( 'Set Logo', 'banda-delivery-notes' ); ?></a>
								<span id="company-logo-loader" class="spinner"></span>
							</div>
							<span class="description">
								<?php _e( 'A company/shop logo representing your business.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'When the image is printed, its pixel density will automatically be eight times higher than the original. This means, 1 printed inch will correspond to about 288 pixels on the screen. Example: an image with a width of 576 pixels and a height of 288 pixels will have a printed size of about 2 inches to 1 inch.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'Company/Shop Name', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<input type="text" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>custom_company_name" class="large-text" value="<?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'custom_company_name' ) ) ); ?>" />
							<span class="description">
								<?php _e( 'Your company/shop name for the Delivery Note.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'Leave blank to use the default Website/Blog title defined in WordPress settings. The name will be ignored when a Logo is set.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>company_address"><?php _e( 'Company/Shop Address', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<textarea name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>company_address" rows="5" class="large-text"><?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'company_address' ) ) ); ?></textarea>
							<span class="description">
								<?php _e( 'The postal address of the company/shop or even e-mail or telephone, which gets printed right after the company/shop name.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e('Leave blank to not print an address.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'Complimentary Close', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<textarea name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>personal_notes" rows="5" class="large-text"><?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'personal_notes' ) ) ); ?></textarea>
							<span class="description">
								<?php _e( 'Add some personal notes, or season greetings or whatever (e.g. Thank You for Your Order!, Merry Christmas!, etc.).', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e('Leave blank to not print any personal notes.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'Returns Policy, Conditions, etc', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<textarea name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>policies_conditions" rows="5" class="large-text"><?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'policies_conditions' ) ) ); ?></textarea>
							<span class="description">
								<?php _e( 'Here you can add some more policies, conditions etc. For example add a returns policy in case the client would like to send back some goods. In some countries (e.g. in the European Union) this is required so please add any required info in accordance with the statutory regulations.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong> 
								<?php _e('Leave blank to not print any policies or conditions.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>footer_imprint"><?php _e( 'Footer', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<textarea name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>footer_imprint" rows="5" class="large-text"><?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'footer_imprint' ) ) ); ?></textarea>
							<span class="description">
								<?php _e( 'Add some further footer imprint, e-mail, telephone, copyright notes etc. This makes the printed sheets a bit more branded.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong> 
								<?php _e('Leave blank to not print a footer.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>

			<h3><?php _e( 'Print Options', 'banda-delivery-notes' ); ?></h3>
			<p>
				<?php 
				// show template preview links when an order is available	
				$args = array(
					'post_type' => 'shop_order',
					'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' ),
					'posts_per_page' => 1
				);
				$query = new WP_Query( $args );
				
				if( $query->have_posts() ) : ?>
					<?php
					$results = $query->get_posts();
					$test_id = $results[0]->ID;
					$invoice_url = wcdn_get_print_link( $test_id, 'invoice' );
					$delivery_note_url = wcdn_get_print_link( $test_id, 'delivery-note' );
					$receipt_url = wcdn_get_print_link( $test_id, 'receipt' );
					?>
					<span class="description">
						<?php printf( __( 'You can preview the <a href="%1$s" target="%4$s" class="%5$s">invoice</a>, <a href="%2$s" target="%4$s" class="%5$s">delivery note</a> or <a href="%3$s" target="%4$s" class="%5$s">receipt</a> template.', 'banda-delivery-notes' ), $invoice_url, $delivery_note_url, $receipt_url, '_blank', '' ); ?>
						<?php _e( 'With the FAQ in the readme file you can learn how to customize the template.', 'banda-delivery-notes' ); ?>
					</span>
				<?php endif; ?>
			</p>
			<table class="form-table">
				<tbody>	
<!--
					<tr>
						<th>
							<label><?php _e( 'Style', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<input type="text" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>template_style" value="<?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'template_style' ) ) ); ?>" />
							</p>
							<span class="description">
								<?php _e( 'The default template style.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'Get more styles and advanced options with <a href="#">Banda Print Invoice & Delivery Note Pro</a>.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'Order fields', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								
							</p>
							<span class="description">
								<?php _e( 'The information that is shown for every Product.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>					<tr>
						<th>
							<label><?php _e( 'Product fields', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								coupon name, dimensions
							</p>
							<span class="description">
								<?php _e( 'The information that is shown for every Product.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
-->
					<tr>
						<th>
							<?php _e( 'Types', 'banda-delivery-notes' ); ?>
						</th>
						<td>
							<?php foreach( Banda_Delivery_Notes_Print::$templates as $template ) : ?>
																	
								<fieldset>
									<label>
										<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>template_type_<?php echo $template['type']; ?>" type="hidden" value="" />
										<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>template_type_<?php echo $template['type']; ?>" type="checkbox" value="1" <?php checked( get_option( Banda_Delivery_Notes::$plugin_prefix . 'template_type_' . $template['type'] ), 1 ); ?> />
										<?php _e( $template['labels']['setting'], 'banda-delivery-notes' ); ?>
									</label>
								</fieldset>
								
							<?php endforeach; ?>
						</td>
					</tr>
				</tbody>
			</table>

			<h3><?php _e( 'Theme Options', 'banda-delivery-notes' ); ?></h3>
			<table class="form-table">
				<tbody>	
					<tr>
						<th>
							<label><?php _e( 'Print Page Endpoint', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<input type="text" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>print_order_page_endpoint" value="<?php echo get_option( Banda_Delivery_Notes::$plugin_prefix . 'print_order_page_endpoint', 'print-order' ); ?>" />
							</p>
							<span class="description">
								<?php _e( 'The endpoint is appended to the accounts page URL to print the order. It should be unique.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<?php _e( 'My Account', 'banda-delivery-notes' ); ?>
						</th>
						<td>
							<fieldset>
								<label>
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>print_button_on_view_order_page" type="hidden" value="" />
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>print_button_on_view_order_page" type="checkbox" value="1" <?php checked( get_option( Banda_Delivery_Notes::$plugin_prefix . 'print_button_on_view_order_page' ), 1 ); ?> />
									<?php _e( 'Show print button on the "View Order" page', 'banda-delivery-notes' ); ?>
								</label>
							</fieldset>
							<fieldset>
								<label>
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>print_button_on_my_account_page" type="hidden" value="" />
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>print_button_on_my_account_page" type="checkbox" value="1" <?php checked( get_option( Banda_Delivery_Notes::$plugin_prefix . 'print_button_on_my_account_page' ), 1 ); ?> />
									<?php _e( 'Show print buttons on the "My Account" page', 'banda-delivery-notes' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th>
							<?php _e( 'Email', 'banda-delivery-notes' ); ?>
						</th>
						<td>
							<fieldset>
								<label>
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>email_print_link" type="hidden" value="" />
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>email_print_link" type="checkbox" value="1" <?php checked( get_option( Banda_Delivery_Notes::$plugin_prefix . 'email_print_link' ), 1 ); ?> />
									<?php _e( 'Show print link in customer emails', 'banda-delivery-notes' ); ?>
								</label>
							</fieldset>
							<span class="description">
								<?php _e( 'This includes the emails for a new, processing and completed order. On top of that the customer invoice email also includes the link.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h3><?php _e( 'Order Numbering', 'banda-delivery-notes' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<?php _e( 'Invoice Numbering', 'banda-delivery-notes' ); ?>
						</th>
						<td>
							<p>
								<label>
									<?php $create_invoice_number = get_option( Banda_Delivery_Notes::$plugin_prefix . 'create_invoice_number' ); ?>
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>create_invoice_number" type="hidden" value="" />
									<input name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>create_invoice_number" id="create-invoice-number" type="checkbox" value="1" <?php checked( $create_invoice_number, 1 ); ?> />
									<?php _e( 'Create invoice numbers', 'banda-delivery-notes' ); ?>
								</label>
							</p>
						</td>
					</tr>
					<tr class="invoice-number-row" <?php if( empty( $create_invoice_number ) ) : ?> style="display: none;"<?php endif; ?>>
						<th>
							<label><?php _e( 'Invoice Number Start', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<input type="number" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>invoice_number_start" value="<?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_start', 1 ) ) ); ?>" />
							</p>
							<span class="description">
								<?php _e( 'Start the numbering at the specified number.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'Use only integers.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr class="invoice-number-row" <?php if( empty( $create_invoice_number ) ) : ?> style="display: none;"<?php endif; ?>>
						<th>
							<label><?php _e( 'Invoice Number Prefix', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<input type="text" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>invoice_number_prefix" value="<?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_prefix' ) ) ); ?>" />
							</p>
							<span class="description">
								<?php _e( 'This text will be prepended to the invoice number.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'Leave blank to not add a prefix.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr class="invoice-number-row" <?php if( empty( $create_invoice_number ) ) : ?> style="display: none;"<?php endif; ?>>
						<th>
							<label><?php _e( 'Invoice Number Suffix', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<input type="text" name="<?php echo Banda_Delivery_Notes::$plugin_prefix; ?>invoice_number_suffix" value="<?php echo stripslashes( wp_kses_stripslashes( get_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_suffix' ) ) ); ?>" />
							</p>
							<span class="description">
								<?php _e( 'This text will be appended to the invoice number.', 'banda-delivery-notes' ); ?>
								<strong><?php _e( 'Note:', 'banda-delivery-notes' ); ?></strong>
								<?php _e( 'Leave blank to not add a suffix.', 'banda-delivery-notes' ); ?>
							</span>
						</td>
					</tr>
					<tr class="invoice-number-row" <?php if( empty( $create_invoice_number ) ) : ?> style="display: none;"<?php endif; ?>>
						<th>
							<label><?php _e( 'Invoice Number Counter', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
								<?php
									$invoice_start = intval( get_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_start', 1 ) );
									$invoice_counter = intval( get_option( Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_counter', 0 ) );
								?>
								<span id="invoice-counter-value"><?php echo $invoice_counter; ?></span>
								<a href="#" id="reset-invoice-counter" class="button"><?php _e( 'Reset Counter …', 'banda-delivery-notes' ); ?></a>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'Sequential Order Number', 'banda-delivery-notes' ); ?></label>
						</th>
						<td>
							<p>
							<?php if( $this->is_banda_sequential_order_numbers_activated() ) : ?>
								<?php _e( 'Sequential numbering is enabled.', 'banda-delivery-notes' ); ?>
							<?php else : ?>
								<?php printf( __( 'Install and activate the free <a href="%s">Banda Sequential Order Numbers</a> Plugin.', 'banda-delivery-notes' ), 'http://wordpress.org/extend/plugins/banda-sequential-order-numbers/' ); ?>
							<?php endif; ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<input type="hidden" name="<?php echo $this->hidden_submit; ?>" value="submitted">
			<?php
		}
		
		/**
		 * Save all settings
		 */
		public function save_settings_page() {
			if ( isset( $_POST[ $this->hidden_submit ] ) && $_POST[ $this->hidden_submit ] == 'submitted' ) {
				
				if( empty( $_POST[Banda_Delivery_Notes::$plugin_prefix . 'template_type_invoice'] ) &&
					empty( $_POST[Banda_Delivery_Notes::$plugin_prefix . 'template_type_delivery-note'] ) &&
					empty( $_POST[Banda_Delivery_Notes::$plugin_prefix . 'template_type_receipt'] ) ) {
					$_POST[Banda_Delivery_Notes::$plugin_prefix . 'template_type_invoice'] = 1;
					$_POST[Banda_Delivery_Notes::$plugin_prefix . 'template_type_delivery-note'] = 1;
				}
				
				// Save settings
				foreach ( $_POST as $key => $value ) {
					if ( $key != $this->hidden_submit && strpos( $key, Banda_Delivery_Notes::$plugin_prefix ) !== false ) {
						// Set a default values
						if ( empty( $value ) ) {
							if ( $key == Banda_Delivery_Notes::$plugin_prefix . 'print_order_page_endpoint' ) {
								$value = 'print-order';
							}
							
							if ( $key == Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_start' ) {
								$value = 0;
							}
						}
						
						// Sanitize values
						if ( $key == Banda_Delivery_Notes::$plugin_prefix . 'print_order_page_endpoint' ) {
							$value = sanitize_title( $value );
						}
						
						if ( $key == Banda_Delivery_Notes::$plugin_prefix . 'invoice_number_start' ) {
							if ( !ctype_digit( $value ) ) {
								$value = 0;
							}
						}
						
						// Update the value
						if ( empty( $value ) ) {
							delete_option( $key );
						} else {
							if ( get_option( $key ) && get_option( $key ) != $value ) {
								update_option( $key, $value );
							}
							else {
								add_option( $key, $value );
							}
						}
					}
				}
				
				// Flush permalink structs with 
				set_transient( Banda_Delivery_Notes::$plugin_prefix . 'flush_rewrite_rules', true );
			}
		}
	
	}
	
}

?>