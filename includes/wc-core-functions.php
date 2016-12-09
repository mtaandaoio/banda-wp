<?php
/**
 * Banda Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		Mtaandao
 * @category 	Core
 * @package 	Banda/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
include( 'wc-conditional-functions.php' );
include( 'wc-coupon-functions.php' );
include( 'wc-user-functions.php' );
include( 'wc-deprecated-functions.php' );
include( 'wc-formatting-functions.php' );
include( 'wc-order-functions.php' );
include( 'wc-page-functions.php' );
include( 'wc-product-functions.php' );
include( 'wc-account-functions.php' );
include( 'wc-term-functions.php' );
include( 'wc-attribute-functions.php' );
include( 'wc-rest-functions.php' );

/**
 * Filters on data used in admin and frontend.
 */
add_filter( 'banda_coupon_code', 'html_entity_decode' );
add_filter( 'banda_coupon_code', 'sanitize_text_field' );
add_filter( 'banda_coupon_code', 'strtolower' ); // Coupons case-insensitive by default
add_filter( 'banda_stock_amount', 'intval' ); // Stock amounts are integers by default
add_filter( 'banda_shipping_rate_label', 'sanitize_text_field' ); // Shipping rate label

/**
 * Short Description (excerpt).
 */
add_filter( 'banda_short_description', 'wptexturize' );
add_filter( 'banda_short_description', 'convert_smilies' );
add_filter( 'banda_short_description', 'convert_chars' );
add_filter( 'banda_short_description', 'wpautop' );
add_filter( 'banda_short_description', 'shortcode_unautop' );
add_filter( 'banda_short_description', 'prepend_attachment' );
add_filter( 'banda_short_description', 'do_shortcode', 11 ); // AFTER wpautop()

/**
 * Create a new order programmatically.
 *
 * Returns a new order object on success which can then be used to add additional data.
 *
 * @param  array $args
 *
 * @return WC_Order|WP_Error WC_Order on success, WP_Error on failure.
 */
function wc_create_order( $args = array() ) {
	$default_args = array(
		'status'        => '',
		'customer_id'   => null,
		'customer_note' => null,
		'order_id'      => 0,
		'created_via'   => '',
		'cart_hash'     => '',
		'parent'        => 0,
	);

	$args       = wp_parse_args( $args, $default_args );
	$order_data = array();

	if ( $args['order_id'] > 0 ) {
		$updating         = true;
		$order_data['ID'] = $args['order_id'];
	} else {
		$updating                    = false;
		$order_data['post_type']     = 'shop_order';
		$order_data['post_status']   = 'wc-' . apply_filters( 'banda_default_order_status', 'pending' );
		$order_data['ping_status']   = 'closed';
		$order_data['post_author']   = 1;
		$order_data['post_password'] = uniqid( 'order_' );
		$order_data['post_title']    = sprintf( __( 'Order &ndash; %s', 'banda' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'banda' ) ) );
		$order_data['post_parent']   = absint( $args['parent'] );
	}

	if ( $args['status'] ) {
		if ( ! in_array( 'wc-' . $args['status'], array_keys( wc_get_order_statuses() ) ) ) {
			return new WP_Error( 'banda_invalid_order_status', __( 'Invalid order status', 'banda' ) );
		}
		$order_data['post_status']  = 'wc-' . $args['status'];
	}

	if ( ! is_null( $args['customer_note'] ) ) {
		$order_data['post_excerpt'] = $args['customer_note'];
	}

	if ( $updating ) {
		$order_id = wp_update_post( $order_data );
	} else {
		$order_id = wp_insert_post( apply_filters( 'banda_new_order_data', $order_data ), true );
	}

	if ( is_wp_error( $order_id ) ) {
		return $order_id;
	}

	if ( ! $updating ) {
		update_post_meta( $order_id, '_order_key', 'wc_' . apply_filters( 'banda_generate_order_key', uniqid( 'order_' ) ) );
		update_post_meta( $order_id, '_order_currency', get_banda_currency() );
		update_post_meta( $order_id, '_prices_include_tax', get_option( 'banda_prices_include_tax' ) );
		update_post_meta( $order_id, '_customer_ip_address', WC_Geolocation::get_ip_address() );
		update_post_meta( $order_id, '_customer_user_agent', isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );
		update_post_meta( $order_id, '_customer_user', 0 );
		update_post_meta( $order_id, '_created_via', sanitize_text_field( $args['created_via'] ) );
		update_post_meta( $order_id, '_cart_hash', sanitize_text_field( $args['cart_hash'] ) );
	}

	if ( is_numeric( $args['customer_id'] ) ) {
		update_post_meta( $order_id, '_customer_user', $args['customer_id'] );
	}

	update_post_meta( $order_id, '_order_version', WC_VERSION );

	return wc_get_order( $order_id );
}

