<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'banda_tax_settings', array(

	array( 'title' => __( 'Tax Options', 'banda' ), 'type' => 'title','desc' => '', 'id' => 'tax_options' ),

	array(
		'title'    => __( 'Prices Entered With Tax', 'banda' ),
		'id'       => 'banda_prices_include_tax',
		'default'  => 'no',
		'type'     => 'radio',
		'desc_tip' =>  __( 'This option is important as it will affect how you input prices. Changing it will not update existing products.', 'banda' ),
		'options'  => array(
			'yes' => __( 'Yes, I will enter prices inclusive of tax', 'banda' ),
			'no'  => __( 'No, I will enter prices exclusive of tax', 'banda' )
		),
	),

	array(
		'title'    => __( 'Calculate Tax Based On', 'banda' ),
		'id'       => 'banda_tax_based_on',
		'desc_tip' =>  __( 'This option determines which address is used to calculate tax.', 'banda' ),
		'default'  => 'shipping',
		'type'     => 'select',
		'class'    => 'wc-enhanced-select',
		'options'  => array(
			'shipping' => __( 'Customer shipping address', 'banda' ),
			'billing'  => __( 'Customer billing address', 'banda' ),
			'base'     => __( 'Shop base address', 'banda' )
		),
	),

	array(
		'title'    => __( 'Shipping Tax Class', 'banda' ),
		'desc'     => __( 'Optionally control which tax class shipping gets, or leave it so shipping tax is based on the cart items themselves.', 'banda' ),
		'id'       => 'banda_shipping_tax_class',
		'css'      => 'min-width:150px;',
		'default'  => '',
		'type'     => 'select',
		'class'    => 'wc-enhanced-select',
		'options'  => array( '' => __( 'Shipping tax class based on cart items', 'banda' ), 'standard' => __( 'Standard', 'banda' ) ) + $classes_options,
		'desc_tip' =>  true,
	),

	array(
		'title'   => __( 'Rounding', 'banda' ),
		'desc'    => __( 'Round tax at subtotal level, instead of rounding per line', 'banda' ),
		'id'      => 'banda_tax_round_at_subtotal',
		'default' => 'no',
		'type'    => 'checkbox',
	),

	array(
		'title'   => __( 'Additional Tax Classes', 'banda' ),
		'desc_tip'    => __( 'List additional tax classes below (1 per line). This is in addition to the default "Standard Rate".', 'banda' ),
		'id'      => 'banda_tax_classes',
		'css'     => 'width:100%; height: 65px;',
		'type'    => 'textarea',
		'default' => sprintf( __( 'Reduced Rate%sZero Rate', 'banda' ), PHP_EOL )
	),

	array(
		'title'   => __( 'Display Prices in the Shop', 'banda' ),
		'id'      => 'banda_tax_display_shop',
		'default' => 'excl',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			'incl'   => __( 'Including tax', 'banda' ),
			'excl'   => __( 'Excluding tax', 'banda' ),
		)
	),

	array(
		'title'   => __( 'Display Prices During Cart and Checkout', 'banda' ),
		'id'      => 'banda_tax_display_cart',
		'default' => 'excl',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			'incl'   => __( 'Including tax', 'banda' ),
			'excl'   => __( 'Excluding tax', 'banda' ),
		),
		'autoload'      => false
	),

	array(
		'title'       => __( 'Price Display Suffix', 'banda' ),
		'id'          => 'banda_price_display_suffix',
		'default'     => '',
		'placeholder' => __( 'N/A', 'banda' ),
		'type'        => 'text',
		'desc_tip'    => __( 'Define text to show after your product prices. This could be, for example, "inc. Vat" to explain your pricing. You can also have prices substituted here using one of the following: {price_including_tax}, {price_excluding_tax}.', 'banda' ),
	),

	array(
		'title'   => __( 'Display Tax Totals', 'banda' ),
		'id'      => 'banda_tax_total_display',
		'default' => 'itemized',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			'single'     => __( 'As a single total', 'banda' ),
			'itemized'   => __( 'Itemized', 'banda' ),
		),
		'autoload' => false
	),

	array( 'type' => 'sectionend', 'id' => 'tax_options' ),

) );
