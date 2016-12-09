<?php
/**
 * Coupon Data
 *
 * Display the coupon data meta box.
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Meta_Box_Coupon_Data Class.
 */
class WC_Meta_Box_Coupon_Data {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		wp_nonce_field( 'banda_save_data', 'banda_meta_nonce' );
		?>
		<style type="text/css">
			#edit-slug-box, #minor-publishing-actions { display:none }
		</style>
		<div id="coupon_options" class="panel-wrap coupon_data">

			<div class="wc-tabs-back"></div>

			<ul class="coupon_data_tabs wc-tabs" style="display:none;">
				<?php
					$coupon_data_tabs = apply_filters( 'banda_coupon_data_tabs', array(
						'general' => array(
							'label'  => __( 'General', 'banda' ),
							'target' => 'general_coupon_data',
							'class'  => 'general_coupon_data',
						),
						'usage_restriction' => array(
							'label'  => __( 'Usage Restriction', 'banda' ),
							'target' => 'usage_restriction_coupon_data',
							'class'  => '',
						),
						'usage_limit' => array(
							'label'  => __( 'Usage Limits', 'banda' ),
							'target' => 'usage_limit_coupon_data',
							'class'  => '',
						)
					) );

					foreach ( $coupon_data_tabs as $key => $tab ) {
						?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
							<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
						</li><?php
					}
				?>
			</ul>
			<div id="general_coupon_data" class="panel banda_options_panel"><?php

				// Type
				banda_wp_select( array( 'id' => 'discount_type', 'label' => __( 'Discount type', 'banda' ), 'options' => wc_get_coupon_types() ) );

				// Amount
				banda_wp_text_input( array( 'id' => 'coupon_amount', 'label' => __( 'Coupon amount', 'banda' ), 'placeholder' => wc_format_localized_price( 0 ), 'description' => __( 'Value of the coupon.', 'banda' ), 'data_type' => 'price', 'desc_tip' => true ) );

