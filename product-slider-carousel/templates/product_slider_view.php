<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

$suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
$path_js = file_exists( YWCPS_ASSETS_PATH .'js/yith_product_slider_custom.js' ) ? YWCPS_ASSETS_URL .'js/yith_product_slider_custom.js' : YWCPS_ASSETS_URL .'js/yith_product_slider'.$suffix.'.js';

wp_register_script( 'yith_wc_product_slider', $path_js, array('jquery'), YWCPS_VERSION, true );

wp_enqueue_style('fontawesome');
wp_enqueue_style('owl-carousel-style');
wp_enqueue_style('yith-animate');
wp_enqueue_style('yith-product-slider-style');
wp_enqueue_script( 'owl-carousel' );
wp_enqueue_script( 'yith_wc_product_slider' );


$query_args =   array(
    'posts_per_page' =>  $posts_per_page,
    'post_type'     =>  'product',
    'post_status'   =>  'publish',

);


if ( isset( $categories ) && !empty( $categories ) ){
    $query_args['product_cat']  =   $categories;
}

if( isset( $product_type ) && !empty( $product_type ) ){

    switch ( $product_type ){

        case 'on_sale'  :
            $product_ids_on_sale    = wc_get_product_ids_on_sale();
            $product_ids_on_sale[]  = 0;
            $query_args['post__in'] = $product_ids_on_sale;
            break;
        case 'best_seller'  :
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby']  = 'meta_value_num';
            $query_args['order']    =   'DESC';
            break;
        case 'last_ins' :
            $query_args['orderby']  =  'date';
            $query_args['order']    =   'DESC';
            break;
        case 'free'  :
            $query_args['meta_query'][] = array(
                'key'     => '_price',
                'value'   => 0,
                'compare' => '=',
                'type'    => 'DECIMAL',
            );

            break;
        case 'featured' :
            $query_args['meta_query']   = array();
            $query_args['meta_query'][] = array(
                'key'   => '_featured',
                'value' => 'yes'
            );
            break;

        case 'custom_select' :
            $product_ids    =   get_post_meta( $id, '_ywcps_products', true );

            if( !empty( $product_ids ) )
            {
                $query_args['post__in'] = $product_ids ;
                unset ( $query_args['product_cat'] );
            }
            break;
    }

    $order =    strtoupper ( $order );
    switch ( $order_by ) {


        case 'date':
            if ( !isset( $query_args['orderby'] ) ) {
                $query_args['orderby']  =  'date';
                $query_args['order']    =   $order;
            }
            break;

        case 'price' :

                $query_args['meta_key'] = '_price';
                $query_args['orderby']  =  'meta_value_num';
                $query_args['order']    =  $order;

            break;

        case 'name' :
            if ( !isset( $query_args['orderby'] ) ) {
                $query_args['orderby']  =  'title';
                $query_args['order']    =   $order;
            }
            break;
    }
}


$atts['query_args'] =   $query_args;


if( $template_slider !== 'default' ){

    $atts['layouts'] =   $template_slider;
    $template_name = 'product_slider_view_custom_template.php';
}else{
    $template_name = 'product_slider_view_default.php';
}

wc_get_template( $template_name, $atts,'', YWCPS_TEMPLATE_PATH );
