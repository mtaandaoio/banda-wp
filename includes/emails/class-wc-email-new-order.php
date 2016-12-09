<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Email_New_Order' ) ) :

/**
 * New Order Email.
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class       WC_Email_New_Order
 * @version     2.0.0
 * @package     Banda/Classes/Emails
 * @author      Mtaandao
 * @extends     WC_Email
 */
class WC_Email_New_Order extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'new_order';
		$this->title            = __( 'New order', 'banda' );
		$this->description      = __( 'New order emails are sent to chosen recipient(s) when a new order is received.', 'banda' );
		$this->heading          = __( 'New customer order', 'banda' );
		$this->subject          = __( '[{site_title}] New customer order ({order_number}) - {order_date}', 'banda' );
		$this->template_html    = 'emails/admin-new-order.php';
		$this->template_plain   = 'emails/plain/admin-new-order.php';

		// Triggers for this email
		add_action( 'banda_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'banda_order_status_pending_to_completed_notification', array( $this, 'trigger' ) );
		add_action( 'banda_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ) );
		add_action( 'banda_order_status_failed_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'banda_order_status_failed_to_completed_notification', array( $this, 'trigger' ) );
		add_action( 'banda_order_status_failed_to_on-hold_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Trigger.
	 *
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
		if ( $order_id ) {
			$this->object                  = wc_get_order( $order_id );
			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';
			$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			$this->replace['order-number'] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false,
			'email'			=> $this
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true,
			'email'			=> $this
		) );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'         => __( 'Enable/Disable', 'banda' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'banda' ),
				'default'       => 'yes'
			),
			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'banda' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'banda' ), esc_attr( get_option('admin_email') ) ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'subject' => array(
				'title'         => __( 'Subject', 'banda' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'banda' ), $this->subject ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'heading' => array(
				'title'         => __( 'Email Heading', 'banda' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'banda' ), $this->heading ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'banda' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'banda' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true
			)
		);
	}
}

endif;

return new WC_Email_New_Order();
