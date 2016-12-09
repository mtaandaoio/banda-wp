<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tag Cloud Widget.
 *
 * @author   Mtaandao
 * @category Widgets
 * @package  Banda/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class WC_Widget_Product_Tag_Cloud extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'banda widget_product_tag_cloud';
		$this->widget_description = __( 'Your most used product tags in cloud format.', 'banda' );
		$this->widget_id          = 'banda_product_tag_cloud';
		$this->widget_name        = __( 'Banda Product Tags', 'banda' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Product Tags', 'banda' ),
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
		$current_taxonomy = $this->_get_current_taxonomy( $instance );

		if ( empty( $instance['title'] ) ) {
			$taxonomy = get_taxonomy( $current_taxonomy );
			$instance['title'] = $taxonomy->labels->name;
		}

		$this->widget_start( $args, $instance );

		echo '<div class="tagcloud">';

		wp_tag_cloud( apply_filters( 'banda_product_tag_cloud_widget_args', array(
			'taxonomy' => $current_taxonomy,
			'topic_count_text_callback' => array( $this, '_topic_count_text' ),
		) ) );

		echo '</div>';

		$this->widget_end( $args );
	}

	/**
	 * Return the taxonomy being displayed.
	 *
	 * @param  object $instance
	 * @return string
	 */
	public function _get_current_taxonomy( $instance ) {
		return 'product_tag';
	}

	/**
	 * Retuns topic count text.
	 *
	 * @since 2.6.0
	 * @param int $count
	 * @return string
	 */
	public function _topic_count_text( $count ) {
		/* translators: %s for product quantity, e.g. 1 product and 2 products */
		return sprintf( _n( '%s product', '%s products', $count, 'banda' ), number_format_i18n( $count ) );
	}
}
