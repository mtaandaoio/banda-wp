<?php
/**
 * Add some content to the help tab
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Admin_Help' ) ) :

/**
 * WC_Admin_Help Class.
 */
class WC_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( "current_screen", array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add Contextual help tabs.
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, wc_get_screen_ids() ) ) {
			return;
		}

		$video_map = array(
			'wc-settings' => array(
				'title' => __( 'General Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/mz2l10u5f6?videoFoam=true'
			),
			'wc-settings-general' => array(
				'title' => __( 'General Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/mz2l10u5f6?videoFoam=true'
			),
			'wc-settings-products' => array(
				'title' => __( 'Product Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/lolkan4fxf?videoFoam=true'
			),
			'wc-settings-tax' => array(
				'title' => __( 'Tax Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/qp1v19dwrh?videoFoam=true'
			),
			'wc-settings-shipping' => array(
				'title' => __( 'Shipping Zones', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/95yiocro6p?videoFoam=true'
			),
			'wc-settings-shipping-options' => array(
				'title' => __( 'Shipping Options', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/9c9008dxnr?videoFoam=true'
			),
			'wc-settings-shipping-classes' => array(
				'title' => __( 'Shipping Classes', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/tpqg17aq99?videoFoam=true'
			),
			'wc-settings-checkout' => array(
				'title' => __( 'Checkout Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/65yjv96z51?videoFoam=true'
			),
			'wc-settings-checkout-bacs' => array(
				'title' => __( 'Bank Transfer (BACS) Payments', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/dh4piy3sek?videoFoam=true'
			),
			'wc-settings-checkout-cheque' => array(
				'title' => __( 'Check Payments', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/u2m2kcakea?videoFoam=true'
			),
			'wc-settings-checkout-cod' => array(
				'title' => __( 'Cash on Delivery', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/8hyli8wu5f?videoFoam=true'
			),
			'wc-settings-checkout-paypal' => array(
				'title' => __( 'PayPal Standard', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/rbl7e7l4k2?videoFoam=true'
			),
			'wc-settings-checkout-paypalbraintree_cards' => array(
				'title' => __( 'PayPal by Braintree', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/oyksirgn40?videoFoam=true'
			),
			'wc-settings-checkout-stripe' => array(
				'title' => __( 'Stripe', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/mf975hx5de?videoFoam=true'
			),
			'wc-settings-checkout-simplify_commerce' => array(
				'title' => __( 'Simplify Commerce', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/jdfzjiiw61?videoFoam=true'
			),
			'wc-settings-account' => array(
				'title' => __( 'Account Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/35mazq7il2?videoFoam=true'
			),
			'wc-settings-email' => array(
				'title' => __( 'Email Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/svcaftq4xv?videoFoam=true'
			),
			'wc-settings-api' => array(
				'title' => __( 'Webhook Settings', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/1q0ny74vvq?videoFoam=true'
			),
			'product' => array(
				'title' => __( 'Simple Products', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/ziyjmd4kut?videoFoam=true'
			),
			'edit-product_cat' => array(
				'title' => __( 'Product Categories', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/f0j5gzqigg?videoFoam=true'
			),
			'edit-product_tag' => array(
				'title' => __( 'Product Categories, Tags, Shipping Classes, &amp; Attributes', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/f0j5gzqigg?videoFoam=true'
			),
			'product_attributes' => array(
				'title' => __( 'Product Categories, Tags, Shipping Classes, &amp; Attributes', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/f0j5gzqigg?videoFoam=true'
			),
			'wc-status' => array(
				'title' => __( 'System Status', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/xdn733nnhi?videoFoam=true'
			),
			'wc-reports' => array(
				'title' => __( 'Reports', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/6aasex0w99?videoFoam=true'
			),
			'edit-shop_coupon' => array(
				'title' => __( 'Coupons', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/gupd4h8sit?videoFoam=true'
			),
			'shop_coupon' => array(
				'title' => __( 'Coupons', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/gupd4h8sit?videoFoam=true'
			),
			'edit-shop_order' => array(
				'title' => __( 'Managing Orders', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/n8n0sa8hee?videoFoam=true'
			),
			'shop_order' => array(
				'title' => __( 'Managing Orders', 'banda' ),
				'url'   => '//fast.wistia.net/embed/iframe/n8n0sa8hee?videoFoam=true'
			)
		);

		$page      = empty( $_GET['page'] ) ? '' : sanitize_title( $_GET['page'] );
		$tab       = empty( $_GET['tab'] ) ? '' : sanitize_title( $_GET['tab'] );
		$section   = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );
		$video_key = $page ? implode( '-', array_filter( array( $page, $tab, $section ) ) ) : $screen->id;

		// Fallback for sections
		if ( ! isset( $video_map[ $video_key ] ) ) {
			$video_key = $page ? implode( '-', array_filter( array( $page, $tab ) ) ) : $screen->id;
		}

		// Fallback for tabs
		if ( ! isset( $video_map[ $video_key ] ) ) {
			$video_key = $page ? $page : $screen->id;
		}

		if ( isset( $video_map[ $video_key ] ) ) {
			$screen->add_help_tab( array(
				'id'        => 'banda_101_tab',
				'title'     => __( 'Banda 101', 'banda' ),
				'content'   =>
					'<h2><a href="https://docs.mtaandao.co.ke/document/banda-101-video-series/?utm_source=helptab&utm_medium=product&utm_content=videos&utm_campaign=bandaplugin">' . __( 'Banda 101', 'banda' ) . '</a> &ndash; ' . esc_html( $video_map[ $video_key ]['title'] ) . '</h2>' .
					'<iframe data-src="' . esc_url( $video_map[ $video_key ]['url'] ) . '" src="" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="480" height="298"></iframe>'
			) );
		}

		$screen->add_help_tab( array(
			'id'        => 'banda_support_tab',
			'title'     => __( 'Help &amp; Support', 'banda' ),
			'content'   =>
				'<h2>' . __( 'Help &amp; Support', 'banda' ) . '</h2>' .
				'<p>' . sprintf(
					__( 'Should you need help understanding, using, or extending Banda, %splease read our documentation%s. You will find all kinds of resources including snippets, tutorials and much more.' , 'banda' ),
					'<a href="https://docs.mtaandao.co.ke/documentation/plugins/banda/?utm_source=helptab&utm_medium=product&utm_content=docs&utm_campaign=bandaplugin">',
					'</a>'
				) . '</p>' .
				'<p>' . sprintf(
					__( 'For further assistance with Banda core you can use the %1$scommunity forum%2$s. If you need help with premium extensions sold by Banda, please %3$suse our helpdesk%4$s.', 'banda' ),
					'<a href="https://wordpress.org/support/plugin/banda">',
					'</a>',
					'<a href="https://mtaandao.co.ke/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=bandaplugin">',
					'</a>'
				) . '</p>' .
				'<p>' . __( 'Before asking for help we recommend checking the system status page to identify any problems with your configuration.', 'banda' ) . '</p>' .
				'<p><a href="' . admin_url( 'admin.php?page=wc-status' ) . '" class="button button-primary">' . __( 'System Status', 'banda' ) . '</a> <a href="' . 'https://wordpress.org/support/plugin/banda' . '" class="button">' . __( 'Community Forum', 'banda' ) . '</a> <a href="' . 'https://mtaandao.co.ke/my-account/tickets/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=bandaplugin' . '" class="button">' . __( 'Mtaandao Helpdesk', 'banda' ) . '</a></p>'
		) );

		$screen->add_help_tab( array(
			'id'        => 'banda_bugs_tab',
			'title'     => __( 'Found a bug?', 'banda' ),
			'content'   =>
				'<h2>' . __( 'Found a bug?', 'banda' ) . '</h2>' .
				'<p>' . sprintf( __( 'If you find a bug within Banda core you can create a ticket via <a href="%s">Github issues</a>. Ensure you read the <a href="%s">contribution guide</a> prior to submitting your report. To help us solve your issue, please be as descriptive as possible and include your <a href="%s">system status report</a>.', 'banda' ), 'https://github.com/mtaandao/banda/issues?state=open', 'https://github.com/mtaandao/banda/blob/master/.github/CONTRIBUTING.md', admin_url( 'admin.php?page=wc-status' ) ) . '</p>' .
				'<p><a href="' . 'https://github.com/mtaandao/banda/issues?state=open' . '" class="button button-primary">' . __( 'Report a bug', 'banda' ) . '</a> <a href="' . admin_url( 'admin.php?page=wc-status' ) . '" class="button">' . __( 'System Status', 'banda' ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
			'id'        => 'banda_education_tab',
			'title'     => __( 'Education', 'banda' ),
			'content'   =>
				'<h2>' . __( 'Education', 'banda' ) . '</h2>' .
				'<p>' . __( 'If you would like to learn about using Banda from an expert, consider following a Banda course ran by one of our educational partners.', 'banda' ) . '</p>' .
				'<p><a href="' . 'https://mtaandao.co.ke/educational-partners/?utm_source=helptab&utm_medium=product&utm_content=edupartners&utm_campaign=bandaplugin' . '" class="button button-primary">' . __( 'View Education Partners', 'banda' ) . '</a></p>'
		) );

		$screen->add_help_tab( array(
			'id'        => 'banda_onboard_tab',
			'title'     => __( 'Setup Wizard', 'banda' ),
			'content'   =>
				'<h2>' . __( 'Setup Wizard', 'banda' ) . '</h2>' .
				'<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'banda' ) . '</p>' .
				'<p><a href="' . admin_url( 'index.php?page=wc-setup' ) . '" class="button button-primary">' . __( 'Setup Wizard', 'banda' ) . '</a></p>'

		) );

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'banda' ) . '</strong></p>' .
			'<p><a href="' . 'https://mtaandao.co.ke/?utm_source=helptab&utm_medium=product&utm_content=about&utm_campaign=bandaplugin' . '" target="_blank">' . __( 'About Banda', 'banda' ) . '</a></p>' .
			'<p><a href="' . 'https://wordpress.org/extend/plugins/banda/' . '" target="_blank">' . __( 'WordPress.org Project', 'banda' ) . '</a></p>' .
			'<p><a href="' . 'https://github.com/mtaandao/banda' . '" target="_blank">' . __( 'Github Project', 'banda' ) . '</a></p>' .
			'<p><a href="' . 'https://mtaandao.co.ke/product-category/themes/banda/?utm_source=helptab&utm_medium=product&utm_content=wcthemes&utm_campaign=bandaplugin' . '" target="_blank">' . __( 'Official Themes', 'banda' ) . '</a></p>' .
			'<p><a href="' . 'https://mtaandao.co.ke/product-category/banda-extensions/?utm_source=helptab&utm_medium=product&utm_content=wcextensions&utm_campaign=bandaplugin' . '" target="_blank">' . __( 'Official Extensions', 'banda' ) . '</a></p>'
		);
	}

}

endif;

return new WC_Admin_Help();