/**
 * Update an order. Uses wc_create_order.
 *
 * @param  array $args
 * @return string | WC_Order
 */
function wc_update_order( $args ) {
	if ( ! $args['order_id'] ) {
		return new WP_Error( __( 'Invalid order ID', 'banda' ) );
	}
	return wc_create_order( $args );
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * WC_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function wc_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/banda/slug-name.php
	if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", WC()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( WC()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = WC()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/banda/slug.php
	if ( ! $template && ! WC_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", WC()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wc_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function wc_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = wc_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'wc_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'banda_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'banda_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like wc_get_template, but returns the HTML instead of outputting.
 * @see wc_get_template
 * @since 2.5.0
 * @param string $template_name
 */
function wc_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	wc_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function wc_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = WC()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = WC()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template/
	if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'banda_locate_template', $template, $template_name, $template_path );
}

/**
 * Get Base Currency Code.
 *
 * @return string
 */
function get_banda_currency() {
	return apply_filters( 'banda_currency', get_option('banda_currency') );
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function get_banda_currencies() {
	return array_unique(
		apply_filters( 'banda_currencies',
			array(
				'AED' => __( 'United Arab Emirates dirham', 'banda' ),
				'AFN' => __( 'Afghan afghani', 'banda' ),
				'ALL' => __( 'Albanian lek', 'banda' ),
				'AMD' => __( 'Armenian dram', 'banda' ),
				'ANG' => __( 'Netherlands Antillean guilder', 'banda' ),
				'AOA' => __( 'Angolan kwanza', 'banda' ),
				'ARS' => __( 'Argentine peso', 'banda' ),
				'AUD' => __( 'Australian dollar', 'banda' ),
				'AWG' => __( 'Aruban florin', 'banda' ),
				'AZN' => __( 'Azerbaijani manat', 'banda' ),
				'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'banda' ),
				'BBD' => __( 'Barbadian dollar', 'banda' ),
				'BDT' => __( 'Bangladeshi taka', 'banda' ),
				'BGN' => __( 'Bulgarian lev', 'banda' ),
				'BHD' => __( 'Bahraini dinar', 'banda' ),
				'BIF' => __( 'Burundian franc', 'banda' ),
				'BMD' => __( 'Bermudian dollar', 'banda' ),
				'BND' => __( 'Brunei dollar', 'banda' ),
				'BOB' => __( 'Bolivian boliviano', 'banda' ),
				'BRL' => __( 'Brazilian real', 'banda' ),
				'BSD' => __( 'Bahamian dollar', 'banda' ),
				'BTC' => __( 'Bitcoin', 'banda' ),
				'BTN' => __( 'Bhutanese ngultrum', 'banda' ),
				'BWP' => __( 'Botswana pula', 'banda' ),
				'BYR' => __( 'Belarusian ruble', 'banda' ),
				'BZD' => __( 'Belize dollar', 'banda' ),
				'CAD' => __( 'Canadian dollar', 'banda' ),
				'CDF' => __( 'Congolese franc', 'banda' ),
				'CHF' => __( 'Swiss franc', 'banda' ),
				'CLP' => __( 'Chilean peso', 'banda' ),
				'CNY' => __( 'Chinese yuan', 'banda' ),
				'COP' => __( 'Colombian peso', 'banda' ),
				'CRC' => __( 'Costa Rican col&oacute;n', 'banda' ),
				'CUC' => __( 'Cuban convertible peso', 'banda' ),
				'CUP' => __( 'Cuban peso', 'banda' ),
				'CVE' => __( 'Cape Verdean escudo', 'banda' ),
				'CZK' => __( 'Czech koruna', 'banda' ),
				'DJF' => __( 'Djiboutian franc', 'banda' ),
				'DKK' => __( 'Danish krone', 'banda' ),
				'DOP' => __( 'Dominican peso', 'banda' ),
				'DZD' => __( 'Algerian dinar', 'banda' ),
				'EGP' => __( 'Egyptian pound', 'banda' ),
				'ERN' => __( 'Eritrean nakfa', 'banda' ),
				'ETB' => __( 'Ethiopian birr', 'banda' ),
				'EUR' => __( 'Euro', 'banda' ),
				'FJD' => __( 'Fijian dollar', 'banda' ),
				'FKP' => __( 'Falkland Islands pound', 'banda' ),
				'GBP' => __( 'Pound sterling', 'banda' ),
				'GEL' => __( 'Georgian lari', 'banda' ),
				'GGP' => __( 'Guernsey pound', 'banda' ),
				'GHS' => __( 'Ghana cedi', 'banda' ),
				'GIP' => __( 'Gibraltar pound', 'banda' ),
				'GMD' => __( 'Gambian dalasi', 'banda' ),
				'GNF' => __( 'Guinean franc', 'banda' ),
				'GTQ' => __( 'Guatemalan quetzal', 'banda' ),
				'GYD' => __( 'Guyanese dollar', 'banda' ),
				'HKD' => __( 'Hong Kong dollar', 'banda' ),
				'HNL' => __( 'Honduran lempira', 'banda' ),
				'HRK' => __( 'Croatian kuna', 'banda' ),
				'HTG' => __( 'Haitian gourde', 'banda' ),
				'HUF' => __( 'Hungarian forint', 'banda' ),
				'IDR' => __( 'Indonesian rupiah', 'banda' ),
				'ILS' => __( 'Israeli new shekel', 'banda' ),
				'IMP' => __( 'Manx pound', 'banda' ),
				'INR' => __( 'Indian rupee', 'banda' ),
				'IQD' => __( 'Iraqi dinar', 'banda' ),
				'IRR' => __( 'Iranian rial', 'banda' ),
				'ISK' => __( 'Icelandic kr&oacute;na', 'banda' ),
				'JEP' => __( 'Jersey pound', 'banda' ),
				'JMD' => __( 'Jamaican dollar', 'banda' ),
				'JOD' => __( 'Jordanian dinar', 'banda' ),
				'JPY' => __( 'Japanese yen', 'banda' ),
				'KES' => __( 'Kenyan shilling', 'banda' ),
				'KGS' => __( 'Kyrgyzstani som', 'banda' ),
				'KHR' => __( 'Cambodian riel', 'banda' ),
				'KMF' => __( 'Comorian franc', 'banda' ),
				'KPW' => __( 'North Korean won', 'banda' ),
				'KRW' => __( 'South Korean won', 'banda' ),
				'KWD' => __( 'Kuwaiti dinar', 'banda' ),
				'KYD' => __( 'Cayman Islands dollar', 'banda' ),
				'KZT' => __( 'Kazakhstani tenge', 'banda' ),
				'LAK' => __( 'Lao kip', 'banda' ),
				'LBP' => __( 'Lebanese pound', 'banda' ),
				'LKR' => __( 'Sri Lankan rupee', 'banda' ),
				'LRD' => __( 'Liberian dollar', 'banda' ),
				'LSL' => __( 'Lesotho loti', 'banda' ),
				'LYD' => __( 'Libyan dinar', 'banda' ),
				'MAD' => __( 'Moroccan dirham', 'banda' ),
				'MDL' => __( 'Moldovan leu', 'banda' ),
				'MGA' => __( 'Malagasy ariary', 'banda' ),
				'MKD' => __( 'Macedonian denar', 'banda' ),
				'MMK' => __( 'Burmese kyat', 'banda' ),
				'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'banda' ),
				'MOP' => __( 'Macanese pataca', 'banda' ),
				'MRO' => __( 'Mauritanian ouguiya', 'banda' ),
				'MUR' => __( 'Mauritian rupee', 'banda' ),
				'MVR' => __( 'Maldivian rufiyaa', 'banda' ),
				'MWK' => __( 'Malawian kwacha', 'banda' ),
				'MXN' => __( 'Mexican peso', 'banda' ),
				'MYR' => __( 'Malaysian ringgit', 'banda' ),
				'MZN' => __( 'Mozambican metical', 'banda' ),
				'NAD' => __( 'Namibian dollar', 'banda' ),
				'NGN' => __( 'Nigerian naira', 'banda' ),
				'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'banda' ),
				'NOK' => __( 'Norwegian krone', 'banda' ),
				'NPR' => __( 'Nepalese rupee', 'banda' ),
				'NZD' => __( 'New Zealand dollar', 'banda' ),
				'OMR' => __( 'Omani rial', 'banda' ),
				'PAB' => __( 'Panamanian balboa', 'banda' ),
				'PEN' => __( 'Peruvian nuevo sol', 'banda' ),
				'PGK' => __( 'Papua New Guinean kina', 'banda' ),
				'PHP' => __( 'Philippine peso', 'banda' ),
				'PKR' => __( 'Pakistani rupee', 'banda' ),
				'PLN' => __( 'Polish z&#x142;oty', 'banda' ),
				'PRB' => __( 'Transnistrian ruble', 'banda' ),
				'PYG' => __( 'Paraguayan guaran&iacute;', 'banda' ),
				'QAR' => __( 'Qatari riyal', 'banda' ),
				'RON' => __( 'Romanian leu', 'banda' ),
				'RSD' => __( 'Serbian dinar', 'banda' ),
				'RUB' => __( 'Russian ruble', 'banda' ),
				'RWF' => __( 'Rwandan franc', 'banda' ),
				'SAR' => __( 'Saudi riyal', 'banda' ),
				'SBD' => __( 'Solomon Islands dollar', 'banda' ),
				'SCR' => __( 'Seychellois rupee', 'banda' ),
				'SDG' => __( 'Sudanese pound', 'banda' ),
				'SEK' => __( 'Swedish krona', 'banda' ),
				'SGD' => __( 'Singapore dollar', 'banda' ),
				'SHP' => __( 'Saint Helena pound', 'banda' ),
				'SLL' => __( 'Sierra Leonean leone', 'banda' ),
				'SOS' => __( 'Somali shilling', 'banda' ),
				'SRD' => __( 'Surinamese dollar', 'banda' ),
				'SSP' => __( 'South Sudanese pound', 'banda' ),
				'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'banda' ),
				'SYP' => __( 'Syrian pound', 'banda' ),
				'SZL' => __( 'Swazi lilangeni', 'banda' ),
				'THB' => __( 'Thai baht', 'banda' ),
				'TJS' => __( 'Tajikistani somoni', 'banda' ),
				'TMT' => __( 'Turkmenistan manat', 'banda' ),
				'TND' => __( 'Tunisian dinar', 'banda' ),
				'TOP' => __( 'Tongan pa&#x2bb;anga', 'banda' ),
				'TRY' => __( 'Turkish lira', 'banda' ),
				'TTD' => __( 'Trinidad and Tobago dollar', 'banda' ),
				'TWD' => __( 'New Taiwan dollar', 'banda' ),
				'TZS' => __( 'Tanzanian shilling', 'banda' ),
				'UAH' => __( 'Ukrainian hryvnia', 'banda' ),
				'UGX' => __( 'Ugandan shilling', 'banda' ),
				'USD' => __( 'United States dollar', 'banda' ),
				'UYU' => __( 'Uruguayan peso', 'banda' ),
				'UZS' => __( 'Uzbekistani som', 'banda' ),
				'VEF' => __( 'Venezuelan bol&iacute;var', 'banda' ),
				'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'banda' ),
				'VUV' => __( 'Vanuatu vatu', 'banda' ),
				'WST' => __( 'Samoan t&#x101;l&#x101;', 'banda' ),
				'XAF' => __( 'Central African CFA franc', 'banda' ),
				'XCD' => __( 'East Caribbean dollar', 'banda' ),
				'XOF' => __( 'West African CFA franc', 'banda' ),
				'XPF' => __( 'CFP franc', 'banda' ),
				'YER' => __( 'Yemeni rial', 'banda' ),
				'ZAR' => __( 'South African rand', 'banda' ),
				'ZMW' => __( 'Zambian kwacha', 'banda' ),
			)
		)
	);
}

/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function get_banda_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = get_banda_currency();
	}

	$symbols = apply_filters( 'banda_currency_symbols', array(
		'AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&fnof;',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '&#8377;',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'ISK' => 'kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x441;&#x43e;&#x43c;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'MAD' => '&#x62f;. &#x645;.',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'L',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'Fr',
		'XCD' => '&#36;',
		'XOF' => 'Fr',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
	) );

	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return apply_filters( 'banda_currency_symbol', $currency_symbol, $currency );
}

