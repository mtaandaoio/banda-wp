<?php
/**
 * Banda Product Settings
 *
 * @author   Mtaandao
 * @category Admin
 * @package  Banda/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_Products' ) ) :

/**
 * WC_Settings_Products.
 */
class WC_Settings_Products extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'products';
		$this->label = __( 'Products', 'banda' );

		add_filter( 'banda_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'banda_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'banda_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'banda_sections_' . $this->id, array( $this, 'output_sections' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''          	=> __( 'General', 'banda' ),
			'display'       => __( 'Display', 'banda' ),
			'inventory' 	=> __( 'Inventory', 'banda' ),
			'downloadable' 	=> __( 'Downloadable Products', 'banda' ),
		);

		return apply_filters( 'banda_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'display' == $current_section ) {

			$settings = apply_filters( 'banda_product_settings', array(

				array(
					'title' => __( 'Shop & Product Pages', 'banda' ),
					'type' 	=> 'title',
					'desc' 	=> '',
					'id' 	=> 'catalog_options'
				),

				array(
					'title'    => __( 'Shop Page', 'banda' ),
					'desc'     => '<br/>' . sprintf( __( 'The base page can also be used in your <a href="%s">product permalinks</a>.', 'banda' ), admin_url( 'options-permalink.php' ) ),
					'id'       => 'banda_shop_page_id',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => __( 'This sets the base page of your shop - this is where your product archive will be.', 'banda' ),
				),

				array(
					'title'    => __( 'Shop Page Display', 'banda' ),
					'desc'     => __( 'This controls what is shown on the product archive.', 'banda' ),
					'id'       => 'banda_shop_page_display',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => '',
					'type'     => 'select',
					'options'  => array(
						''              => __( 'Show products', 'banda' ),
						'subcategories' => __( 'Show categories', 'banda' ),
						'both'          => __( 'Show categories &amp; products', 'banda' ),
					),
					'desc_tip' =>  true,
				),

				array(
					'title'    => __( 'Default Category Display', 'banda' ),
					'desc'     => __( 'This controls what is shown on category archives.', 'banda' ),
					'id'       => 'banda_category_archive_display',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => '',
					'type'     => 'select',
					'options'  => array(
						''              => __( 'Show products', 'banda' ),
						'subcategories' => __( 'Show subcategories', 'banda' ),
						'both'          => __( 'Show subcategories &amp; products', 'banda' ),
					),
					'desc_tip' =>  true,
				),

				array(
					'title'    => __( 'Default Product Sorting', 'banda' ),
					'desc'     => __( 'This controls the default sort order of the catalog.', 'banda' ),
					'id'       => 'banda_default_catalog_orderby',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'menu_order',
					'type'     => 'select',
					'options'  => apply_filters( 'banda_default_catalog_orderby_options', array(
						'menu_order' => __( 'Default sorting (custom ordering + name)', 'banda' ),
						'popularity' => __( 'Popularity (sales)', 'banda' ),
						'rating'     => __( 'Average Rating', 'banda' ),
						'date'       => __( 'Sort by most recent', 'banda' ),
						'price'      => __( 'Sort by price (asc)', 'banda' ),
						'price-desc' => __( 'Sort by price (desc)', 'banda' ),
					) ),
					'desc_tip' =>  true,
				),

				array(
					'title'         => __( 'Add to cart behaviour', 'banda' ),
					'desc'          => __( 'Redirect to the cart page after successful addition', 'banda' ),
					'id'            => 'banda_cart_redirect_after_add',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start'
				),

				array(
					'desc'          => __( 'Enable AJAX add to cart buttons on archives', 'banda' ),
					'id'            => 'banda_enable_ajax_add_to_cart',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end'
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'catalog_options'
				),

				array(
					'title' => __( 'Product Images', 'banda' ),
					'type' 	=> 'title',
					'desc' 	=> sprintf( __( 'These settings affect the display and dimensions of images in your catalog - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'banda' ), 'https://wordpress.org/extend/plugins/regenerate-thumbnails/' ),
					'id' 	=> 'image_options'
				),

				array(
					'title'    => __( 'Catalog Images', 'banda' ),
					'desc'     => __( 'This size is usually used in product listings', 'banda' ),
					'id'       => 'shop_catalog_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '300',
						'height' => '300',
						'crop'   => 1
					),
					'desc_tip' =>  true,
				),

				array(
					'title'    => __( 'Single Product Image', 'banda' ),
					'desc'     => __( 'This is the size used by the main image on the product page.', 'banda' ),
					'id'       => 'shop_single_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '600',
						'height' => '600',
						'crop'   => 1
					),
					'desc_tip' =>  true,
				),

				array(
					'title'    => __( 'Product Thumbnails', 'banda' ),
					'desc'     => __( 'This size is usually used for the gallery of images on the product page.', 'banda' ),
					'id'       => 'shop_thumbnail_image_size',
					'css'      => '',
					'type'     => 'image_width',
					'default'  => array(
						'width'  => '180',
						'height' => '180',
						'crop'   => 1
					),
					'desc_tip' =>  true,
				),

				array(
					'title'         => __( 'Product Image Gallery', 'banda' ),
					'desc'          => __( 'Enable Lightbox for product images', 'banda' ),
					'id'            => 'banda_enable_lightbox',
					'default'       => 'yes',
					'desc_tip'      => __( 'Include Banda\'s lightbox. Product gallery images will open in a lightbox.', 'banda' ),
					'type'          => 'checkbox'
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'image_options'
				)

			));
		} elseif ( 'inventory' == $current_section ) {

			$settings = apply_filters( 'banda_inventory_settings', array(

				array(
					'title' => __( 'Inventory', 'banda' ),
					'type' 	=> 'title',
					'desc' 	=> '',
					'id' 	=> 'product_inventory_options'
				),

				array(
					'title'   => __( 'Manage Stock', 'banda' ),
					'desc'    => __( 'Enable stock management', 'banda' ),
					'id'      => 'banda_manage_stock',
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'title'             => __( 'Hold Stock (minutes)', 'banda' ),
					'desc'              => __( 'Hold stock (for unpaid orders) for x minutes. When this limit is reached, the pending order will be cancelled. Leave blank to disable.', 'banda' ),
					'id'                => 'banda_hold_stock_minutes',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1
					),
					'css'               => 'width: 80px;',
					'default'           => '60',
					'autoload'          => false
				),

				array(
					'title'         => __( 'Notifications', 'banda' ),
					'desc'          => __( 'Enable low stock notifications', 'banda' ),
					'id'            => 'banda_notify_low_stock',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'autoload'      => false
				),

				array(
					'desc'          => __( 'Enable out of stock notifications', 'banda' ),
					'id'            => 'banda_notify_no_stock',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
					'autoload'      => false
				),

				array(
					'title'    => __( 'Notification Recipient(s)', 'banda' ),
					'desc'     => __( 'Enter recipients (comma separated) that will receive this notification.', 'banda' ),
					'id'       => 'banda_stock_email_recipient',
					'type'     => 'text',
					'default'  => get_option( 'admin_email' ),
					'css'      => 'width: 250px;',
					'autoload' => false,
					'desc_tip' => true
				),

				array(
					'title'             => __( 'Low Stock Threshold', 'banda' ),
					'desc'              => '',
					'id'                => 'banda_notify_low_stock_amount',
					'css'               => 'width:50px;',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1
					),
					'default'           => '2',
					'autoload'          => false
				),

				array(
					'title'             => __( 'Out Of Stock Threshold', 'banda' ),
					'desc'              => '',
					'id'                => 'banda_notify_no_stock_amount',
					'css'               => 'width:50px;',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1
					),
					'default'           => '0'
				),

				array(
					'title'    => __( 'Out Of Stock Visibility', 'banda' ),
					'desc'     => __( 'Hide out of stock items from the catalog', 'banda' ),
					'id'       => 'banda_hide_out_of_stock_items',
					'default'  => 'no',
					'type'     => 'checkbox'
				),

				array(
					'title'    => __( 'Stock Display Format', 'banda' ),
					'desc'     => __( 'This controls how stock is displayed on the frontend.', 'banda' ),
					'id'       => 'banda_stock_format',
					'css'      => 'min-width:150px;',
					'class'    => 'wc-enhanced-select',
					'default'  => '',
					'type'     => 'select',
					'options'  => array(
						''           => __( 'Always show stock e.g. "12 in stock"', 'banda' ),
						'low_amount' => __( 'Only show stock when low e.g. "Only 2 left in stock" vs. "In Stock"', 'banda' ),
						'no_amount'  => __( 'Never show stock amount', 'banda' ),
					),
					'desc_tip' =>  true,
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'product_inventory_options'
				),

			));

		} elseif ( 'downloadable' == $current_section ) {
			$settings = apply_filters( 'banda_downloadable_products_settings', array(
				array(
					'title' => __( 'Downloadable Products', 'banda' ),
					'type' 	=> 'title',
					'id' 	=> 'digital_download_options'
				),

				array(
					'title'    => __( 'File Download Method', 'banda' ),
					'desc'     => __( 'Forcing downloads will keep URLs hidden, but some servers may serve large files unreliably. If supported, <code>X-Accel-Redirect</code>/ <code>X-Sendfile</code> can be used to serve downloads instead (server requires <code>mod_xsendfile</code>).', 'banda' ),
					'id'       => 'banda_file_download_method',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'force',
					'desc_tip' =>  true,
					'options'  => array(
						'force'     => __( 'Force Downloads', 'banda' ),
						'xsendfile' => __( 'X-Accel-Redirect/X-Sendfile', 'banda' ),
						'redirect'  => __( 'Redirect only', 'banda' ),
					),
					'autoload' => false
				),

				array(
					'title'         => __( 'Access Restriction', 'banda' ),
					'desc'          => __( 'Downloads require login', 'banda' ),
					'id'            => 'banda_downloads_require_login',
					'type'          => 'checkbox',
					'default'       => 'no',
					'desc_tip'      => __( 'This setting does not apply to guest purchases.', 'banda' ),
					'checkboxgroup' => 'start',
					'autoload'      => false
				),

				array(
					'desc'          => __( 'Grant access to downloadable products after payment', 'banda' ),
					'id'            => 'banda_downloads_grant_access_after_payment',
					'type'          => 'checkbox',
					'default'       => 'yes',
					'desc_tip'      => __( 'Enable this option to grant access to downloads when orders are "processing", rather than "completed".', 'banda' ),
					'checkboxgroup' => 'end',
					'autoload'      => false
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'digital_download_options'
				),

			));

		} else {
			$settings = apply_filters( 'banda_products_general_settings', array(
				array(
					'title' 	=> __( 'Measurements', 'banda' ),
					'type' 		=> 'title',
					'id' 		=> 'product_measurement_options'
				),

				array(
					'title'    => __( 'Weight Unit', 'banda' ),
					'desc'     => __( 'This controls what unit you will define weights in.', 'banda' ),
					'id'       => 'banda_weight_unit',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'kg',
					'type'     => 'select',
					'options'  => array(
						'kg'  => __( 'kg', 'banda' ),
						'g'   => __( 'g', 'banda' ),
						'lbs' => __( 'lbs', 'banda' ),
						'oz'  => __( 'oz', 'banda' ),
					),
					'desc_tip' =>  true,
				),

				array(
					'title'    => __( 'Dimensions Unit', 'banda' ),
					'desc'     => __( 'This controls what unit you will define lengths in.', 'banda' ),
					'id'       => 'banda_dimension_unit',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'cm',
					'type'     => 'select',
					'options'  => array(
						'm'  => __( 'm', 'banda' ),
						'cm' => __( 'cm', 'banda' ),
						'mm' => __( 'mm', 'banda' ),
						'in' => __( 'in', 'banda' ),
						'yd' => __( 'yd', 'banda' ),
					),
					'desc_tip' =>  true,
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'product_measurement_options',
				),

				array(
					'title' => __( 'Reviews', 'banda' ),
					'type' 	=> 'title',
					'desc' 	=> '',
					'id' 	=> 'product_rating_options',
				),

				array(
					'title'           => __( 'Product Ratings', 'banda' ),
					'desc'            => __( 'Enable ratings on reviews', 'banda' ),
					'id'              => 'banda_enable_review_rating',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'start',
					'show_if_checked' => 'option',
				),

				array(
					'desc'            => __( 'Ratings are required to leave a review', 'banda' ),
					'id'              => 'banda_review_rating_required',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => '',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'desc'            => __( 'Show "verified owner" label for customer reviews', 'banda' ),
					'id'              => 'banda_review_rating_verification_label',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => '',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'desc'            => __( 'Only allow reviews from "verified owners"', 'banda' ),
					'id'              => 'banda_review_rating_verification_required',
					'default'         => 'no',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'end',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'product_rating_options'
				),

			));
		}

		return apply_filters( 'banda_get_settings_' . $this->id, $settings, $current_section );
	}
}

endif;

return new WC_Settings_Products();
