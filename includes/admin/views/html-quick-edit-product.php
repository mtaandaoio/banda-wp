<?php
/**
 * Admin View: Quick Edit Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-left">
	<div id="banda-fields" class="inline-edit-col">

		<h4><?php _e( 'Product Data', 'banda' ); ?></h4>

		<?php do_action( 'banda_product_quick_edit_start' ); ?>

		<?php if ( wc_product_sku_enabled() ) : ?>

			<label>
				<span class="title"><?php _e( 'SKU', 'banda' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_sku" class="text sku" value="">
				</span>
			</label>
			<br class="clear" />

		<?php endif; ?>

		<div class="price_fields">
			<label>
				<span class="title"><?php _e( 'Price', 'banda' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_regular_price" class="text wc_input_price regular_price" placeholder="<?php esc_attr_e( 'Regular Price', 'banda' ); ?>" value="">
				</span>
			</label>
			<br class="clear" />
			<label>
				<span class="title"><?php _e( 'Sale', 'banda' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_sale_price" class="text wc_input_price sale_price" placeholder="<?php esc_attr_e( 'Sale Price', 'banda' ); ?>" value="">
				</span>
			</label>
			<br class="clear" />
		</div>

		<?php if ( wc_tax_enabled() ) : ?>
			<label class="alignleft">
				<span class="title"><?php _e( 'Tax Status', 'banda' ); ?></span>
				<span class="input-text-wrap">
					<select class="tax_status" name="_tax_status">
					<?php
						$options = array(
							'taxable'  => __( 'Taxable', 'banda' ),
							'shipping' => __( 'Shipping only', 'banda' ),
							'none'     => _x( 'None', 'Tax status', 'banda' )
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
						}
					?>
					</select>
				</span>
			</label>
			<br class="clear" />
			<label class="alignleft">
				<span class="title"><?php _e( 'Tax Class', 'banda' ); ?></span>
				<span class="input-text-wrap">
					<select class="tax_class" name="_tax_class">
					<?php
						$options = array(
							'' => __( 'Standard', 'banda' )
						);

						$tax_classes = WC_Tax::get_tax_classes();

						if ( ! empty( $tax_classes ) )
							foreach ( $tax_classes as $class ) {
								$options[ sanitize_title( $class ) ] = esc_html( $class );
							}

						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
						}
					?>
					</select>
				</span>
			</label>
			<br class="clear" />
		<?php endif; ?>

		<?php if ( wc_product_weight_enabled() || wc_product_dimensions_enabled() ) : ?>
		<div class="dimension_fields">

			<?php if ( wc_product_weight_enabled() ) : ?>
				<label>
					<span class="title"><?php _e( 'Weight', 'banda' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="_weight" class="text weight" placeholder="<?php echo wc_format_localized_decimal( 0 ); ?>" value="">
					</span>
				</label>
				<br class="clear" />
			<?php endif; ?>

			<?php if ( wc_product_dimensions_enabled() ) : ?>
				<div class="inline-edit-group dimensions">
					<div>
						<span class="title"><?php _e( 'L/W/H', 'banda' ); ?></span>
						<span class="input-text-wrap">
							<input type="text" name="_length" class="text wc_input_decimal length" placeholder="<?php esc_attr_e( 'Length', 'banda' ); ?>" value="">
							<input type="text" name="_width" class="text wc_input_decimal width" placeholder="<?php esc_attr_e( 'Width', 'banda' ); ?>" value="">
							<input type="text" name="_height" class="text wc_input_decimal height" placeholder="<?php esc_attr_e( 'Height', 'banda' ); ?>" value="">
						</span>
					</div>
				</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<label class="alignleft">
			<span class="title"><?php _e( 'Shipping class', 'banda' ); ?></span>
			<span class="input-text-wrap">
				<select class="shipping_class" name="_shipping_class">
					<option value="_no_shipping_class"><?php _e( 'No shipping class', 'banda' ); ?></option>
				<?php
					foreach ( $shipping_class as $key => $value ) {
						echo '<option value="' . esc_attr( $value->slug ) . '">'. $value->name .'</option>';
					}
				?>
				</select>
			</span>
		</label>
		<br class="clear" />

		<label class="alignleft">
			<span class="title"><?php _e( 'Visibility', 'banda' ); ?></span>
			<span class="input-text-wrap">
				<select class="visibility" name="_visibility">
				<?php
					$options = apply_filters( 'banda_product_visibility_options', array(
						'visible' => __( 'Catalog &amp; search', 'banda' ),
						'catalog' => __( 'Catalog', 'banda' ),
						'search'  => __( 'Search', 'banda' ),
						'hidden'  => __( 'Hidden', 'banda' )
					) );
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">'. $value .'</option>';
					}
				?>
				</select>
			</span>
		</label>
		<label class="alignleft featured">
			<input type="checkbox" name="_featured" value="1">
			<span class="checkbox-title"><?php _e( 'Featured', 'banda' ); ?></span>
		</label>
		<br class="clear" />
		<label class="alignleft">
			<span class="title"><?php _e( 'In stock?', 'banda' ); ?></span>
			<span class="input-text-wrap">
				<select class="stock_status" name="_stock_status">
				<?php
					$options = array(
						'instock'    => __( 'In stock', 'banda' ),
						'outofstock' => __( 'Out of stock', 'banda' )
					);
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) .'">'. $value .'</option>';
					}
				?>
				</select>
			</span>
		</label>

		<div class="stock_fields">

			<?php if (get_option('banda_manage_stock')=='yes') : ?>
				<label class="alignleft manage_stock">
					<input type="checkbox" name="_manage_stock" value="1">
					<span class="checkbox-title"><?php _e( 'Manage stock?', 'banda' ); ?></span>
				</label>
				<br class="clear" />
				<label class="stock_qty_field">
					<span class="title"><?php _e( 'Stock Qty', 'banda' ); ?></span>
					<span class="input-text-wrap">
						<input type="number" name="_stock" class="text stock" step="any" value="">
					</span>
				</label>
			<?php endif; ?>

		</div>

		<label class="alignleft">
			<span class="title"><?php _e( 'Backorders?', 'banda' ); ?></span>
			<span class="input-text-wrap">
				<select class="backorders" name="_backorders">
				<?php
					$options = array(
						'no'     => __( 'Do not allow', 'banda' ),
						'notify' => __( 'Allow, but notify customer', 'banda' ),
						'yes'    => __( 'Allow', 'banda' )
					);
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">'. $value .'</option>';
					}
				?>
				</select>
			</span>
		</label>

		<?php do_action( 'banda_product_quick_edit_end' ); ?>

		<input type="hidden" name="banda_quick_edit" value="1" />
		<input type="hidden" name="banda_quick_edit_nonce" value="<?php echo wp_create_nonce( 'banda_quick_edit_nonce' ); ?>" />
	</div>
</fieldset>