/**
 * Send HTML emails from Banda.
 *
 * @param mixed $to
 * @param mixed $subject
 * @param mixed $message
 * @param string $headers (default: "Content-Type: text/html\r\n")
 * @param string $attachments (default: "")
 */
function wc_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	$mailer = WC()->mailer();

	$mailer->send( $to, $subject, $message, $headers, $attachments );
}

/**
 * Get an image size.
 *
 * Variable is filtered by banda_get_image_size_{image_size}.
 *
 * @param mixed $image_size
 * @return array
 */
function wc_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'shop_thumbnail', 'shop_catalog', 'shop_single' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'banda_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wc_enqueue_js( $code ) {
	global $wc_queued_js;

	if ( empty( $wc_queued_js ) ) {
		$wc_queued_js = '';
	}

	$wc_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wc_print_js() {
	global $wc_queued_js;

	if ( ! empty( $wc_queued_js ) ) {
		// Sanitize.
		$wc_queued_js = wp_check_invalid_utf8( $wc_queued_js );
		$wc_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wc_queued_js );
		$wc_queued_js = str_replace( "\r", '', $wc_queued_js );

		$js = "<!-- Banda JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $wc_queued_js });\n</script>\n";

		/**
		 * banda_queued_js filter.
		 *
		 * @since 2.6.0
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'banda_queued_js', $js );

		unset( $wc_queued_js );
	}
}

/**
 * Set a cookie - wrapper for setcookie using WP constants.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  string  $secure Whether the cookie should be served only over https.
 */
function wc_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}

