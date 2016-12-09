<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Search Widget.
 *
 * @author   Mtaandao
 * @category Widgets
 * @package  Banda/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class WC_Widget_Product_Search extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'banda widget_product_search';
		$this->widget_description = __( 'A Search box for products only.', 'banda' );
		$this->widget_id          = 'banda_product_search';
		$this->widget_name        = __( 'Banda Product Search', 'banda' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'banda' )
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
		$this->widget_start( $args, $instance );

		get_product_search_form();

		$this->widget_end( $args );
	}
}
