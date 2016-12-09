<tr>
	<td class="sort"></td>
	<td class="file_name"><input type="text" class="input_text" placeholder="<?php esc_attr_e( 'File Name', 'banda' ); ?>" name="_wc_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" /></td>
	<td class="file_url"><input type="text" class="input_text" placeholder="<?php esc_attr_e( "http://", 'banda' ); ?>" name="_wc_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
	<td class="file_url_choose" width="1%"><a href="#" class="button upload_file_button" data-choose="<?php esc_attr_e( 'Choose file', 'banda' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'banda' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'banda' ) ); ?></a></td>
	<td width="1%"><a href="#" class="delete"><?php _e( 'Delete', 'banda' ); ?></a></td>
</tr>