				// Free Shipping
				banda_wp_checkbox( array( 'id' => 'free_shipping', 'label' => __( 'Allow free shipping', 'banda' ), 'description' => sprintf( __( 'Check this box if the coupon grants free shipping. A <a href="%s">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'banda' ), 'https://docs.mtaandao.co.ke/document/free-shipping/' ) ) );

				// Expiry date
				banda_wp_text_input( array( 'id' => 'expiry_date', 'label' => __( 'Coupon expiry date', 'banda' ), 'placeholder' => _x( 'YYYY-MM-DD', 'placeholder', 'banda' ), 'description' => '', 'class' => 'date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );

				do_action( 'banda_coupon_options' );

			?></div>
			<div id="usage_restriction_coupon_data" class="panel banda_options_panel"><?php

				echo '<div class="options_group">';

				// minimum spend
				banda_wp_text_input( array( 'id' => 'minimum_amount', 'label' => __( 'Minimum spend', 'banda' ), 'placeholder' => __( 'No minimum', 'banda' ), 'description' => __( 'This field allows you to set the minimum spend (subtotal, including taxes) allowed to use the coupon.', 'banda' ), 'data_type' => 'price', 'desc_tip' => true ) );

				// maximum spend
				banda_wp_text_input( array( 'id' => 'maximum_amount', 'label' => __( 'Maximum spend', 'banda' ), 'placeholder' => __( 'No maximum', 'banda' ), 'description' => __( 'This field allows you to set the maximum spend (subtotal, including taxes) allowed when using the coupon.', 'banda' ), 'data_type' => 'price', 'desc_tip' => true ) );

				// Individual use
				banda_wp_checkbox( array( 'id' => 'individual_use', 'label' => __( 'Individual use only', 'banda' ), 'description' => __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'banda' ) ) );

				// Exclude Sale Products
				banda_wp_checkbox( array( 'id' => 'exclude_sale_items', 'label' => __( 'Exclude sale items', 'banda' ), 'description' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'banda' ) ) );

				echo '</div><div class="options_group">';

				// Product ids
				?>
				<p class="form-field"><label><?php _e( 'Products', 'banda' ); ?></label>
				<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="product_ids" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'banda' ); ?>" data-action="banda_json_search_products_and_variations" data-selected="<?php
					$product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'product_ids', true ) ) ) );
					$json_ids    = array();

					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
						}
					}

					echo esc_attr( json_encode( $json_ids ) );
					?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <?php echo wc_help_tip( __( 'Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted.', 'banda' ) ); ?></p>
				<?php

				// Exclude Product ids
				?>
				<p class="form-field"><label><?php _e( 'Exclude products', 'banda' ); ?></label>
				<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="exclude_product_ids" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'banda' ); ?>" data-action="banda_json_search_products_and_variations" data-selected="<?php
					$product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'exclude_product_ids', true ) ) ) );
					$json_ids    = array();

					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
						}
					}

					echo esc_attr( json_encode( $json_ids ) );
				?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <?php echo wc_help_tip( __( 'Products which must not be in the cart to use this coupon or, for "Product Discounts", which products are not discounted.', 'banda' ) ); ?></p>
				<?php

				echo '</div><div class="options_group">';

				// Categories
				?>
				<p class="form-field"><label for="product_categories"><?php _e( 'Product categories', 'banda' ); ?></label>
				<select id="product_categories" name="product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any category', 'banda' ); ?>">
					<?php
						$category_ids = (array) get_post_meta( $post->ID, 'product_categories', true );
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) foreach ( $categories as $cat ) {
							echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
						}
					?>
				</select> <?php echo wc_help_tip( __( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'banda' ) ); ?></p>
				<?php

				// Exclude Categories
				?>
				<p class="form-field"><label for="exclude_product_categories"><?php _e( 'Exclude categories', 'banda' ); ?></label>
				<select id="exclude_product_categories" name="exclude_product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'banda' ); ?>">
					<?php
						$category_ids = (array) get_post_meta( $post->ID, 'exclude_product_categories', true );
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) foreach ( $categories as $cat ) {
							echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'banda' ) ); ?></p>
				<?php

				echo '</div><div class="options_group">';

				// Customers
				banda_wp_text_input( array( 'id' => 'customer_email', 'label' => __( 'Email restrictions', 'banda' ), 'placeholder' => __( 'No restrictions', 'banda' ), 'description' => __( 'List of allowed emails to check against the customer\'s billing email when an order is placed. Separate email addresses with commas.', 'banda' ), 'value' => implode(', ', (array) get_post_meta( $post->ID, 'customer_email', true ) ), 'desc_tip' => true, 'type' => 'email', 'class' => '', 'custom_attributes' => array(
						'multiple' 	=> 'multiple'
				) ) );

				echo '</div>';

				do_action( 'banda_coupon_options_usage_restriction' );

			?></div>
			<div id="usage_limit_coupon_data" class="panel banda_options_panel"><?php

				echo '<div class="options_group">';

				// Usage limit per coupons
				banda_wp_text_input( array( 'id' => 'usage_limit', 'label' => __( 'Usage limit per coupon', 'banda' ), 'placeholder' => _x('Unlimited usage', 'placeholder', 'banda'), 'description' => __( 'How many times this coupon can be used before it is void.', 'banda' ), 'type' => 'number', 'desc_tip' => true, 'class' => 'short', 'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
				) ) );

				// Usage limit per product
				banda_wp_text_input( array( 'id' => 'limit_usage_to_x_items', 'label' => __( 'Limit usage to X items', 'banda' ), 'placeholder' => _x( 'Apply to all qualifying items in cart', 'placeholder', 'banda' ), 'description' => __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'banda' ), 'desc_tip' => true, 'class' => 'short', 'type' => 'number', 'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
				) ) );

				// Usage limit per users
				banda_wp_text_input( array( 'id' => 'usage_limit_per_user', 'label' => __( 'Usage limit per user', 'banda' ), 'placeholder' => _x( 'Unlimited usage', 'placeholder', 'banda' ), 'description' => __( 'How many times this coupon can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.', 'banda' ), 'desc_tip' => true, 'class' => 'short', 'type' => 'number', 'custom_attributes' => array(
						'step' 	=> '1',
						'min'	=> '0'
				) ) );

				echo '</div>';

				do_action( 'banda_coupon_options_usage_limit' );

			?></div>
			<?php do_action( 'banda_coupon_data_panels' ); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;

		// Ensure coupon code is correctly formatted
		$post->post_title = apply_filters( 'banda_coupon_code', $post->post_title );
		$wpdb->update( $wpdb->posts, array( 'post_title' => $post->post_title ), array( 'ID' => $post_id ) );

		// Check for dupe coupons
		$coupon_found = $wpdb->get_var( $wpdb->prepare( "
			SELECT $wpdb->posts.ID
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type = 'shop_coupon'
			AND $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_title = '%s'
			AND $wpdb->posts.ID != %s
		 ", $post->post_title, $post_id ) );

		if ( $coupon_found ) {
			WC_Admin_Meta_Boxes::add_error( __( 'Coupon code already exists - customers will use the latest coupon with this code.', 'banda' ) );
		}

		// Add/Replace data to array
		$type                   = wc_clean( $_POST['discount_type'] );
		$amount                 = wc_format_decimal( $_POST['coupon_amount'] );
		$usage_limit            = empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
		$usage_limit_per_user   = empty( $_POST['usage_limit_per_user'] ) ? '' : absint( $_POST['usage_limit_per_user'] );
		$limit_usage_to_x_items = empty( $_POST['limit_usage_to_x_items'] ) ? '' : absint( $_POST['limit_usage_to_x_items'] );
		$individual_use         = isset( $_POST['individual_use'] ) ? 'yes' : 'no';
		$expiry_date            = wc_clean( $_POST['expiry_date'] );
		$free_shipping          = isset( $_POST['free_shipping'] ) ? 'yes' : 'no';
		$exclude_sale_items     = isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no';
		$minimum_amount         = wc_format_decimal( $_POST['minimum_amount'] );
		$maximum_amount         = wc_format_decimal( $_POST['maximum_amount'] );
		$customer_email         = array_filter( array_map( 'trim', explode( ',', wc_clean( $_POST['customer_email'] ) ) ) );
		$product_ids            = implode( ',', array_filter( array_map( 'intval', explode( ',', $_POST['product_ids'] ) ) ) );
		$exclude_product_ids    = implode( ',', array_filter( array_map( 'intval', explode( ',', $_POST['exclude_product_ids'] ) ) ) );

		$product_categories         = isset( $_POST['product_categories'] ) ? array_map( 'intval', $_POST['product_categories'] ) : array();
		$exclude_product_categories = isset( $_POST['exclude_product_categories'] ) ? array_map( 'intval', $_POST['exclude_product_categories'] ) : array();

		// Save
		update_post_meta( $post_id, 'discount_type', $type );
		update_post_meta( $post_id, 'coupon_amount', $amount );
		update_post_meta( $post_id, 'individual_use', $individual_use );
		update_post_meta( $post_id, 'product_ids', $product_ids );
		update_post_meta( $post_id, 'exclude_product_ids', $exclude_product_ids );
		update_post_meta( $post_id, 'usage_limit', $usage_limit );
		update_post_meta( $post_id, 'usage_limit_per_user', $usage_limit_per_user );
		update_post_meta( $post_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
		update_post_meta( $post_id, 'expiry_date', $expiry_date );
		update_post_meta( $post_id, 'free_shipping', $free_shipping );
		update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
		update_post_meta( $post_id, 'product_categories', $product_categories );
		update_post_meta( $post_id, 'exclude_product_categories', $exclude_product_categories );
		update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
		update_post_meta( $post_id, 'maximum_amount', $maximum_amount );
		update_post_meta( $post_id, 'customer_email', $customer_email );

		// Clear cache
		WC_Cache_Helper::incr_cache_prefix( 'coupons' );

		do_action( 'banda_coupon_options_save', $post_id );
	}
}
