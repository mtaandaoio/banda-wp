<?php
if( !defined( 'ABSPATH' ) )
    exit;

extract( $args );

global $post;
$placeholder_txt    =   isset( $placeholder ) ? $placeholder : '';
$is_multiple = isset( $multiple ) && $multiple;
$multiple = ( $is_multiple ) ? 'true' : 'false';


$category_ids = explode(',', get_post_meta( $post->ID, $id  , true )  ) ;
$json_ids   =   array();

if( $category_ids ){

    foreach( $category_ids as $category_id ){

        $cat_name   =   get_term_by( 'slug', $category_id, 'product_cat' );
        if( !empty( $cat_name ) )
            $json_ids[ $category_id ] = '#'.$cat_name->term_id.'-'.$cat_name->name;
        }
    }

?>

<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>

    <label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html($label ); ?></label>
    <div class="">
        <input type="hidden" style="width:80%;" class="ywcps_enhanced_select" id="<?php echo esc_attr( $id );?>" name="<?php echo esc_attr( $name );?>" data-placeholder="<?php echo $placeholder_txt; ?>" data-action="yith_json_search_product_categories" data-multiple="<?php echo $multiple;?>" data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>"
               value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
        <span class="desc inline"><?php echo $desc ?></span>
    </div>
</div>