/**
 * Get the URL to the Banda REST API.
 *
 * @since 2.1
 * @param string $path an endpoint to include in the URL.
 * @return string the URL.
 */
function get_banda_api_url( $path ) {
	$version  = defined( 'WC_API_REQUEST_VERSION' ) ? WC_API_REQUEST_VERSION : substr( WC_API::VERSION, 0, 1 );

	$url = get_home_url( null, "wc-api/v{$version}/", is_ssl() ? 'https' : 'http' );

	if ( ! empty( $path ) && is_string( $path ) ) {
		$url .= ltrim( $path, '/' );
	}

	return $url;
}

/**
 * Get a log file path.
 *
 * @since 2.2
 * @param string $handle name.
 * @return string the log file path.
 */
function wc_get_log_file_path( $handle ) {
	return trailingslashit( WC_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}

/**
 * Init for our rewrite rule fixes.
 */
function wc_fix_rewrite_rules_init() {
	$permalinks = get_option( 'banda_permalinks' );

	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
	}
}
add_action( 'init', 'wc_fix_rewrite_rules_init' );

/**
 * Various rewrite rule fixes.
 *
 * @since 2.2
 * @param array $rules
 * @return array
 */
function wc_fix_rewrite_rules( $rules ) {
	global $wp_rewrite;

	$permalinks        = get_option( 'banda_permalinks' );
	$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'banda' ) : $permalinks['product_base'];

	// Fix the rewrite rules when the product permalink have %product_cat% flag.
	if ( preg_match( '`/(.+)(/%product_cat%)`' , $product_permalink, $matches ) ) {
		foreach ( $rules as $rule => $rewrite ) {

			if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '/\(`', $rule ) && preg_match( '/^(index\.php\?product_cat)(?!(.*product))/', $rewrite ) ) {
				unset( $rules[ $rule ] );
			}
		}
	}

	// If the shop page is used as the base, we need to enable verbose rewrite rules or sub pages will 404.
	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$page_rewrite_rules = $wp_rewrite->page_rewrite_rules();
		$rules              = array_merge( $page_rewrite_rules, $rules );
	}

	return $rules;
}
add_filter( 'rewrite_rules_array', 'wc_fix_rewrite_rules' );

