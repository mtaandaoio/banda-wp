<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * External Product Class.
 *
 * External products cannot be bought; they link offsite. Extends simple products.
 *
 * @class 		WC_Product_External
 * @version		2.0.0
 * @package		Banda/Classes/Products
 * @category	Class
 * @author 		Mtaandao
 */
class WC_Product_External extends WC_Product {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param mixed $product
	 */
	public function __construct( $product ) {
		$this->product_type = 'external';
		parent::__construct( $product );
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @access public
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'banda_is_purchasable', false, $this );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		return apply_filters( 'banda_product_add_to_cart_url', $this->get_product_url(), $this );
	}

	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @access public
	 * @return string
	 */
	public function single_add_to_cart_text() {
		return apply_filters( 'banda_product_single_add_to_cart_text', $this->get_button_text(), $this );
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'banda_product_single_add_to_cart_text', $this->get_button_text(), $this );
	}

	/**
	 * Get product url.
	 *
	 * @access public
	 * @return string
	 */
	public function get_product_url() {
		return esc_url( $this->product_url );
	}

	/**
	 * Get button text.
	 *
	 * @access public
	 * @return string
	 */
	public function get_button_text() {
		return $this->button_text ? $this->button_text : __( 'Buy product', 'banda' );
	}
}
