<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see banda_default_product_tabs()
 */
$tabs = apply_filters( 'banda_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>

	<div class="banda-tabs wc-tabs-wrapper">
		<ul class="tabs wc-tabs">
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<li class="<?php echo esc_attr( $key ); ?>_tab">
					<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'banda_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php foreach ( $tabs as $key => $tab ) : ?>
			<div class="banda-Tabs-panel banda-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ); ?>
			</div>
		<?php endforeach; ?>
	</div>

<?php endif; ?>