/**
 * Prevent product attachment links from breaking when using complex rewrite structures.
 *
 * @param  string $link
 * @param  id $post_id
 * @return string
 */
function wc_fix_product_attachment_link( $link, $post_id ) {
	global $wp_rewrite;

	$post = get_post( $post_id );
	if ( 'product' === get_post_type( $post->post_parent ) ) {
		$permalinks        = get_option( 'banda_permalinks' );
		$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'banda' ) : $permalinks['product_base'];
		if ( preg_match( '/\/(.+)(\/%product_cat%)$/' , $product_permalink, $matches ) ) {
			$link = home_url( '/?attachment_id=' . $post->ID );
		}
	}
	return $link;
}
add_filter( 'attachment_link', 'wc_fix_product_attachment_link', 10, 2 );

/**
 * Protect downloads from ms-files.php in multisite.
 *
 * @param mixed $rewrite
 * @return string
 */
function wc_ms_protect_download_rewite_rules( $rewrite ) {
	if ( ! is_multisite() || 'redirect' == get_option( 'banda_file_download_method' ) ) {
		return $rewrite;
	}

	$rule  = "\n# Banda Rules - Protect Files from ms-files.php\n\n";
	$rule .= "<IfModule mod_rewrite.c>\n";
	$rule .= "RewriteEngine On\n";
	$rule .= "RewriteCond %{QUERY_STRING} file=banda_uploads/ [NC]\n";
	$rule .= "RewriteRule /ms-files.php$ - [F]\n";
	$rule .= "</IfModule>\n\n";

	return $rule . $rewrite;
}
add_filter( 'mod_rewrite_rules', 'wc_ms_protect_download_rewite_rules' );

/**
 * Banda Core Supported Themes.
 *
 * @since 2.2
 * @return string[]
 */
function wc_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

/**
 * Wrapper function to execute the `banda_deliver_webhook_async` cron.
 * hook, see WC_Webhook::process().
 *
 * @since 2.2
 * @param int $webhook_id webhook ID to deliver.
 * @param mixed $arg hook argument.
 */
function wc_deliver_webhook_async( $webhook_id, $arg ) {

	$webhook = new WC_Webhook( $webhook_id );

	$webhook->deliver( $arg );
}
add_action( 'banda_deliver_webhook_async', 'wc_deliver_webhook_async', 10, 2 );

/**
 * Enables template debug mode.
 */
