<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shopping Cart Widget.
 *
 * Displays shopping cart widget.
 *
 * @author   Mtaandao
 * @category Widgets
 * @package  Banda/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class WC_Widget_Cart extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'banda widget_shopping_cart';
		$this->widget_description = __( "Display the user's Cart in the sidebar.", 'banda' );
		$this->widget_id          = 'banda_widget_cart';
		$this->widget_name        = __( 'Banda Cart', 'banda' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Cart', 'banda' ),
				'label' => __( 'Title', 'banda' )
			),
			'hide_if_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide if cart is empty', 'banda' )
			)
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'banda_widget_cart_is_hidden', is_cart() || is_checkout() ) ) {
			return;
		}

		$hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;

		$this->widget_start( $args, $instance );

		if ( $hide_if_empty ) {
			echo '<div class="hide_cart_widget_if_empty">';
		}

		// Insert cart widget placeholder - code in banda.js will update this on page load
		echo '<div class="widget_shopping_cart_content"></div>';

		if ( $hide_if_empty ) {
			echo '</div>';
		}

		$this->widget_end( $args );
	}
}
