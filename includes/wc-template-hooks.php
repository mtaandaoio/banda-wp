<?php
/**
 * Banda Template Hooks
 *
 * Action/filter hooks used for Banda functions/templates.
 *
 * @author 		Mtaandao
 * @category 	Core
 * @package 	Banda/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'body_class', 'wc_body_class' );
add_filter( 'post_class', 'wc_product_post_class', 20, 3 );

/**
 * WP Header.
 *
 * @see  wc_generator_tag()
 */
add_action( 'get_the_generator_html', 'wc_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'wc_generator_tag', 10, 2 );

/**
 * Content Wrappers.
 *
 * @see banda_output_content_wrapper()
 * @see banda_output_content_wrapper_end()
 */
add_action( 'banda_before_main_content', 'banda_output_content_wrapper', 10 );
add_action( 'banda_after_main_content', 'banda_output_content_wrapper_end', 10 );

/**
 * Sale flashes.
 *
 * @see banda_show_product_loop_sale_flash()
 * @see banda_show_product_sale_flash()
 */
add_action( 'banda_before_shop_loop_item_title', 'banda_show_product_loop_sale_flash', 10 );
add_action( 'banda_before_single_product_summary', 'banda_show_product_sale_flash', 10 );

/**
 * Breadcrumbs.
 *
 * @see banda_breadcrumb()
 */
add_action( 'banda_before_main_content', 'banda_breadcrumb', 20, 0 );

/**
 * Sidebar.
 *
 * @see banda_get_sidebar()
 */
add_action( 'banda_sidebar', 'banda_get_sidebar', 10 );

/**
 * Archive descriptions.
 *
 * @see banda_taxonomy_archive_description()
 * @see banda_product_archive_description()
 */
add_action( 'banda_archive_description', 'banda_taxonomy_archive_description', 10 );
add_action( 'banda_archive_description', 'banda_product_archive_description', 10 );

/**
 * Products Loop.
 *
 * @see banda_result_count()
 * @see banda_catalog_ordering()
 */
add_action( 'banda_before_shop_loop', 'banda_result_count', 20 );
add_action( 'banda_before_shop_loop', 'banda_catalog_ordering', 30 );

/**
 * Product Loop Items.
 *
 * @see banda_template_loop_product_link_open()
 * @see banda_template_loop_product_link_close()
 * @see banda_template_loop_add_to_cart()
 * @see banda_template_loop_product_thumbnail()
 * @see banda_template_loop_product_title()
 * @see banda_template_loop_category_link_open()
 * @see banda_template_loop_category_title()
 * @see banda_template_loop_category_link_close()
 * @see banda_template_loop_price()
 * @see banda_template_loop_rating()
 */
add_action( 'banda_before_shop_loop_item', 'banda_template_loop_product_link_open', 10 );
add_action( 'banda_after_shop_loop_item', 'banda_template_loop_product_link_close', 5 );
add_action( 'banda_after_shop_loop_item', 'banda_template_loop_add_to_cart', 10 );
add_action( 'banda_before_shop_loop_item_title', 'banda_template_loop_product_thumbnail', 10 );
add_action( 'banda_shop_loop_item_title', 'banda_template_loop_product_title', 10 );

add_action( 'banda_before_subcategory', 'banda_template_loop_category_link_open', 10 );
add_action( 'banda_shop_loop_subcategory_title', 'banda_template_loop_category_title', 10 );
add_action( 'banda_after_subcategory', 'banda_template_loop_category_link_close', 10 );

add_action( 'banda_after_shop_loop_item_title', 'banda_template_loop_price', 10 );
add_action( 'banda_after_shop_loop_item_title', 'banda_template_loop_rating', 5 );

/**
 * Subcategories.
 *
 * @see banda_subcategory_thumbnail()
 */
add_action( 'banda_before_subcategory_title', 'banda_subcategory_thumbnail', 10 );

/**
 * Before Single Products Summary Div.
 *
 * @see banda_show_product_images()
 * @see banda_show_product_thumbnails()
 */
add_action( 'banda_before_single_product_summary', 'banda_show_product_images', 20 );
add_action( 'banda_product_thumbnails', 'banda_show_product_thumbnails', 20 );

/**
 * After Single Products Summary Div.
 *
 * @see banda_output_product_data_tabs()
 * @see banda_upsell_display()
 * @see banda_output_related_products()
 */
add_action( 'banda_after_single_product_summary', 'banda_output_product_data_tabs', 10 );
add_action( 'banda_after_single_product_summary', 'banda_upsell_display', 15 );
add_action( 'banda_after_single_product_summary', 'banda_output_related_products', 20 );

/**
 * Product Summary Box.
 *
 * @see banda_template_single_title()
 * @see banda_template_single_rating()
 * @see banda_template_single_price()
 * @see banda_template_single_excerpt()
 * @see banda_template_single_meta()
 * @see banda_template_single_sharing()
 */
add_action( 'banda_single_product_summary', 'banda_template_single_title', 5 );
add_action( 'banda_single_product_summary', 'banda_template_single_rating', 10 );
add_action( 'banda_single_product_summary', 'banda_template_single_price', 10 );
add_action( 'banda_single_product_summary', 'banda_template_single_excerpt', 20 );
add_action( 'banda_single_product_summary', 'banda_template_single_meta', 40 );
add_action( 'banda_single_product_summary', 'banda_template_single_sharing', 50 );

/**
 * Reviews
 *
 * @see banda_review_display_gravatar()
 * @see banda_review_display_rating()
 * @see banda_review_display_meta()
 * @see banda_review_display_comment_text()
 */
add_action( 'banda_review_before', 'banda_review_display_gravatar', 10 );
add_action( 'banda_review_before_comment_meta', 'banda_review_display_rating', 10 );
add_action( 'banda_review_meta', 'banda_review_display_meta', 10 );
add_action( 'banda_review_comment_text', 'banda_review_display_comment_text', 10 );

/**
 * Product Add to cart.
 *
 * @see banda_template_single_add_to_cart()
 * @see banda_simple_add_to_cart()
 * @see banda_grouped_add_to_cart()
 * @see banda_variable_add_to_cart()
 * @see banda_external_add_to_cart()
 * @see banda_single_variation()
 * @see banda_single_variation_add_to_cart_button()
 */
add_action( 'banda_single_product_summary', 'banda_template_single_add_to_cart', 30 );
add_action( 'banda_simple_add_to_cart', 'banda_simple_add_to_cart', 30 );
add_action( 'banda_grouped_add_to_cart', 'banda_grouped_add_to_cart', 30 );
add_action( 'banda_variable_add_to_cart', 'banda_variable_add_to_cart', 30 );
add_action( 'banda_external_add_to_cart', 'banda_external_add_to_cart', 30 );
add_action( 'banda_single_variation', 'banda_single_variation', 10 );
add_action( 'banda_single_variation', 'banda_single_variation_add_to_cart_button', 20 );

/**
 * Pagination after shop loops.
 *
 * @see banda_pagination()
 */
add_action( 'banda_after_shop_loop', 'banda_pagination', 10 );

/**
 * Product page tabs.
 */
add_filter( 'banda_product_tabs', 'banda_default_product_tabs' );
add_filter( 'banda_product_tabs', 'banda_sort_product_tabs', 99 );

/**
 * Checkout.
 *
 * @see banda_checkout_login_form()
 * @see banda_checkout_coupon_form()
 * @see banda_order_review()
 * @see banda_checkout_payment()
 */
add_action( 'banda_before_checkout_form', 'banda_checkout_login_form', 10 );
add_action( 'banda_before_checkout_form', 'banda_checkout_coupon_form', 10 );
add_action( 'banda_checkout_order_review', 'banda_order_review', 10 );
add_action( 'banda_checkout_order_review', 'banda_checkout_payment', 20 );


/**
 * Cart.
 *
 * @see banda_cross_sell_display()
 * @see banda_cart_totals()
 * @see banda_button_proceed_to_checkout()
 */
add_action( 'banda_cart_collaterals', 'banda_cross_sell_display' );
add_action( 'banda_cart_collaterals', 'banda_cart_totals', 10 );
add_action( 'banda_proceed_to_checkout', 'banda_button_proceed_to_checkout', 20 );

/**
 * Footer.
 *
 * @see  wc_print_js()
 * @see banda_demo_store()
 */
add_action( 'wp_footer', 'wc_print_js', 25 );
add_action( 'wp_footer', 'banda_demo_store' );

/**
 * Order details.
 *
 * @see banda_order_details_table()
 * @see banda_order_again_button()
 */
add_action( 'banda_view_order', 'banda_order_details_table', 10 );
add_action( 'banda_thankyou', 'banda_order_details_table', 10 );
add_action( 'banda_order_details_after_order_table', 'banda_order_again_button' );

/**
 * Auth.
 *
 * @see banda_output_auth_header()
 * @see banda_output_auth_footer()
 */
add_action( 'banda_auth_page_header', 'banda_output_auth_header', 10 );
add_action( 'banda_auth_page_footer', 'banda_output_auth_footer', 10 );

/**
 * Comments.
 *
 * Disable Jetpack comments.
 */
add_filter( 'jetpack_comment_form_enabled_for_product', '__return_false' );

/**
 * My Account.
 */
add_action( 'banda_account_navigation', 'banda_account_navigation' );
add_action( 'banda_account_content', 'banda_account_content' );
add_action( 'banda_account_orders_endpoint', 'banda_account_orders' );
add_action( 'banda_account_view-order_endpoint', 'banda_account_view_order' );
add_action( 'banda_account_downloads_endpoint', 'banda_account_downloads' );
add_action( 'banda_account_edit-address_endpoint', 'banda_account_edit_address' );
add_action( 'banda_account_payment-methods_endpoint', 'banda_account_payment_methods' );
add_action( 'banda_account_add-payment-method_endpoint', 'banda_account_add_payment_method' );
add_action( 'banda_account_edit-account_endpoint', 'banda_account_edit_account' );
