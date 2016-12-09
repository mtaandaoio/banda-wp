<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for PayPal Gateway.
 */
return array(
	'enabled' => array(
		'title'   => __( 'Enable/Disable', 'banda' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable PayPal standard', 'banda' ),
		'default' => 'yes'
	),
	'title' => array(
		'title'       => __( 'Title', 'banda' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'banda' ),
		'default'     => __( 'PayPal', 'banda' ),
		'desc_tip'    => true,
	),
	'description' => array(
		'title'       => __( 'Description', 'banda' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'banda' ),
		'default'     => __( 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.', 'banda' )
	),
	'email' => array(
		'title'       => __( 'PayPal Email', 'banda' ),
		'type'        => 'email',
		'description' => __( 'Please enter your PayPal email address; this is needed in order to take payment.', 'banda' ),
		'default'     => get_option( 'admin_email' ),
		'desc_tip'    => true,
		'placeholder' => 'you@youremail.com'
	),
	'testmode' => array(
		'title'       => __( 'PayPal Sandbox', 'banda' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable PayPal sandbox', 'banda' ),
		'default'     => 'no',
		'description' => sprintf( __( 'PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'banda' ), 'https://developer.paypal.com/' ),
	),
	'debug' => array(
		'title'       => __( 'Debug Log', 'banda' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'banda' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>%s</code>', 'banda' ), wc_get_log_file_path( 'paypal' ) )
	),
	'advanced' => array(
		'title'       => __( 'Advanced options', 'banda' ),
		'type'        => 'title',
		'description' => '',
	),
	'receiver_email' => array(
		'title'       => __( 'Receiver Email', 'banda' ),
		'type'        => 'email',
		'description' => __( 'If your main PayPal email differs from the PayPal email entered above, input your main receiver email for your PayPal account here. This is used to validate IPN requests.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => 'you@youremail.com'
	),
	'identity_token' => array(
		'title'       => __( 'PayPal Identity Token', 'banda' ),
		'type'        => 'text',
		'description' => __( 'Optionally enable "Payment Data Transfer" (Profile > Profile and Settings > My Selling Tools > Website Preferences) and then copy your identity token here. This will allow payments to be verified without the need for PayPal IPN.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => ''
	),
	'invoice_prefix' => array(
		'title'       => __( 'Invoice Prefix', 'banda' ),
		'type'        => 'text',
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'banda' ),
		'default'     => 'WC-',
		'desc_tip'    => true,
	),
	'send_shipping' => array(
		'title'       => __( 'Shipping Details', 'banda' ),
		'type'        => 'checkbox',
		'label'       => __( 'Send shipping details to PayPal instead of billing.', 'banda' ),
		'description' => __( 'PayPal allows us to send one address. If you are using PayPal for shipping labels you may prefer to send the shipping address rather than billing.', 'banda' ),
		'default'     => 'no'
	),
	'address_override' => array(
		'title'       => __( 'Address Override', 'banda' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable "address_override" to prevent address information from being changed.', 'banda' ),
		'description' => __( 'PayPal verifies addresses therefore this setting can cause errors (we recommend keeping it disabled).', 'banda' ),
		'default'     => 'no'
	),
	'paymentaction' => array(
		'title'       => __( 'Payment Action', 'banda' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select',
		'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only.', 'banda' ),
		'default'     => 'sale',
		'desc_tip'    => true,
		'options'     => array(
			'sale'          => __( 'Capture', 'banda' ),
			'authorization' => __( 'Authorize', 'banda' )
		)
	),
	'page_style' => array(
		'title'       => __( 'Page Style', 'banda' ),
		'type'        => 'text',
		'description' => __( 'Optionally enter the name of the page style you wish to use. These are defined within your PayPal account.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'banda' )
	),
	'api_details' => array(
		'title'       => __( 'API Credentials', 'banda' ),
		'type'        => 'title',
		'description' => sprintf( __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your PayPal API Credentials %shere%s.', 'banda' ), '<a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-an-api-signature">', '</a>' ),
	),
	'api_username' => array(
		'title'       => __( 'API Username', 'banda' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'banda' )
	),
	'api_password' => array(
		'title'       => __( 'API Password', 'banda' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'banda' )
	),
	'api_signature' => array(
		'title'       => __( 'API Signature', 'banda' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'banda' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'banda' )
	),
);
