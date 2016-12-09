<?php
/**
 * Banda Email Settings
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_Emails' ) ) :

/**
 * WC_Settings_Emails.
 */
class WC_Settings_Emails extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = __( 'Emails', 'banda' );

		add_filter( 'banda_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'banda_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'banda_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'banda_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'banda_admin_field_email_notification', array( $this, 'email_notification_setting' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'Email Options', 'banda' )
		);
		return apply_filters( 'banda_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'banda_email_settings', array(

			array( 'title' => __( 'Email Notifications', 'banda' ),  'desc' => __( 'Email notifications sent from Banda are listed below. Click on an email to configure it.', 'banda' ), 'type' => 'title', 'id' => 'email_notification_settings' ),

			array( 'type' => 'email_notification' ),

			array( 'type' => 'sectionend', 'id' => 'email_notification_settings' ),

			array( 'type' => 'sectionend', 'id' => 'email_recipient_options' ),

			array( 'title' => __( 'Email Sender Options', 'banda' ), 'type' => 'title', 'desc' => '', 'id' => 'email_options' ),

			array(
				'title'    => __( '"From" Name', 'banda' ),
				'desc'     => __( 'How the sender\'s name appears in outgoing Banda emails.', 'banda' ),
				'id'       => 'banda_email_from_name',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
				'default'  => esc_attr( get_bloginfo( 'name', 'display' ) ),
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'             => __( '"From" Address', 'banda' ),
				'desc'              => __( 'How the sender\'s email appears in outgoing Banda emails.', 'banda' ),
				'id'                => 'banda_email_from_address',
				'type'              => 'email',
				'custom_attributes' => array(
					'multiple' => 'multiple'
				),
				'css'               => 'min-width:300px;',
				'default'           => get_option( 'admin_email' ),
				'autoload'          => false,
				'desc_tip'          => true
			),

			array( 'type' => 'sectionend', 'id' => 'email_options' ),

			array( 'title' => __( 'Email Template', 'banda' ), 'type' => 'title', 'desc' => sprintf(__( 'This section lets you customize the Banda emails. <a href="%s" target="_blank">Click here to preview your email template</a>.', 'banda' ), wp_nonce_url( admin_url( '?preview_banda_mail=true' ), 'preview-mail' ) ), 'id' => 'email_template_options' ),

			array(
				'title'       => __( 'Header Image', 'banda' ),
				'desc'        => __( 'URL to an image you want to show in the email header. Upload images using the media uploader (Admin > Media).', 'banda' ),
				'id'          => 'banda_email_header_image',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'placeholder' => __( 'N/A', 'banda' ),
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true
			),

			array(
				'title'       => __( 'Footer Text', 'banda' ),
				'desc'        => __( 'The text to appear in the footer of Banda emails.', 'banda' ),
				'id'          => 'banda_email_footer_text',
				'css'         => 'width:300px; height: 75px;',
				'placeholder' => __( 'N/A', 'banda' ),
				'type'        => 'textarea',
				'default'     => get_bloginfo( 'name', 'display' ) . ' - ' . __( 'Powered by Banda', 'banda' ),
				'autoload'    => false,
				'desc_tip'    => true
			),

			array(
				'title'    => __( 'Base Colour', 'banda' ),
				'desc'     => __( 'The base colour for Banda email templates. Default <code>#557da1</code>.', 'banda' ),
				'id'       => 'banda_email_base_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#557da1',
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'    => __( 'Background Colour', 'banda' ),
				'desc'     => __( 'The background colour for Banda email templates. Default <code>#f5f5f5</code>.', 'banda' ),
				'id'       => 'banda_email_background_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#f5f5f5',
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'    => __( 'Body Background Colour', 'banda' ),
				'desc'     => __( 'The main body background colour. Default <code>#fdfdfd</code>.', 'banda' ),
				'id'       => 'banda_email_body_background_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#fdfdfd',
				'autoload' => false,
				'desc_tip' => true
			),

			array(
				'title'    => __( 'Body Text Colour', 'banda' ),
				'desc'     => __( 'The main body text colour. Default <code>#505050</code>.', 'banda' ),
				'id'       => 'banda_email_text_color',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#505050',
				'autoload' => false,
				'desc_tip' => true
			),

			array( 'type' => 'sectionend', 'id' => 'email_notification_settings' ),

		) );

		return apply_filters( 'banda_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		// Define emails that can be customised here
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		if ( $current_section ) {
			foreach ( $email_templates as $email_key => $email ) {
				if ( strtolower( $email_key ) == $current_section ) {
					$email->admin_options();
					break;
				}
			}
		} else {
			$settings = $this->get_settings();
			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {
			WC_Admin_Settings::save_fields( $this->get_settings() );

		} else {
			$wc_emails = WC_Emails::instance();

			if ( in_array( $current_section, array_map( 'sanitize_title', array_keys( $wc_emails->get_emails() ) ) ) ) {
				foreach ( $wc_emails->get_emails() as $email_id => $email ) {
					if ( $current_section === sanitize_title( $email_id ) ) {
						do_action( 'banda_update_options_' . $this->id . '_' . $email->id );
					}
				}
			} else {
				do_action( 'banda_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}

	/**
	 * Output email notification settings.
	 */
	public function email_notification_setting() {
		// Define emails that can be customised here
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();
		?>
		<tr valign="top">
		    <td class="wc_emails_wrapper" colspan="2">
				<table class="wc_emails widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = apply_filters( 'banda_email_setting_columns', array(
									'status'     => '',
									'name'       => __( 'Email', 'banda' ),
									'email_type' => __( 'Content Type', 'banda' ),
									'recipient'  => __( 'Recipient(s)', 'banda' ),
									'actions'    => ''
								) );
								foreach ( $columns as $key => $column ) {
									echo '<th class="wc-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $email_templates as $email_key => $email ) {
							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {
									case 'name' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											<a href="' . admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) . '">' . $email->get_title() . '</a>
											' . wc_help_tip( $email->get_description() ) . '
										</td>';
									break;
									case 'recipient' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											' . esc_html( $email->is_customer_email() ? __( 'Customer', 'banda' ) : $email->get_recipient() ) . '
										</td>';
									break;
									case 'status' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">';

										if ( $email->is_manual() ) {
											echo '<span class="status-manual tips" data-tip="' . __( 'Manually sent', 'banda' ) . '">' . __( 'Manual', 'banda' ) . '</span>';
										} elseif ( $email->is_enabled() ) {
											echo '<span class="status-enabled tips" data-tip="' . __( 'Enabled', 'banda' ) . '">' . __( 'Yes', 'banda' ) . '</span>';
										} else {
											echo '<span class="status-disabled tips" data-tip="' . __( 'Disabled', 'banda' ) . '">-</span>';
										}

										echo '</td>';
									break;
									case 'email_type' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											' . esc_html( $email->get_content_type() ) . '
										</td>';
									break;
									case 'actions' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											<a class="button alignright tips" data-tip="' . __( 'Configure', 'banda' ) . '" href="' . admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) . '">' . __( 'Configure', 'banda' ) . '</a>
										</td>';
									break;
									default :
										do_action( 'banda_email_setting_column_' . $key, $email );
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
}

endif;

return new WC_Settings_Emails();
