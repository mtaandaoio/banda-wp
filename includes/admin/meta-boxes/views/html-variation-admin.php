<?php
/**
 * Outputs a variation
 *
 * @var int $variation_id
 * @var WP_POST $variation
 * @var array $variation_data array of variation data
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract( $variation_data );
?>
<div class="banda_variation wc-metabox closed">
	<h3>
		<a href="#" class="remove_variation delete" rel="<?php echo esc_attr( $variation_id ); ?>"><?php _e( 'Remove', 'banda' ); ?></a>
		<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'banda' ); ?>"></div>
		<div class="tips sort" data-tip="<?php esc_attr_e( 'Drag and drop, or click to set admin variation order', 'banda' ); ?>"></div>
		<strong>#<?php echo esc_html( $variation_id ); ?> </strong>
		<?php
			foreach ( $parent_data['attributes'] as $attribute ) {

				// Only deal with attributes that are variations
				if ( ! $attribute['is_variation'] || 'false' === $attribute['is_variation'] ) {
					continue;
				}

				// Get current value for variation (if set)
				$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ] : '';

				// Name will be something like attribute_pa_color
				echo '<select name="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']"><option value="">' . __( 'Any', 'banda' ) . ' ' . esc_html( wc_attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

				// Get terms for attribute taxonomy or value if its a custom attribute
				if ( $attribute['is_taxonomy'] ) {

					$post_terms = wp_get_post_terms( $parent_data['id'], $attribute['name'] );

					foreach ( $post_terms as $term ) {
						echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . esc_html( apply_filters( 'banda_variation_option_name', $term->name ) ) . '</option>';
					}

				} else {

					$options = wc_get_text_attributes( $attribute['value'] );

					foreach ( $options as $option ) {
						$selected = sanitize_title( $variation_selected_value ) === $variation_selected_value ? selected( $variation_selected_value, sanitize_title( $option ), false ) : selected( $variation_selected_value, $option, false );
						echo '<option ' . $selected . ' value="' . esc_attr( $option ) . '">' . esc_html( apply_filters( 'banda_variation_option_name', $option ) ) . '</option>';
					}

				}

				echo '</select>';
			}
		?>
		<input type="hidden" name="variable_post_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
		<input type="hidden" class="variation_menu_order" name="variation_menu_order[<?php echo $loop; ?>]" value="<?php echo isset( $menu_order ) ? absint( $menu_order ) : 0; ?>" />
	</h3>
	<div class="banda_variable_attributes wc-metabox-content" style="display: none;">
		<div class="data">
			<p class="form-row form-row-first upload_image">
				<a href="#" class="upload_image_button tips <?php if ( $_thumbnail_id > 0 ) echo 'remove'; ?>" data-tip="<?php if ( $_thumbnail_id > 0 ) { echo __( 'Remove this image', 'banda' ); } else { echo __( 'Upload an image', 'banda' ); } ?>" rel="<?php echo esc_attr( $variation_id ); ?>"><img src="<?php if ( ! empty( $image ) ) echo esc_attr( $image ); else echo esc_attr( wc_placeholder_img_src() ); ?>" /><input type="hidden" name="upload_image_id[<?php echo $loop; ?>]" class="upload_image_id" value="<?php echo esc_attr( $_thumbnail_id ); ?>" /></a>
			</p>
			<?php if ( wc_product_sku_enabled() ) : ?>
				<p class="sku form-row form-row-last">
					<label><?php _e( 'SKU', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enter a SKU for this variation or leave blank to use the parent product SKU.', 'banda' ) ); ?></label>
					<input type="text" size="5" name="variable_sku[<?php echo $loop; ?>]" value="<?php if ( isset( $_sku ) ) echo esc_attr( $_sku ); ?>" placeholder="<?php echo esc_attr( $parent_data['sku'] ); ?>" />
				</p>
			<?php else : ?>
				<input type="hidden" name="variable_sku[<?php echo $loop; ?>]" value="<?php if ( isset( $_sku ) ) echo esc_attr( $_sku ); ?>" />
			<?php endif; ?>

			<p class="form-row form-row-full options">
				<label><input type="checkbox" class="checkbox" name="variable_enabled[<?php echo $loop; ?>]" <?php checked( $variation->post_status, 'publish' ); ?> /> <?php _e( 'Enabled', 'banda' ); ?></label>

				<label><input type="checkbox" class="checkbox variable_is_downloadable" name="variable_is_downloadable[<?php echo $loop; ?>]" <?php checked( isset( $_downloadable ) ? $_downloadable : '', 'yes' ); ?> /> <?php _e( 'Downloadable', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enable this option if access is given to a downloadable file upon purchase of a product', 'banda' ) ); ?></label>

				<label><input type="checkbox" class="checkbox variable_is_virtual" name="variable_is_virtual[<?php echo $loop; ?>]" <?php checked( isset( $_virtual ) ? $_virtual : '', 'yes' ); ?> /> <?php _e( 'Virtual', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enable this option if a product is not shipped or there is no shipping cost', 'banda' ) ); ?></label>

				<?php if ( get_option( 'banda_manage_stock' ) == 'yes' ) : ?>

					<label><input type="checkbox" class="checkbox variable_manage_stock" name="variable_manage_stock[<?php echo $loop; ?>]" <?php checked( isset( $_manage_stock ) ? $_manage_stock : '', 'yes' ); ?> /> <?php _e( 'Manage stock?', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enable this option to enable stock management at variation level', 'banda' ) ); ?></label>

				<?php endif; ?>

				<?php do_action( 'banda_variation_options', $loop, $variation_data, $variation ); ?>
			</p>

			<div class="variable_pricing">
				<p class="form-row form-row-first">
					<label><?php echo __( 'Regular price', 'banda' ) . ' (' . get_banda_currency_symbol() . ')'; ?></label>
					<input type="text" size="5" name="variable_regular_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_regular_price ) ) echo esc_attr( $_regular_price ); ?>" class="wc_input_price" placeholder="<?php esc_attr_e( 'Variation price (required)', 'banda' ); ?>" />
				</p>
				<p class="form-row form-row-last">
					<label><?php echo __( 'Sale price', 'banda' ) . ' (' . get_banda_currency_symbol() . ')'; ?> <a href="#" class="sale_schedule"><?php _e( 'Schedule', 'banda' ); ?></a><a href="#" class="cancel_sale_schedule" style="display:none"><?php _e( 'Cancel schedule', 'banda' ); ?></a></label>
					<input type="text" size="5" name="variable_sale_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_sale_price ) ) echo esc_attr( $_sale_price ); ?>" class="wc_input_price" />
				</p>

				<div class="sale_price_dates_fields" style="display: none">
					<p class="form-row form-row-first">
						<label><?php _e( 'Sale start date', 'banda' ); ?></label>
						<input type="text" class="sale_price_dates_from" name="variable_sale_price_dates_from[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : ''; ?>" placeholder="<?php echo esc_attr_x( 'From&hellip;', 'placeholder', 'banda' ) ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
					</p>
					<p class="form-row form-row-last">
						<label><?php _e( 'Sale end date', 'banda' ); ?></label>
						<input type="text" class="sale_price_dates_to" name="variable_sale_price_dates_to[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : ''; ?>" placeholder="<?php echo esc_attr_x('To&hellip;', 'placeholder', 'banda') ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
					</p>
				</div>

				<?php
					/**
					 * banda_variation_options_pricing action.
					 *
					 * @since 2.5.0
					 *
					 * @param int     $loop
					 * @param array   $variation_data
					 * @param WP_Post $variation
					 */
					do_action( 'banda_variation_options_pricing', $loop, $variation_data, $variation );
				?>
			</div>

			<?php if ( 'yes' == get_option( 'banda_manage_stock' ) ) : ?>
				<div class="show_if_variation_manage_stock" style="display: none;">
					<p class="form-row form-row-first">
						<label><?php _e( 'Stock quantity', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enter a quantity to enable stock management at variation level, or leave blank to use the parent product\'s options.', 'banda' ) ); ?></label>
						<input type="number" size="5" name="variable_stock[<?php echo $loop; ?>]" value="<?php if ( isset( $_stock ) ) echo esc_attr( wc_stock_amount( $_stock ) ); ?>" step="any" />
					</p>
					<p class="form-row form-row-last">
						<label><?php _e( 'Allow backorders?', 'banda' ); ?></label>
						<select name="variable_backorders[<?php echo $loop; ?>]">
							<?php
								foreach ( $parent_data['backorder_options'] as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key === $_backorders, true, false ) . '>' . esc_html( $value ) . '</option>';
								}
							?>
						</select>
					</p>

					<?php
						/**
						 * banda_variation_options_inventory action.
						 *
						 * @since 2.5.0
						 *
						 * @param int     $loop
						 * @param array   $variation_data
						 * @param WP_Post $variation
						 */
						do_action( 'banda_variation_options_inventory', $loop, $variation_data, $variation );
					?>
				</div>
			<?php endif; ?>

			<div class="">
				<p class="form-row form-row-full">
					<label><?php _e( 'Stock status', 'banda' ); ?> <?php echo wc_help_tip( __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'banda' ) ); ?></label>
					<select name="variable_stock_status[<?php echo $loop; ?>]">
						<?php
							foreach ( $parent_data['stock_status_options'] as $key => $value ) {
								echo '<option value="' . esc_attr( $key === $_stock_status ? '' : $key ) . '" ' . selected( $key === $_stock_status, true, false ) . '>' . esc_html( $value ) . '</option>';
							}
						?>
					</select>
				</p>
			</div>

			<?php if ( wc_product_weight_enabled() || wc_product_dimensions_enabled() ) : ?>
				<div>
					<?php if ( wc_product_weight_enabled() ) : ?>
						<p class="form-row hide_if_variation_virtual form-row-first">
							<label><?php echo __( 'Weight', 'banda' ) . ' (' . esc_html( get_option( 'banda_weight_unit' ) ) . ')'; ?> <?php echo wc_help_tip( __( 'Enter a weight for this variation or leave blank to use the parent product weight.', 'banda' ) ); ?></a></label>
							<input type="text" size="5" name="variable_weight[<?php echo $loop; ?>]" value="<?php if ( isset( $_weight ) ) echo esc_attr( $_weight ); ?>" placeholder="<?php echo esc_attr( $parent_data['weight'] ); ?>" class="wc_input_decimal" />
						</p>
					<?php else : ?>
						<p>&nbsp;</p>
					<?php endif; ?>
					<?php if ( wc_product_dimensions_enabled() ) : ?>
						<p class="form-row dimensions_field hide_if_variation_virtual form-row-last">
							<label for="product_length"><?php echo __( 'Dimensions (L&times;W&times;H)', 'banda' ) . ' (' . esc_html( get_option( 'banda_dimension_unit' ) ) . ')'; ?></label>
							<input id="product_length" class="input-text wc_input_decimal" size="6" type="text" name="variable_length[<?php echo $loop; ?>]" value="<?php if ( isset( $_length ) ) echo esc_attr( $_length ); ?>" placeholder="<?php echo esc_attr( $parent_data['length'] ); ?>" />
							<input class="input-text wc_input_decimal" size="6" type="text" name="variable_width[<?php echo $loop; ?>]" value="<?php if ( isset( $_width ) ) echo esc_attr( $_width ); ?>" placeholder="<?php echo esc_attr( $parent_data['width'] ); ?>" />
							<input class="input-text wc_input_decimal last" size="6" type="text" name="variable_height[<?php echo $loop; ?>]" value="<?php if ( isset( $_height ) ) echo esc_attr( $_height ); ?>" placeholder="<?php echo esc_attr( $parent_data['height'] ); ?>" />
						</p>
					<?php else : ?>
						<p>&nbsp;</p>
					<?php endif; ?>

					<?php
						/**
						 * banda_variation_options_dimensions action.
						 *
						 * @since 2.5.0
						 *
						 * @param int     $loop
						 * @param array   $variation_data
						 * @param WP_Post $variation
						 */
						do_action( 'banda_variation_options_dimensions', $loop, $variation_data, $variation );
					?>
				</div>
			<?php endif; ?>
			<div>
				<p class="form-row hide_if_variation_virtual form-row-full"><label><?php _e( 'Shipping class', 'banda' ); ?></label> <?php
					$args = array(
						'taxonomy' 			=> 'product_shipping_class',
						'hide_empty'		=> 0,
						'show_option_none' 	=> __( 'Same as parent', 'banda' ),
						'name' 				=> 'variable_shipping_class[' . $loop . ']',
						'id'				=> '',
						'selected'			=> isset( $shipping_class ) ? esc_attr( $shipping_class ) : '',
						'echo'				=> 0
					);

					echo wp_dropdown_categories( $args );
				?></p>

				<?php if ( wc_tax_enabled() ) : ?>
					<p class="form-row form-row-full">
						<label><?php _e( 'Tax class', 'banda' ); ?></label>
						<select name="variable_tax_class[<?php echo $loop; ?>]">
							<option value="parent" <?php selected( is_null( $_tax_class ), true ); ?>><?php _e( 'Same as parent', 'banda' ); ?></option>
							<?php
							foreach ( $parent_data['tax_class_options'] as $key => $value )
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key === $_tax_class, true, false ) . '>' . esc_html( $value ) . '</option>';
						?></select>
					</p>

					<?php
						/**
						 * banda_variation_options_tax action.
						 *
						 * @since 2.5.0
						 *
						 * @param int     $loop
						 * @param array   $variation_data
						 * @param WP_Post $variation
						 */
						do_action( 'banda_variation_options_tax', $loop, $variation_data, $variation );
					?>
				<?php endif; ?>
			</div>
			<div>
				<p class="form-row form-row-full">
					<label><?php _e( 'Variation description', 'banda' ); ?></label>
					<textarea name="variable_description[<?php echo $loop; ?>]" rows="3" style="width:100%;"><?php echo isset( $variation_data['_variation_description'] ) ? esc_textarea( $variation_data['_variation_description'] ) : ''; ?></textarea>
				</p>
			</div>
			<div class="show_if_variation_downloadable" style="display: none;">
				<div class="form-row form-row-full downloadable_files">
					<label><?php _e( 'Downloadable files', 'banda' ); ?></label>
					<table class="widefat">
						<thead>
							<div>
								<th><?php _e( 'Name', 'banda' ); ?> <?php echo wc_help_tip( __( 'This is the name of the download shown to the customer.', 'banda' ) ); ?></th>
								<th colspan="2"><?php _e( 'File URL', 'banda' ); ?> <?php echo wc_help_tip( __( 'This is the URL or absolute path to the file which customers will get access to. URLs entered here should already be encoded.', 'banda' ) ); ?></th>
								<th>&nbsp;</th>
							</div>
						</thead>
						<tbody>
							<?php
							if ( $_downloadable_files ) {
								foreach ( $_downloadable_files as $key => $file ) {
									if ( ! is_array( $file ) ) {
										$file = array(
											'file' => $file,
											'name' => ''
										);
									}
									include( 'html-product-variation-download.php' );
								}
							}
							?>
						</tbody>
						<tfoot>
							<div>
								<th colspan="4">
									<a href="#" class="button insert" data-row="<?php
										$file = array(
											'file' => '',
											'name' => ''
										);
										ob_start();
										include( 'html-product-variation-download.php' );
										echo esc_attr( ob_get_clean() );
									?>"><?php _e( 'Add File', 'banda' ); ?></a>
								</th>
							</div>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="show_if_variation_downloadable" style="display: none;">
				<p class="form-row form-row-first">
					<label><?php _e( 'Download limit', 'banda' ); ?> <?php echo wc_help_tip( __( 'Leave blank for unlimited re-downloads.', 'banda' ) ); ?></a></label>
					<input type="number" size="5" name="variable_download_limit[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_limit ) ) echo esc_attr( $_download_limit ); ?>" placeholder="<?php esc_attr_e( 'Unlimited', 'banda' ); ?>" step="1" min="0" />
				</p>
				<p class="form-row form-row-last">
					<label><?php _e( 'Download expiry', 'banda' ); ?> <?php echo wc_help_tip( __( 'Enter the number of days before a download link expires, or leave blank.', 'banda' ) ); ?></a></label>
					<input type="number" size="5" name="variable_download_expiry[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_expiry ) ) echo esc_attr( $_download_expiry ); ?>" placeholder="<?php esc_attr_e( 'Unlimited', 'banda' ); ?>" step="1" min="0" />
				</p>

				<?php
					/**
					 * banda_variation_options_download action.
					 *
					 * @since 2.5.0
					 *
					 * @param int     $loop
					 * @param array   $variation_data
					 * @param WP_Post $variation
					 */
					do_action( 'banda_variation_options_download', $loop, $variation_data, $variation );
				?>
			</div>
			<?php do_action( 'banda_product_after_variable_attributes', $loop, $variation_data, $variation ); ?>
		</div>
	</div>
</div>
