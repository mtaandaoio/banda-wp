<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     WC_Post_types
 * @version   2.5.0
 * @package   Banda/Classes/Products
 * @category  Class
 * @author    Mtaandao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Post_types Class.
 */
class WC_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'product_type' ) ) {
			return;
		}

		do_action( 'banda_register_taxonomy' );

		$permalinks = get_option( 'banda_permalinks' );

		register_taxonomy( 'product_type',
			apply_filters( 'banda_taxonomy_objects_product_type', array( 'product' ) ),
			apply_filters( 'banda_taxonomy_args_product_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		register_taxonomy( 'product_cat',
			apply_filters( 'banda_taxonomy_objects_product_cat', array( 'product' ) ),
			apply_filters( 'banda_taxonomy_args_product_cat', array(
				'hierarchical'          => true,
				'update_count_callback' => '_wc_term_recount',
				'label'                 => __( 'Product Categories', 'banda' ),
				'labels' => array(
						'name'              => __( 'Product Categories', 'banda' ),
						'singular_name'     => __( 'Product Category', 'banda' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'banda' ),
						'search_items'      => __( 'Search Product Categories', 'banda' ),
						'all_items'         => __( 'All Product Categories', 'banda' ),
						'parent_item'       => __( 'Parent Product Category', 'banda' ),
						'parent_item_colon' => __( 'Parent Product Category:', 'banda' ),
						'edit_item'         => __( 'Edit Product Category', 'banda' ),
						'update_item'       => __( 'Update Product Category', 'banda' ),
						'add_new_item'      => __( 'Add New Product Category', 'banda' ),
						'new_item_name'     => __( 'New Product Category Name', 'banda' ),
						'not_found'         => __( 'No Product Category found', 'banda' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms',
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'product-category', 'slug', 'banda' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'product_tag',
			apply_filters( 'banda_taxonomy_objects_product_tag', array( 'product' ) ),
			apply_filters( 'banda_taxonomy_args_product_tag', array(
				'hierarchical'          => false,
				'update_count_callback' => '_wc_term_recount',
				'label'                 => __( 'Product Tags', 'banda' ),
				'labels'                => array(
						'name'                       => __( 'Product Tags', 'banda' ),
						'singular_name'              => __( 'Product Tag', 'banda' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'banda' ),
						'search_items'               => __( 'Search Product Tags', 'banda' ),
						'all_items'                  => __( 'All Product Tags', 'banda' ),
						'edit_item'                  => __( 'Edit Product Tag', 'banda' ),
						'update_item'                => __( 'Update Product Tag', 'banda' ),
						'add_new_item'               => __( 'Add New Product Tag', 'banda' ),
						'new_item_name'              => __( 'New Product Tag Name', 'banda' ),
						'popular_items'              => __( 'Popular Product Tags', 'banda' ),
						'separate_items_with_commas' => __( 'Separate Product Tags with commas', 'banda'  ),
						'add_or_remove_items'        => __( 'Add or remove Product Tags', 'banda' ),
						'choose_from_most_used'      => __( 'Choose from the most used Product tags', 'banda' ),
						'not_found'                  => __( 'No Product Tags found', 'banda' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms',
				),
				'rewrite'               => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'banda' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		register_taxonomy( 'product_shipping_class',
			apply_filters( 'banda_taxonomy_objects_product_shipping_class', array( 'product', 'product_variation' ) ),
			apply_filters( 'banda_taxonomy_args_product_shipping_class', array(
				'hierarchical'          => false,
				'update_count_callback' => '_update_post_term_count',
				'label'                 => __( 'Shipping Classes', 'banda' ),
				'labels' => array(
						'name'              => __( 'Shipping Classes', 'banda' ),
						'singular_name'     => __( 'Shipping Class', 'banda' ),
						'menu_name'         => _x( 'Shipping Classes', 'Admin menu name', 'banda' ),
						'search_items'      => __( 'Search Shipping Classes', 'banda' ),
						'all_items'         => __( 'All Shipping Classes', 'banda' ),
						'parent_item'       => __( 'Parent Shipping Class', 'banda' ),
						'parent_item_colon' => __( 'Parent Shipping Class:', 'banda' ),
						'edit_item'         => __( 'Edit Shipping Class', 'banda' ),
						'update_item'       => __( 'Update Shipping Class', 'banda' ),
						'add_new_item'      => __( 'Add New Shipping Class', 'banda' ),
						'new_item_name'     => __( 'New Shipping Class Name', 'banda' )
					),
				'show_ui'               => false,
				'show_in_quick_edit'    => false,
				'show_in_nav_menus'     => false,
				'query_var'             => is_admin(),
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms',
				),
				'rewrite'               => false,
			) )
		);

		global $wc_product_attributes;

		$wc_product_attributes = array();

		if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( $name = wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
					$tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
					$label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$wc_product_attributes[ $name ] = $tax;
					$taxonomy_data                  = array(
						'hierarchical'          => true,
						'update_count_callback' => '_update_post_term_count',
						'labels'                => array(
								'name'              => $label,
								'singular_name'     => $label,
								'search_items'      => sprintf( __( 'Search %s', 'banda' ), $label ),
								'all_items'         => sprintf( __( 'All %s', 'banda' ), $label ),
								'parent_item'       => sprintf( __( 'Parent %s', 'banda' ), $label ),
								'parent_item_colon' => sprintf( __( 'Parent %s:', 'banda' ), $label ),
								'edit_item'         => sprintf( __( 'Edit %s', 'banda' ), $label ),
								'update_item'       => sprintf( __( 'Update %s', 'banda' ), $label ),
								'add_new_item'      => sprintf( __( 'Add New %s', 'banda' ), $label ),
								'new_item_name'     => sprintf( __( 'New %s', 'banda' ), $label ),
								'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'banda' ), $label ),
							),
						'show_ui'            => true,
						'show_in_quick_edit' => false,
						'show_in_menu'       => false,
						'show_in_nav_menus'  => false,
						'meta_box_cb'        => false,
						'query_var'          => 1 === $tax->attribute_public,
						'rewrite'            => false,
						'sort'               => false,
						'public'             => 1 === $tax->attribute_public,
						'show_in_nav_menus'  => 1 === $tax->attribute_public && apply_filters( 'banda_attribute_show_in_nav_menus', false, $name ),
						'capabilities'       => array(
							'manage_terms' => 'manage_product_terms',
							'edit_terms'   => 'edit_product_terms',
							'delete_terms' => 'delete_product_terms',
							'assign_terms' => 'assign_product_terms',
						)
					);

					if ( 1 === $tax->attribute_public ) {
						$taxonomy_data['rewrite'] = array(
							'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $tax->attribute_name ),
							'with_front'   => false,
							'hierarchical' => true
						);
					}

					register_taxonomy( $name, apply_filters( "banda_taxonomy_objects_{$name}", array( 'product' ) ), apply_filters( "banda_taxonomy_args_{$name}", $taxonomy_data ) );
				}
			}
		}

		do_action( 'banda_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('product') ) {
			return;
		}

		do_action( 'banda_register_post_type' );

		$permalinks        = get_option( 'banda_permalinks' );
		$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'banda' ) : $permalinks['product_base'];

		register_post_type( 'product',
			apply_filters( 'banda_register_post_type_product',
				array(
					'labels'              => array(
							'name'                  => __( 'Products', 'banda' ),
							'singular_name'         => __( 'Product', 'banda' ),
							'menu_name'             => _x( 'Products', 'Admin menu name', 'banda' ),
							'add_new'               => __( 'Add Product', 'banda' ),
							'add_new_item'          => __( 'Add New Product', 'banda' ),
							'edit'                  => __( 'Edit', 'banda' ),
							'edit_item'             => __( 'Edit Product', 'banda' ),
							'new_item'              => __( 'New Product', 'banda' ),
							'view'                  => __( 'View Product', 'banda' ),
							'view_item'             => __( 'View Product', 'banda' ),
							'search_items'          => __( 'Search Products', 'banda' ),
							'not_found'             => __( 'No Products found', 'banda' ),
							'not_found_in_trash'    => __( 'No Products found in trash', 'banda' ),
							'parent'                => __( 'Parent Product', 'banda' ),
							'featured_image'        => __( 'Product Image', 'banda' ),
							'set_featured_image'    => __( 'Set product image', 'banda' ),
							'remove_featured_image' => __( 'Remove product image', 'banda' ),
							'use_featured_image'    => __( 'Use as product image', 'banda' ),
							'insert_into_item'      => __( 'Insert into product', 'banda' ),
							'uploaded_to_this_item' => __( 'Uploaded to this product', 'banda' ),
							'filter_items_list'     => __( 'Filter products', 'banda' ),
							'items_list_navigation' => __( 'Products navigation', 'banda' ),
							'items_list'            => __( 'Products list', 'banda' ),
						),
					'description'         => __( 'This is where you can add new products to your store.', 'banda' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'product',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => ( $shop_page_id = wc_get_page_id( 'shop' ) ) && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop',
					'show_in_nav_menus'   => true
				)
			)
		);

		register_post_type( 'product_variation',
			apply_filters( 'banda_register_post_type_product_variation',
				array(
					'label'        => __( 'Variations', 'banda' ),
					'public'       => false,
					'hierarchical' => false,
					'supports'     => false,
					'capability_type' => 'product'
				)
			)
		);

		wc_register_order_type(
			'shop_order',
			apply_filters( 'banda_register_post_type_shop_order',
				array(
					'labels'              => array(
							'name'                  => __( 'Orders', 'banda' ),
							'singular_name'         => _x( 'Order', 'shop_order post type singular name', 'banda' ),
							'add_new'               => __( 'Add Order', 'banda' ),
							'add_new_item'          => __( 'Add New Order', 'banda' ),
							'edit'                  => __( 'Edit', 'banda' ),
							'edit_item'             => __( 'Edit Order', 'banda' ),
							'new_item'              => __( 'New Order', 'banda' ),
							'view'                  => __( 'View Order', 'banda' ),
							'view_item'             => __( 'View Order', 'banda' ),
							'search_items'          => __( 'Search Orders', 'banda' ),
							'not_found'             => __( 'No Orders found', 'banda' ),
							'not_found_in_trash'    => __( 'No Orders found in trash', 'banda' ),
							'parent'                => __( 'Parent Orders', 'banda' ),
							'menu_name'             => _x( 'Orders', 'Admin menu name', 'banda' ),
							'filter_items_list'     => __( 'Filter orders', 'banda' ),
							'items_list_navigation' => __( 'Orders navigation', 'banda' ),
							'items_list'            => __( 'Orders list', 'banda' ),
						),
					'description'         => __( 'This is where store orders are stored.', 'banda' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'shop_order',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_banda' ) ? 'banda' : true,
					'hierarchical'        => false,
					'show_in_nav_menus'   => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'comments', 'custom-fields' ),
					'has_archive'         => false,
				)
			)
		);

		wc_register_order_type(
			'shop_order_refund',
			apply_filters( 'banda_register_post_type_shop_order_refund',
				array(
					'label'                            => __( 'Refunds', 'banda' ),
					'capability_type'                  => 'shop_order',
					'public'                           => false,
					'hierarchical'                     => false,
					'supports'                         => false,
					'exclude_from_orders_screen'       => false,
					'add_order_meta_boxes'             => false,
					'exclude_from_order_count'         => true,
					'exclude_from_order_views'         => false,
					'exclude_from_order_reports'       => false,
					'exclude_from_order_sales_reports' => true,
					'class_name'                       => 'WC_Order_Refund'
				)
			)
		);

		if ( 'yes' == get_option( 'banda_enable_coupons' ) ) {
			register_post_type( 'shop_coupon',
				apply_filters( 'banda_register_post_type_shop_coupon',
					array(
						'labels'              => array(
								'name'                  => __( 'Coupons', 'banda' ),
								'singular_name'         => __( 'Coupon', 'banda' ),
								'menu_name'             => _x( 'Coupons', 'Admin menu name', 'banda' ),
								'add_new'               => __( 'Add Coupon', 'banda' ),
								'add_new_item'          => __( 'Add New Coupon', 'banda' ),
								'edit'                  => __( 'Edit', 'banda' ),
								'edit_item'             => __( 'Edit Coupon', 'banda' ),
								'new_item'              => __( 'New Coupon', 'banda' ),
								'view'                  => __( 'View Coupons', 'banda' ),
								'view_item'             => __( 'View Coupon', 'banda' ),
								'search_items'          => __( 'Search Coupons', 'banda' ),
								'not_found'             => __( 'No Coupons found', 'banda' ),
								'not_found_in_trash'    => __( 'No Coupons found in trash', 'banda' ),
								'parent'                => __( 'Parent Coupon', 'banda' ),
								'filter_items_list'     => __( 'Filter coupons', 'banda' ),
								'items_list_navigation' => __( 'Coupons navigation', 'banda' ),
								'items_list'            => __( 'Coupons list', 'banda' ),
							),
						'description'         => __( 'This is where you can add new coupons that customers can use in your store.', 'banda' ),
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => 'shop_coupon',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'show_in_menu'        => current_user_can( 'manage_banda' ) ? 'banda' : true,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title' ),
						'show_in_nav_menus'   => false,
						'show_in_admin_bar'   => true
					)
				)
			);
		}

		register_post_type( 'shop_webhook',
			apply_filters( 'banda_register_post_type_shop_webhook',
				array(
					'labels'              => array(
						'name'               => __( 'Webhooks', 'banda' ),
						'singular_name'      => __( 'Webhook', 'banda' ),
						'menu_name'          => _x( 'Webhooks', 'Admin menu name', 'banda' ),
						'add_new'            => __( 'Add Webhook', 'banda' ),
						'add_new_item'       => __( 'Add New Webhook', 'banda' ),
						'edit'               => __( 'Edit', 'banda' ),
						'edit_item'          => __( 'Edit Webhook', 'banda' ),
						'new_item'           => __( 'New Webhook', 'banda' ),
						'view'               => __( 'View Webhooks', 'banda' ),
						'view_item'          => __( 'View Webhook', 'banda' ),
						'search_items'       => __( 'Search Webhooks', 'banda' ),
						'not_found'          => __( 'No Webhooks found', 'banda' ),
						'not_found_in_trash' => __( 'No Webhooks found in trash', 'banda' ),
						'parent'             => __( 'Parent Webhook', 'banda' )
					),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'shop_webhook',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => false,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => false,
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => false
				)
			)
		);
	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public static function register_post_status() {

		$order_statuses = apply_filters( 'banda_register_shop_order_post_statuses',
			array(
				'wc-pending'    => array(
					'label'                     => _x( 'Pending Payment', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'banda' )
				),
				'wc-processing' => array(
					'label'                     => _x( 'Processing', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'banda' )
				),
				'wc-on-hold'    => array(
					'label'                     => _x( 'On Hold', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'banda' )
				),
				'wc-completed'  => array(
					'label'                     => _x( 'Completed', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'banda' )
				),
				'wc-cancelled'  => array(
					'label'                     => _x( 'Cancelled', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'banda' )
				),
				'wc-refunded'   => array(
					'label'                     => _x( 'Refunded', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'banda' )
				),
				'wc-failed'     => array(
					'label'                     => _x( 'Failed', 'Order status', 'banda' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'banda' )
				),
			)
		);

		foreach ( $order_statuses as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}
	}

	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'product' );
		}
	}

	/**
	 * Added product for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'product';

		return $post_types;
	}
}

WC_Post_types::init();