function wc_template_debug_mode() {
	if ( ! defined( 'WC_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'banda_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'WC_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'WC_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'wc_template_debug_mode', 20 );

/**
 * Formats a string in the format COUNTRY:STATE into an array.
 *
 * @since 2.3.0
 * @param  string $country_string
 * @return array
 */
function wc_format_country_state_string( $country_string ) {
	if ( strstr( $country_string, ':' ) ) {
		list( $country, $state ) = explode( ':', $country_string );
	} else {
		$country = $country_string;
		$state   = '';
	}
	return array(
		'country' => $country,
		'state'   => $state
	);
}

/**
 * Get the store's base location.
 *
 * @todo should the banda_default_country option be renamed to contain 'base'?
 * @since 2.3.0
 * @return array
 */
function wc_get_base_location() {
	$default = apply_filters( 'banda_get_base_location', get_option( 'banda_default_country' ) );

	return wc_format_country_state_string( $default );
}

/**
 * Get the customer's default location.
 *
 * Filtered, and set to base location or left blank. If cache-busting,
 * this should only be used when 'location' is set in the querystring.
 *
 * @todo should the banda_default_country option be renamed to contain 'base'?
 * @todo deprecate banda_customer_default_location and support an array filter only to cover all cases.
 * @since 2.3.0
 * @return array
 */
function wc_get_customer_default_location() {
	$location = array();

	switch ( get_option( 'banda_default_customer_address' ) ) {
		case 'geolocation_ajax' :
		case 'geolocation' :
			// Exclude common bots from geolocation by user agent.
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

			if ( ! strstr( $ua, 'bot' ) && ! strstr( $ua, 'spider' ) && ! strstr( $ua, 'crawl' ) ) {
				$location = WC_Geolocation::geolocate_ip( '', true, false );
			}

			// Base fallback.
			if ( empty( $location['country'] ) ) {
				$location = wc_format_country_state_string( apply_filters( 'banda_customer_default_location', get_option( 'banda_default_country' ) ) );
			}
		break;
		case 'base' :
			$location = wc_format_country_state_string( apply_filters( 'banda_customer_default_location', get_option( 'banda_default_country' ) ) );
		break;
		default :
			$location = wc_format_country_state_string( apply_filters( 'banda_customer_default_location', '' ) );
		break;
	}

	return apply_filters( 'banda_customer_default_location_array', $location );
}

// This function can be removed when WP 3.9.2 or greater is required.
if ( ! function_exists( 'hash_equals' ) ) :
	/**
	 * Compare two strings in constant time.
	 *
	 * This function was added in PHP 5.6.
	 * It can leak the length of a string.
	 *
	 * @since 3.9.2
	 *
	 * @param string $a Expected string.
	 * @param string $b Actual string.
	 * @return bool Whether strings are equal.
	 */
	function hash_equals( $a, $b ) {
		$a_length = strlen( $a );
		if ( $a_length !== strlen( $b ) ) {
			return false;
		}
		$result = 0;

		// Do not attempt to "optimize" this.
		for ( $i = 0; $i < $a_length; $i++ ) {
			$result |= ord( $a[ $i ] ) ^ ord( $b[ $i ] );
		}

		return $result === 0;
	}
endif;

/**
 * Generate a rand hash.
 *
 * @since  2.4.0
 * @return string
 */
function wc_rand_hash() {
	if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return bin2hex( openssl_random_pseudo_bytes( 20 ) );
	} else {
		return sha1( wp_rand() );
	}
}

/**
 * WC API - Hash.
 *
 * @since  2.4.0
 * @param  string $data
 * @return string
 */
function wc_api_hash( $data ) {
	return hash_hmac( 'sha256', $data, 'wc-api' );
}

/**
 * Find all possible combinations of values from the input array and return in a logical order.
 * @since 2.5.0
 * @param array $input
 * @return array
 */
function wc_array_cartesian( $input ) {
	$input   = array_filter( $input );
	$results = array();
	$indexes = array();
	$index   = 0;

	// Generate indexes from keys and values so we have a logical sort order
	foreach ( $input as $key => $values ) {
		foreach ( $values as $value ) {
			$indexes[ $key ][ $value ] = $index++;
		}
	}

	// Loop over the 2D array of indexes and generate all combinations
	foreach ( $indexes as $key => $values ) {
		// When result is empty, fill with the values of the first looped array
		if ( empty( $results ) ) {
			foreach ( $values as $value ) {
				$results[] = array( $key => $value );
			}

		// Second and subsequent input sub-array merging.
		} else {
			foreach ( $results as $result_key => $result ) {
				foreach ( $values as $value ) {
					// If the key is not set, we can set it
					if ( ! isset( $results[ $result_key ][ $key ] ) ) {
						$results[ $result_key ][ $key ] = $value;
					// If the key is set, we can add a new combination to the results array
					} else {
						$new_combination         = $results[ $result_key ];
						$new_combination[ $key ] = $value;
						$results[]               = $new_combination;
					}
				}
			}
		}
	}

	// Sort the indexes
	arsort( $results );

	// Convert indexes back to values
	foreach ( $results as $result_key => $result ) {
		$converted_values = array();

		// Sort the values
		arsort( $results[ $result_key ] );

		// Convert the values
		foreach ( $results[ $result_key ] as $key => $value ) {
			$converted_values[ $key ] = array_search( $value, $indexes[ $key ] );
		}

		$results[ $result_key ] = $converted_values;
	}

	return $results;
}

/**
 * Run a MySQL transaction query, if supported.
 * @param string $type start (default), commit, rollback
 * @since 2.5.0
 */
function wc_transaction_query( $type = 'start' ) {
	global $wpdb;

	$wpdb->hide_errors();

	if ( ! defined( 'WC_USE_TRANSACTIONS' ) ) {
		define( 'WC_USE_TRANSACTIONS', true );
	}

	if ( WC_USE_TRANSACTIONS ) {
		switch ( $type ) {
			case 'commit' :
				$wpdb->query( 'COMMIT' );
				break;
			case 'rollback' :
				$wpdb->query( 'ROLLBACK' );
				break;
			default :
				$wpdb->query( 'START TRANSACTION' );
			break;
		}
	}
}

/**
 * Gets the url to the cart page.
 *
 * @since  2.5.0
 *
 * @return string Url to cart page
 */
function wc_get_cart_url() {
	return apply_filters( 'banda_get_cart_url', wc_get_page_permalink( 'cart' ) );
}

/**
 * Gets the url to the checkout page.
 *
 * @since  2.5.0
 *
 * @return string Url to checkout page
 */
function wc_get_checkout_url() {
	$checkout_url = wc_get_page_permalink( 'checkout' );
	if ( $checkout_url ) {
		// Force SSL if needed
		if ( is_ssl() || 'yes' === get_option( 'banda_force_ssl_checkout' ) ) {
			$checkout_url = str_replace( 'http:', 'https:', $checkout_url );
		}
	}

	return apply_filters( 'banda_get_checkout_url', $checkout_url );
}

/**
 * Register a shipping method.
 *
 * @since 1.5.7
 * @param string|object $shipping_method class name (string) or a class object.
 */
function banda_register_shipping_method( $shipping_method ) {
	WC()->shipping->register_shipping_method( $shipping_method );
}

if ( ! function_exists( 'wc_get_shipping_zone' ) ) {
	/**
	 * Get the shipping zone matching a given package from the cart.
	 *
	 * @since  2.6.0
	 * @uses   WC_Shipping_Zones::get_zone_matching_package
	 * @param  array $package
	 * @return WC_Shipping_Zone
	 */
	function wc_get_shipping_zone( $package ) {
		return WC_Shipping_Zones::get_zone_matching_package( $package );
	}
}

/**
 * Get a nice name for credit card providers.
 *
 * @since  2.6.0
 * @param  string $type Provider Slug/Type
 * @return string
 */
function wc_get_credit_card_type_label( $type ) {
	// Normalize
	$type = strtolower( $type );
	$type = str_replace( '-', ' ', $type );
	$type = str_replace( '_', ' ', $type );

	$labels = apply_filters( 'wocommerce_credit_card_type_labels', array(
		'mastercard'       => __( 'MasterCard', 'banda' ),
		'visa'             => __( 'Visa', 'banda' ),
		'discover'         => __( 'Discover', 'banda' ),
		'american express' => __( 'American Express', 'banda' ),
		'diners'           => __( 'Diners', 'banda' ),
		'jcb'              => __( 'JCB', 'banda' ),
	) );

	return apply_filters( 'banda_get_credit_card_type_label', ( array_key_exists( $type, $labels ) ? $labels[ $type ] : ucfirst( $type ) ) );
}

/**
 * Outputs a "back" link so admin screens can easily jump back a page.
 *
 * @param string $label Title of the page to return to.
 * @param string $url   URL of the page to return to.
 */
function wc_back_link( $label, $url ) {
	echo '<small class="wc-admin-breadcrumb"><a href="' . esc_url( $url ) . '" title="' . esc_attr( $label ) . '">&#x2934;</a></small>';
}

/**
 * Display a Banda help tip.
 *
 * @since  2.5.0
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function wc_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = wc_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="banda-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Return a list of potential postcodes for wildcard searching.
 * @since 2.6.0
 * @param  string $postcode
 * @param  string $country to format postcode for matching.
 * @return string[]
 */
function wc_get_wildcard_postcodes( $postcode, $country = '' ) {
	$postcodes       = array( $postcode );
	$postcode        = wc_format_postcode( $postcode, $country );
	$postcodes[]     = $postcode;
	$postcode_length = strlen( $postcode );

	for ( $i = 0; $i < $postcode_length; $i ++ ) {
		$postcodes[] = substr( $postcode, 0, ( $i + 1 ) * -1 ) . '*';
	}

	return $postcodes;
}

/**
 * Used by shipping zones and taxes to compare a given $postcode to stored
 * postcodes to find matches for numerical ranges, and wildcards.
 * @since 2.6.0
 * @param string $postcode Postcode you want to match against stored postcodes
 * @param array  $objects Array of postcode objects from Database
 * @param string $object_id_key DB column name for the ID.
 * @param string $object_compare_key DB column name for the value.
 * @param string $country Country from which this postcode belongs. Allows for formatting.
 * @return array Array of matching object ID and matching values.
 */
function wc_postcode_location_matcher( $postcode, $objects, $object_id_key, $object_compare_key, $country = '' ) {
	$postcode           = wc_normalize_postcode( $postcode );
	$wildcard_postcodes = array_map( 'wc_clean', wc_get_wildcard_postcodes( $postcode, $country ) );
	$matches            = array();

	foreach ( $objects as $object ) {
		$object_id       = $object->$object_id_key;
		$compare_against = $object->$object_compare_key;

		// Handle postcodes containing ranges.
		if ( strstr( $compare_against, '...' ) ) {
			$range = array_map( 'trim', explode( '...', $compare_against ) );

			if ( 2 !== sizeof( $range ) ) {
				continue;
			}

			list( $min, $max ) = $range;

			// If the postcode is non-numeric, make it numeric.
			if ( ! is_numeric( $min ) || ! is_numeric( $max ) ) {
				$compare = wc_make_numeric_postcode( $postcode );
				$min     = str_pad( wc_make_numeric_postcode( $min ), strlen( $compare ), '0' );
				$max     = str_pad( wc_make_numeric_postcode( $max ), strlen( $compare ), '0' );
			} else {
				$compare = $postcode;
			}

			if ( $compare >= $min && $compare <= $max ) {
				$matches[ $object_id ]   = isset( $matches[ $object_id ] ) ? $matches[ $object_id ]: array();
				$matches[ $object_id ][] = $compare_against;
			}

		// Wildcard and standard comparison.
		} elseif ( in_array( $compare_against, $wildcard_postcodes ) ) {
			$matches[ $object_id ]   = isset( $matches[ $object_id ] ) ? $matches[ $object_id ]: array();
			$matches[ $object_id ][] = $compare_against;
		}
	}

	return $matches;
}

/**
 * Gets number of shipping methods currently enabled. Used to identify if
 * shipping is configured.
 *
 * @since  2.6.0
 * @param  bool $include_legacy Count legacy shipping methods too.
 * @return int
 */
function wc_get_shipping_method_count( $include_legacy = false ) {
	global $wpdb;

	$transient_name = 'wc_shipping_method_count_' . ( $include_legacy ? 1 : 0 ) . '_' . WC_Cache_Helper::get_transient_version( 'shipping' );
	$method_count   = get_transient( $transient_name );

	if ( false === $method_count ) {
		$method_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}banda_shipping_zone_methods" ) );

		if ( $include_legacy ) {
			// Count activated methods that don't support shipping zones.
			$methods = WC()->shipping->get_shipping_methods();

			foreach ( $methods as $method ) {
				if ( isset( $method->enabled ) && 'yes' === $method->enabled && ! $method->supports( 'shipping-zones' ) ) {
					$method_count++;
				}
			}
		}

		set_transient( $transient_name, $method_count, DAY_IN_SECONDS * 30 );
	}

	return absint( $method_count );
}

/**
 * Wrapper for set_time_limit to see if it is enabled.
 * @since 2.6.0
 */
function wc_set_time_limit( $limit = 0 ) {
	if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
		@set_time_limit( $limit );
	}
}

/**
 * Used to sort products attributes with uasort.
 * @since 2.6.0
 */
function wc_product_attribute_uasort_comparison( $a, $b ) {
	if ( $a['position'] == $b['position'] ) {
		return 0;
	}
	return ( $a['position'] < $b['position'] ) ? -1 : 1;
}

/**
 * Get rounding precision for internal WC calculations.
 * Will increase the precision of wc_get_price_decimals by 2 decimals, unless WC_ROUNDING_PRECISION is set to a higher number.
 *
 * @since 2.6.3
 * @return int
 */
function wc_get_rounding_precision() {
	$precision = wc_get_price_decimals() + 2;
	if ( absint( WC_ROUNDING_PRECISION ) > $precision ) {
		$precision = absint( WC_ROUNDING_PRECISION );
	}
	return $precision;
}
